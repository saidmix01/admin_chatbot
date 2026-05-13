<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Flow Builder — Visual conversation flow editor for Wapi
 *
 * Combines questions, answers, chat_questions into a single
 * intuitive tree/mindmap interface.
 */
class Flow extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('General_Model/General_Model', 'General_Model');
        $this->load->model('Questions/Question_model', 'Question_model');
        $this->load->model('Chat/Chat_model', 'Chat_model');
        $this->load->model('List_manage/List_model', 'List_model');
        $this->load->helper('general_helper');
    }

    // ──────────────────────────────────────────────
    //  MAIN VIEW
    // ──────────────────────────────────────────────

    public function index()
    {
        try {
            if (!validate_session()) throw new Exception("The unauthenticated user", 1);
            $user_content["us_id"] = $this->session->userdata('us_id');
            $user_data = get_user_content($user_content);
            if ($user_data["status"] == false) throw new Exception($user_data["message"], 1);

            // Auto-register menu entry if not exists
            $this->_ensure_menu_entry();

            $data_header["user_data"] = $user_data["data"];
            $data_header["menus"] = get_user_menus(array("us_id" => $this->session->userdata('us_id')))["data"];

            $data_view['module_name'] = 'Flow Builder';

            $data_footer["scripts"] = [
                "js/flow/flow.js?cv=" . control_version()
            ];

            // Cargar listas para el selector
            $this->List_model->data = [];
            $lists = $this->List_model->get_lists();
            $data_view['lists'] = $lists['status'] ? $lists['data'] : [];

            $this->load->view('includes/header', $data_header);
            $this->load->view('flow/flow_view', $data_view);
            $this->load->view('includes/footer', $data_footer);
        } catch (\Throwable $th) {
            $this->load->view('error_pages/500');
        }
    }

    // ──────────────────────────────────────────────
    //  API: QUESTIONS (legacy questions table — Q&A flow)
    // ──────────────────────────────────────────────

    /**
     * GET /flow/api_questions?parent=0
     * Returns tree-structured questions
     */
    public function api_questions()
    {
        $response = ["status" => false, "data" => [], "message" => ""];
        try {
            if (!validate_session()) throw new Exception("Unauthenticated", 1);

            $parent = $this->input->get('parent') ?? 0;
            $us_id = $this->session->userdata('us_id');

            $sql = "SELECT q.*, 
                    (SELECT COUNT(*) FROM answer a WHERE a.que_id = q.que_id) as answer_count,
                    (SELECT COUNT(*) FROM questions c WHERE c.que_parent = q.que_id) as child_count
                    FROM questions q 
                    WHERE q.us_id = ? AND q.que_parent = ?
                    ORDER BY q.que_order ASC, q.que_id ASC";
            $query = $this->db->query($sql, [(int)$us_id, (int)$parent]);

            $items = $query->result();
            foreach ($items as &$item) {
                $item->children = [];
                $item->answers = [];
            }

            $response["status"] = true;
            $response["data"] = $items;
        } catch (\Throwable $th) {
            $response["message"] = $th->getMessage();
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * GET /flow/api_question_tree?root=0
     * Full tree recursive
     */
    public function api_question_tree()
    {
        $response = ["status" => false, "data" => [], "message" => ""];
        try {
            if (!validate_session()) throw new Exception("Unauthenticated", 1);

            $root = $this->input->get('root') ?? 0;
            $us_id = $this->session->userdata('us_id');

            $response["data"] = $this->_build_question_tree((int)$root, (int)$us_id);
            $response["status"] = true;
        } catch (\Throwable $th) {
            $response["message"] = $th->getMessage();
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    private function _build_question_tree($parent, $us_id)
    {
        $sql = "SELECT * FROM questions 
                WHERE us_id = ? AND que_parent = ?
                ORDER BY que_order ASC, que_id ASC";
        $query = $this->db->query($sql, [$us_id, $parent]);
        $items = $query->result();

        $tree = [];
        foreach ($items as $item) {
            $answers = [];
            $ans_query = $this->db->query("SELECT * FROM answer WHERE que_id = ? ORDER BY ans_order ASC", [$item->que_id]);
            $answers = $ans_query->result();

            $tree[] = [
                'id' => (int)$item->que_id,
                'parent' => (int)$item->que_parent,
                'order' => (int)$item->que_order,
                'question' => $item->que_question,
                'answers' => $answers,
                'children' => $this->_build_question_tree($item->que_id, $us_id)
            ];
        }
        return $tree;
    }

    /**
     * POST /flow/save_question
     * Create or update a question
     */
    public function save_question()
    {
        $response = ["status" => false, "message" => ""];
        try {
            if (!validate_session()) throw new Exception("Unauthenticated", 1);
            $input = json_decode(file_get_contents("php://input"), true);
            if (empty($input)) throw new Exception("No data", 1);

            $us_id = $this->session->userdata('us_id');
            $que_id = $input['que_id'] ?? null;
            $data = [
                'que_question' => $input['que_question'] ?? '',
                'que_order' => $input['que_order'] ?? 1,
                'que_parent' => $input['que_parent'] ?? 0,
                'us_id' => $us_id
            ];

            if ($que_id) {
                $this->General_Model->table_name = "questions";
                $this->General_Model->data = $data;
                $this->General_Model->where = ["que_id" => $que_id];
                $res = $this->General_Model->update();
                if (!$res["status"]) throw new Exception($res["message"], 1);
                $response["data"] = ["que_id" => $que_id];
            } else {
                $this->General_Model->table_name = "questions";
                $this->General_Model->data = $data;
                $res = $this->General_Model->insert();
                if (!$res["status"]) throw new Exception($res["message"], 1);
                $response["data"] = ["que_id" => $res["data"]];
            }

            $response["status"] = true;
            $response["message"] = $que_id ? "Updated" : "Created";
        } catch (\Throwable $th) {
            $response["message"] = $th->getMessage();
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * POST /flow/delete_question
     */
    public function delete_question()
    {
        $response = ["status" => false, "message" => ""];
        try {
            if (!validate_session()) throw new Exception("Unauthenticated", 1);
            $input = json_decode(file_get_contents("php://input"), true);
            $que_id = $input['que_id'] ?? null;
            if (!$que_id) throw new Exception("que_id required", 1);

            $this->db->delete("answer", ["que_id" => $que_id]);
            $this->db->delete("questions", ["que_id" => $que_id]);

            $response["status"] = true;
            $response["message"] = "Deleted";
        } catch (\Throwable $th) {
            $response["message"] = $th->getMessage();
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * POST /flow/reorder_questions
     */
    public function reorder_questions()
    {
        $response = ["status" => false, "message" => ""];
        try {
            if (!validate_session()) throw new Exception("Unauthenticated", 1);
            $input = json_decode(file_get_contents("php://input"), true);
            $items = $input['items'] ?? [];

            foreach ($items as $item) {
                $this->db->where("que_id", $item['que_id']);
                $this->db->update("questions", [
                    "que_order" => $item['que_order'],
                    "que_parent" => $item['que_parent'] ?? 0
                ]);
            }

            $response["status"] = true;
        } catch (\Throwable $th) {
            $response["message"] = $th->getMessage();
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    // ──────────────────────────────────────────────
    //  API: ANSWERS
    // ──────────────────────────────────────────────

    /**
     * GET /flow/api_answers?que_id=X
     */
    public function api_answers()
    {
        $response = ["status" => false, "data" => [], "message" => ""];
        try {
            if (!validate_session()) throw new Exception("Unauthenticated", 1);
            $que_id = $this->input->get('que_id');
            if (!$que_id) throw new Exception("que_id required", 1);

            $query = $this->db->query(
                "SELECT * FROM answer WHERE que_id = ? ORDER BY ans_order ASC",
                [(int)$que_id]
            );
            $response["status"] = true;
            $response["data"] = $query->result();
        } catch (\Throwable $th) {
            $response["message"] = $th->getMessage();
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * POST /flow/save_answer
     */
    public function save_answer()
    {
        $response = ["status" => false, "message" => ""];
        try {
            if (!validate_session()) throw new Exception("Unauthenticated", 1);
            $input = json_decode(file_get_contents("php://input"), true);
            if (empty($input)) throw new Exception("No data", 1);

            $us_id = $this->session->userdata('us_id');
            $ans_id = $input['ans_id'] ?? null;

            if (empty($input['ans_order'])) {
                // Get next order
                $ord = $this->db->query(
                    "SELECT COALESCE(MAX(ans_order), 0) + 1 AS next FROM answer WHERE que_id = ?",
                    [$input['que_id']]
                )->row()->next ?? 1;
                $input['ans_order'] = (int)$ord;
            }
            $input['us_id'] = $us_id;
            unset($input['ans_id']);

            if ($ans_id) {
                $this->General_Model->table_name = "answer";
                $this->General_Model->data = $input;
                $this->General_Model->where = ["ans_id" => $ans_id];
                $res = $this->General_Model->update();
                if (!$res["status"]) throw new Exception($res["message"], 1);
            } else {
                $this->General_Model->table_name = "answer";
                $this->General_Model->data = $input;
                $res = $this->General_Model->insert();
                if (!$res["status"]) throw new Exception($res["message"], 1);
            }

            $response["status"] = true;
        } catch (\Throwable $th) {
            $response["message"] = $th->getMessage();
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * POST /flow/delete_answer
     */
    public function delete_answer()
    {
        $response = ["status" => false, "message" => ""];
        try {
            if (!validate_session()) throw new Exception("Unauthenticated", 1);
            $input = json_decode(file_get_contents("php://input"), true);
            $ans_id = $input['ans_id'] ?? null;
            if (!$ans_id) throw new Exception("ans_id required", 1);
            $this->db->delete("answer", ["ans_id" => $ans_id]);
            $response["status"] = true;
        } catch (\Throwable $th) {
            $response["message"] = $th->getMessage();
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    // ──────────────────────────────────────────────
    //  API: CHAT QUESTIONS (tree builder)
    // ──────────────────────────────────────────────

    /**
     * GET /flow/api_chat_questions?parent=0
     */
    public function api_chat_questions()
    {
        $response = ["status" => false, "data" => [], "message" => ""];
        try {
            if (!validate_session()) throw new Exception("Unauthenticated", 1);
            $parent = $this->input->get('parent') ?? 0;
            $us_id = $this->session->userdata('us_id');

            $sql = "SELECT cq.*, l.lis_name
                    FROM chat_questions cq
                    LEFT JOIN lists l ON l.lis_id = cq.lis_id
                    WHERE cq.us_id = ? AND cq.chq_parent = ?
                    ORDER BY cq.chq_order ASC, cq.chq_id ASC";
            $query = $this->db->query($sql, [(int)$us_id, (int)$parent]);

            $items = $query->result();
            foreach ($items as &$item) {
                $child_q = $this->db->query(
                    "SELECT COUNT(*) as cnt FROM chat_questions WHERE chq_parent = ?",
                    [$item->chq_id]
                );
                $item->child_count = (int)$child_q->row()->cnt;
                $item->children = [];
            }

            $response["status"] = true;
            $response["data"] = $items;
        } catch (\Throwable $th) {
            $response["message"] = $th->getMessage();
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * GET /flow/api_chat_tree
     * Full recursive tree of chat_questions
     */
    public function api_chat_tree()
    {
        $response = ["status" => false, "data" => [], "message" => ""];
        try {
            if (!validate_session()) throw new Exception("Unauthenticated", 1);
            $us_id = $this->session->userdata('us_id');
            $response["data"] = $this->_build_chat_tree(0, (int)$us_id);
            $response["status"] = true;
        } catch (\Throwable $th) {
            $response["message"] = $th->getMessage();
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    private function _build_chat_tree($parent, $us_id)
    {
        $sql = "SELECT cq.*, l.lis_name
                FROM chat_questions cq
                LEFT JOIN lists l ON l.lis_id = cq.lis_id
                WHERE cq.us_id = ? AND cq.chq_parent = ?
                ORDER BY cq.chq_order ASC, cq.chq_id ASC";
        $items = $this->db->query($sql, [$us_id, $parent])->result();
        $tree = [];
        foreach ($items as $item) {
            $tree[] = [
                'id' => (int)$item->chq_id,
                'parent' => (int)$item->chq_parent,
                'order' => (int)$item->chq_order,
                'text' => $item->chq_text,
                'status' => (int)$item->chq_status,
                'type' => (int)$item->chq_type,
                'list_id' => (int)$item->lis_id,
                'list_name' => $item->lis_name ?? '',
                'children' => $this->_build_chat_tree($item->chq_id, $us_id)
            ];
        }
        return $tree;
    }

    /**
     * POST /flow/save_chat_question
     */
    public function save_chat_question()
    {
        $response = ["status" => false, "message" => ""];
        try {
            if (!validate_session()) throw new Exception("Unauthenticated", 1);
            $input = json_decode(file_get_contents("php://input"), true);
            if (empty($input)) throw new Exception("No data", 1);

            $us_id = $this->session->userdata('us_id');
            $chq_id = $input['chq_id'] ?? null;
            $data = [
                'chq_text' => $input['chq_text'] ?? '',
                'chq_order' => $input['chq_order'] ?? 1,
                'chq_parent' => $input['chq_parent'] ?? 0,
                'chq_status' => $input['chq_status'] ?? 1,
                'chq_type' => $input['chq_type'] ?? 1,
                'chq_response' => $input['chq_response'] ?? null,
                'lis_id' => $input['lis_id'] ?? null,
                'us_id' => $us_id
            ];

            if ($chq_id) {
                $this->General_Model->table_name = "chat_questions";
                $this->General_Model->data = $data;
                $this->General_Model->where = ["chq_id" => $chq_id];
                $res = $this->General_Model->update();
                if (!$res["status"]) throw new Exception($res["message"], 1);
                $response["data"] = ["chq_id" => $chq_id];
            } else {
                $this->General_Model->table_name = "chat_questions";
                $this->General_Model->data = $data;
                $res = $this->General_Model->insert();
                if (!$res["status"]) throw new Exception($res["message"], 1);
                $response["data"] = ["chq_id" => $res["data"]];
            }

            $response["status"] = true;
        } catch (\Throwable $th) {
            $response["message"] = $th->getMessage();
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * POST /flow/delete_chat_question
     */
    public function delete_chat_question()
    {
        $response = ["status" => false, "message" => ""];
        try {
            if (!validate_session()) throw new Exception("Unauthenticated", 1);
            $input = json_decode(file_get_contents("php://input"), true);
            $chq_id = $input['chq_id'] ?? null;
            if (!$chq_id) throw new Exception("chq_id required", 1);

            // Delete all children first
            $this->db->query(
                "DELETE FROM chat_questions WHERE chq_id IN (
                    WITH RECURSIVE cte AS (
                        SELECT chq_id FROM chat_questions WHERE chq_parent = ?
                        UNION ALL
                        SELECT cq.chq_id FROM chat_questions cq JOIN cte ON cq.chq_parent = cte.chq_id
                    ) SELECT chq_id FROM cte
                )",
                [(int)$chq_id]
            );
            $this->db->delete("chat_questions", ["chq_id" => $chq_id]);

            $response["status"] = true;
        } catch (\Throwable $th) {
            $response["message"] = $th->getMessage();
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * POST /flow/reorder_chat
     */
    public function reorder_chat()
    {
        $response = ["status" => false, "message" => ""];
        try {
            if (!validate_session()) throw new Exception("Unauthenticated", 1);
            $input = json_decode(file_get_contents("php://input"), true);
            $items = $input['items'] ?? [];

            foreach ($items as $item) {
                $this->db->where("chq_id", $item['chq_id']);
                $this->db->update("chat_questions", [
                    "chq_order" => $item['chq_order'],
                    "chq_parent" => $item['chq_parent'] ?? 0
                ]);
            }

            $response["status"] = true;
        } catch (\Throwable $th) {
            $response["message"] = $th->getMessage();
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    // ──────────────────────────────────────────────
    //  AUTO-REGISTER MENU
    // ──────────────────────────────────────────────

    /**
     * Ensure the Flow Builder menu entry exists in the database.
     * Runs once on first access to the flow builder.
     */
    private function _ensure_menu_entry()
    {
        try {
            $exists = $this->db->query("SELECT men_id FROM menus WHERE men_url = 'flow'")->num_rows();
            if ($exists > 0) return;

            $this->db->insert("menus", [
                "men_description" => "Flow Builder",
                "men_url" => "flow",
                "men_icon" => "feather icon-git-branch",
                "men_status" => 1
            ]);
            $men_id = $this->db->insert_id();

            // Assign to admin profile (pro_id=1) if exists
            $profile_exists = $this->db->query("SELECT pro_id FROM profiles WHERE pro_id = 1")->num_rows();
            if ($profile_exists > 0) {
                $already = $this->db->query(
                    "SELECT mpr_id FROM menu_profile WHERE men_id = ? AND pro_id = 1",
                    [$men_id]
                )->num_rows();
                if ($already === 0) {
                    $this->db->insert("menu_profile", ["men_id" => $men_id, "pro_id" => 1]);
                }
            }
        } catch (\Throwable $th) {
            // Silent — don't block page load for a menu registration
        }
    }
}
