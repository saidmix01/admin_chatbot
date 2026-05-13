<?php
class FlowEngine {
    private $db;
    private $vars = [];

    public function __construct($db) {
        $this->db = $db;
    }

    public function renderTemplate($text, $vars) {
        $search = []; $replace = [];
        foreach ($vars as $k => $v) {
            $search[] = '{{' . $k . '}}';
            $replace[] = $v;
        }
        return str_replace($search, $replace, $text);
    }

    public function handleIncoming($userPhone, $message) {
        // Normalize message
        $msg = strtolower(trim($message));
        
        // Check active session first
        $session = $this->db->query(
            "SELECT s.*, fv.id as version_id, fv.flow_id 
             FROM flow_sessions s 
             JOIN flow_versions fv ON s.flow_version_id = fv.id 
             WHERE s.user_phone = ? AND s.status = 'active' AND s.expires_at > NOW() 
             ORDER BY s.created_at DESC LIMIT 1",
            [$userPhone]
        )->row();

        if ($session) {
            return $this->continueFlow($session, $message);
        }

        // No session: find trigger
        $trigger = $this->db->query(
            "SELECT ft.*, f.id as flow_id, fv.id as version_id
             FROM flow_triggers ft
             JOIN flows f ON ft.flow_id = f.id
             JOIN flow_versions fv ON fv.flow_id = f.id AND fv.status = 'published'
             WHERE ft.trigger_type = 'keyword' AND ft.trigger_value = ?
             LIMIT 1",
            [$msg]
        )->row();

        if (!$trigger) return null; // No trigger matched

        return $this->startFlow($trigger->flow_id, $trigger->version_id, $userPhone);
    }

    public function startFlow($flowId, $versionId, $userPhone) {
        // Get start node
        $startNode = $this->db->where('flow_version_id', $versionId)->where('node_key', 'start')->get('flow_nodes')->row();
        if (!$startNode) return ['error' => 'No start node'];

        // Create session
        $this->db->insert('flow_sessions', [
            'flow_version_id' => $versionId,
            'user_phone' => $userPhone,
            'current_node_key' => 'start',
            'variables_json' => '{}',
            'status' => 'active',
            'expires_at' => date('Y-m-d H:i:s', time() + 1800)
        ]);
        $sessionId = $this->db->insert_id();

        return $this->executeNode($versionId, 'start', $sessionId, null);
    }

    public function continueFlow($session, $message) {
        return $this->executeNode($session->flow_version_id, $session->current_node_key, $session->id, $message);
    }

