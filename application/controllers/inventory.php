<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model(['data_model', 'config_model', 'inventory_model']);
        $this->load->helper('url');
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        $config = $this->config_model->getConfig();
        redirect($config['base_url'] . 'inventory/all_item');
    }

    public function inv() {
        $data['onload'] = "showData();";
        $data = $this->load_top($data);
        $data['title_page'] = "Inventory";
        $this->load->view('inventory/banner', $data);
        $this->load->view('inventory/inv', $data);
        $this->load_bot($data);
    }

    public function all_item() {
        $data['onload'] = "showDataItem();";
        $data = $this->load_top($data);
        $data['title_page'] = "All Items";
        $this->load->view('inventory/banner', $data);
        $this->load->view('inventory/all_item', $data);
        $this->load_bot($data);
    }

    public function inv_ecct() {
        $data['onload'] = "showDataEcct();";
        $data = $this->load_top($data);
        $data['title_page'] = "Inventory ECCT";
        $this->load->view('inventory/banner', $data);
        $this->load->view('inventory/inv_ecct', $data);
        $this->load_bot($data);
    }

    public function inv_ecbs() {
        $data['onload'] = "showDataEcbs();";
        $data = $this->load_top($data);
        $data['title_page'] = "Inventory ECBS";
        $this->load->view('inventory/banner', $data);
        $this->load->view('inventory/inv_ecbs', $data);
        $this->load_bot($data);
    }

    public function input_process() {
        $this->_handle_json_request(function() {
            $input_data = $this->_get_json_input();
            if (!$input_data) {
                return $this->_json_response(false, 'Invalid JSON input');
            }
            $type = isset($input_data['type']) ? $input_data['type'] : null;
            switch ($type) {
                case 'in':
                    return $this->inventory_model->processInventoryIn($input_data);
                case 'out':
                    return $this->inventory_model->processInventoryOut($input_data);
                case 'move':
                    return $this->inventory_model->processInventoryMove($input_data);
                default:
                    return $this->_json_response(false, 'Invalid process type');
            }
        });
    }

    public function data($type = "", $input = "") {
        $data = $this->load_top("", "no_view");
        switch ($type) {
            case 'dvc_cal_show':
                $data['data'] = $this->data_model->getDeviceCalibrationList(10);
                $this->load->view('inventory/data/dvc_cal_show', $data);
                break;
            case 'data_item_show':
                // Cek apakah permintaan datang dari konteks inv_ecct
                if (isset($_GET['context']) && $_GET['context'] === 'inv_ecct') {
                    $data['data'] = $this->data_model->getAllItemEcctOnly(10);
                } else {
                    $data['data'] = $this->data_model->getAllItem(10);
                }
                $this->load->view('inventory/data/data_item_show', $data);
                break;
            case 'data_item_export':
                // Cek apakah permintaan datang dari konteks inv_ecct untuk export
                if (isset($_GET['context']) && $_GET['context'] === 'inv_ecct') {
                    $data['data'] = $this->data_model->getAllItemEcctOnly(999999);
                } else {
                    $data['data'] = $this->data_model->getAllItem(999999);
                }
                $this->load->view('inventory/data/data_item_export', $data);
                break;
            // CASE BARU untuk ECCT APP
            case 'data_inv_ecct_app_show':
                $data['data'] = $this->data_model->getEcctAppData(999999);
                $this->load->view('inventory/data/data_inv_ecct_app_show', $data);
                break;
            case 'data_inv_ecct_app_export':
                $data['data'] = $this->data_model->getEcctAppData(999999);
                $this->load->view('inventory/data/data_inv_ecct_app_export', $data);
                break;
            // CASE BARU untuk ECCT OSC
            case 'data_inv_ecct_osc_show':
                $data['data'] = $this->data_model->getEcctOscData(999999);
                $this->load->view('inventory/data/data_inv_ecct_osc_show', $data);
                break;
            case 'data_inv_ecct_osc_export':
                $data['data'] = $this->data_model->getEcctOscData(999999);
                $this->load->view('inventory/data/data_inv_ecct_osc_export', $data);
                break;
            // CASE BARU untuk ECBS APP
            case 'data_inv_ecbs_app_show':
                $data['data'] = $this->data_model->getEcbsAppData(999999);
                $this->load->view('inventory/data/data_inv_ecbs_app_show', $data);
                break;
            case 'data_inv_ecbs_app_export':
                $data['data'] = $this->data_model->getEcbsAppData(999999);
                $this->load->view('inventory/data/data_inv_ecbs_app_export', $data);
                break;
            // CASE BARU untuk ECBS OSC
            case 'data_inv_ecbs_osc_show':
                $data['data'] = $this->data_model->getEcbsOscData(999999);
                $this->load->view('inventory/data/data_inv_ecbs_osc_show', $data);
                break;
            case 'data_inv_ecbs_osc_export':
                $data['data'] = $this->data_model->getEcbsOscData(999999);
                $this->load->view('inventory/data/data_inv_ecbs_osc_export', $data);
                break;
            default:
                show_404();
                break;
        }
        $this->load_bot($data, "no_view");
    }

    private function _handle_json_request($callback) {
        try {
            $result = $callback();
            $this->_output_json($result);
        } catch (Exception $e) {
            $this->_output_json($this->_json_response(false, 'Error: ' . $e->getMessage()));
        }
    }

    private function _get_json_input() {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }

    private function _json_response($success, $message, $data = null) {
        $response = array(
            'success' => $success,
            'message' => $message
        );
        if ($data !== null) {
            $response['data'] = $data;
        }
        return $response;
    }

    private function _output_json($data) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    function load_top($data = "", $view = "", $access = "") {
        $this->load->model("load_model");
        $data = $this->load_model->load_top_v3($data, $view, $access);
        return $data;
    }

    function load_bot($data = "", $view = "") {
        $this->load->model("load_model");
        $this->load_model->load_bot_v3($data, $view);
    }

    public function input_edit_process() {
        $this->_handle_json_request(function() {
            $input_data = $this->_get_json_input();
            if (!$input_data) {
                return $this->_json_response(false, 'Invalid JSON input');
            }
            return $this->inventory_model->processInventoryEdit($input_data);
        });
    }

    public function input_delete_process() {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $response = array('success' => false, 'message' => '');
        if (!isset($data['id_act'])) {
            $response['message'] = 'ID tidak ditemukan';
            $this->_output_json($response);
            return;
        }
        $id_act = $data['id_act'];
        if ($this->db->where('id_act', $id_act)->delete('inv_act')) {
            $response['success'] = true;
            $response['message'] = 'Data berhasil dihapus';
        } else {
            $response['message'] = 'Gagal menghapus data';
        }
        $this->_output_json($response);
    }
}
