<?php
class Webhook extends CI_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function incoming() {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        $from = $input['from'] ?? $input['user_phone'] ?? '';
        $text = $input['text'] ?? $input['message'] ?? '';

        if (!$from || !$text) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'from y text requeridos']);
            return;
        }

        $normalized = preg_replace('/\s+/u', ' ', trim((string)$text));
        $normalizedLower = mb_strtolower($normalized, 'UTF-8');
        if ($normalizedLower === mb_strtolower('Quiero el plan Inicial de Wapi', 'UTF-8')) {
            $reply = "Plan Inicial de Wapi (ideal para empezar):\n"
                . "• Bot de WhatsApp con respuestas automáticas básicas\n"
                . "• Catálogo de productos/servicios + landing/tienda pública\n"
                . "• Mensajes configurables: bienvenida, menú, fuera de horario y despedida\n"
                . "• Gestión desde el panel y soporte para la configuración\n\n"
                . "Si quieres, dime cuántos números necesitas y te recomiendo el plan correcto.";
            echo json_encode(['status' => true, 'action' => 'demo', 'reply' => $reply]);
            return;
        }

        $this->load->library('FlowEngine');
        $engine = new FlowEngine($this->db);
        $result = $engine->handleIncoming($from, $text);

        if ($result === null) {
            echo json_encode(['status' => true, 'message' => 'No trigger matched', 'action' => 'none']);
            return;
        }

        $response = ['status' => true, 'action' => 'flow'];
        if (!empty($result['message'])) $response['reply'] = $result['message'];
        if (!empty($result['awaiting'])) $response['awaiting'] = true;
        if (!empty($result['ended'])) $response['ended'] = true;
        if (!empty($result['error'])) { $response['error'] = $result['error']; $response['status'] = false; }

        echo json_encode($response);
    }
}