    public function executeNode($versionId, $nodeKey, $sessionId, $userResponse) {
        $node = $this->db->where('flow_version_id', $versionId)->where('node_key', $nodeKey)->get('flow_nodes')->row();
        if (!$node) return ['error' => "Node $nodeKey not found"];

        $payload = json_decode($node->payload_json, true) ?: [];
        $session = $this->db->where('id', $sessionId)->get('flow_sessions')->row();
        $vars = json_decode($session->variables_json ?? '{}', true) ?: [];
        $this->vars = $vars;

        switch ($node->type) {
            case 'message': {
                $text = $this->renderTemplate($payload['text'] ?? '', $vars);
                $nextKey = $this->getNextNode($versionId, $nodeKey);
                if ($nextKey) {
                    $this->updateSession($sessionId, $nextKey, $vars);
                    return $this->executeNode($versionId, $nextKey, $sessionId, null);
                }
                return ['message' => $text];
            }

            case 'question': {
                if ($userResponse === null) {
                    $text = $this->renderTemplate($payload['text'] ?? '', $vars);
                    $this->updateSession($sessionId, $nodeKey, $vars);
                    return ['message' => $text, 'awaiting' => true];
                }
                $saveTo = $payload['save_to'] ?? 'respuesta';
                $vars[$saveTo] = $userResponse;
                $this->updateSession($sessionId, $nodeKey, $vars);
                $nextKey = $this->getNextNode($versionId, $nodeKey);
                if ($nextKey) {
                    return $this->executeNode($versionId, $nextKey, $sessionId, null);
                }
                return ['message' => 'OK', 'awaiting' => true];
            }

            case 'choice': {
                if ($userResponse === null) {
                    $text = $this->renderTemplate($payload['text'] ?? '', $vars);
                    $options = $payload['options'] ?? [];
                    $lines = [$text];
                    foreach ($options as $opt) {
                        $lines[] = $opt['value'] . ') ' . $opt['label'];
                    }
                    $this->updateSession($sessionId, $nodeKey, $vars);
                    return ['message' => implode("\n", $lines), 'awaiting' => true];
                }
                $selected = intval($userResponse);
                $saveTo = $payload['save_to'] ?? 'seleccion';
                $vars[$saveTo] = $selected;
                $this->updateSession($sessionId, $nodeKey, $vars);
                $nextKey = $this->getNextNode($versionId, $nodeKey);
                if ($nextKey) return $this->executeNode($versionId, $nextKey, $sessionId, null);
                return ['message' => 'OK', 'awaiting' => true];
            }

            case 'condition': {
                $cond = $payload['if'] ?? [];
                $varVal = $vars[$cond['var'] ?? ''] ?? '';
                $matches = false;
                switch ($cond['op'] ?? 'equals') {
                    case 'equals': $matches = ($varVal == ($cond['value'] ?? '')); break;
                    case 'not_equals': $matches = ($varVal != ($cond['value'] ?? '')); break;
                    case 'gt': $matches = (floatval($varVal) > floatval($cond['value'] ?? 0)); break;
                    case 'lt': $matches = (floatval($varVal) < floatval($cond['value'] ?? 0)); break;
                }
                $nextKey = $matches ? ($payload['true_to'] ?? $this->getNextNode($versionId, $nodeKey)) : ($payload['false_to'] ?? $this->getNextNode($versionId, $nodeKey));
                if ($nextKey) return $this->executeNode($versionId, $nextKey, $sessionId, null);
                return ['message' => 'Fin condicion'];
            }

            case 'goto': {
                $nextKey = $payload['to'] ?? $this->getNextNode($versionId, $nodeKey);
                if ($nextKey) return $this->executeNode($versionId, $nextKey, $sessionId, null);
                return ['message' => 'Goto sin destino'];
            }

            case 'action_webhook': {
                $url = $payload['url'] ?? '';
                $method = strtoupper($payload['method'] ?? 'GET');
                $body = $payload['body'] ?? [];
                $saveTo = $payload['save_to'] ?? 'webhook_result';
                $bodyStr = json_encode($this->renderTemplateValues($body, $vars));
                try {
                    $ch = curl_init($url);
                    curl_setopt_array($ch, [
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_TIMEOUT => ($payload['timeout_ms'] ?? 5000) / 1000,
                        CURLOPT_CUSTOMREQUEST => $method,
                        CURLOPT_HTTPHEADER => ['Content-Type: application/json']
                    ]);
                    if ($method === 'POST') curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyStr);
                    $result = curl_exec($ch);
                    curl_close($ch);
                    $vars[$saveTo] = json_decode($result, true) ?: $result;
                } catch (\Exception $e) {
                    if (!empty($payload['on_error_to'])) {
                        return $this->executeNode($versionId, $payload['on_error_to'], $sessionId, null);
                    }
                }
                $this->updateSession($sessionId, $nodeKey, $vars);
                $nextKey = $this->getNextNode($versionId, $nodeKey);
                if ($nextKey) return $this->executeNode($versionId, $nextKey, $sessionId, null);
                return ['message' => 'Webhook ejecutado'];
            }

            case 'end': {
                $this->db->where('id', $sessionId)->update('flow_sessions', ['status' => 'ended', 'updated_at' => date('Y-m-d H:i:s')]);
                return ['message' => '', 'ended' => true];
            }
        }
        return ['message' => 'Tipo de nodo desconocido: ' . $node->type];
    }

    private function getNextNode($versionId, $fromKey) {
        $edge = $this->db->where('flow_version_id', $versionId)->where('from_node_key', $fromKey)->order_by('id', 'ASC')->get('flow_edges')->row();
        return $edge ? $edge->to_node_key : null;
    }

    private function updateSession($sessionId, $currentNodeKey, $vars) {
        $this->db->where('id', $sessionId)->update('flow_sessions', [
            'current_node_key' => $currentNodeKey,
            'variables_json' => json_encode($vars),
            'updated_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', time() + 1800)
        ]);
    }

    private function renderTemplateValues($data, $vars) {
        if (is_string($data)) return $this->renderTemplate($data, $vars);
        if (is_array($data)) {
            $result = [];
            foreach ($data as $k => $v) $result[$k] = $this->renderTemplateValues($v, $vars);
            return $result;
        }
        return $data;
    }
}
