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

    public function all_item() {
        $data['onload'] = "showDataItem();";
        $data = $this->load_top($data);
        $data['title_page'] = "All Items";
        $this->load->view('inventory/banner', $data);
        $this->load->view('inventory/all_item', $data);
        $this->load_bot($data);
    }

    public function massive_input() {
        $data['onload'] = "";
        $data = $this->load_top($data);
        $data['title_page'] = "Massive Inventory Input";
        $this->load->view('inventory/banner', $data);
        $this->load->view('inventory/massive_input', $data);
        $this->load_bot($data);
    }

    public function inv_ecct() {
        $data['onload'] = "showDataEcct();";
        $data = $this->load_top($data);
        $data['title_page'] = "Inventory ECCT";
        $data['dvc_code'] = $this->data_model->getDeviceTypes('ecct');
        $this->load->view('inventory/banner', $data);
        $this->load->view('inventory/inv_ecct', $data);
        $this->load_bot($data);
    }

    public function inv_ecbs() {
        $data['onload'] = "showDataEcbs();";
        $data = $this->load_top($data);
        $data['title_page'] = "Inventory ECBS";
        $data['dvc_code'] = $this->data_model->getDeviceTypes('ecbs');
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
                    // Tidak ada perubahan di sini, karena $input_data sudah berisi 'user_date' jika ada
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

        // Mapping type ke handler dan parameter
        $type_map = [
            // APP
            'data_inv_app_show' => ['handler' => 'getDeviceStockApp', 'view' => 'inventory/data/data_inv_app_show'],
            'data_inv_ecct_app_show' => ['handler' => 'getDeviceStockApp', 'view' => 'inventory/data/data_inv_app_show', 'tech' => 'ecct'],
            'data_inv_ecbs_app_show' => ['handler' => 'getDeviceStockApp', 'view' => 'inventory/data/data_inv_app_show', 'tech' => 'ecbs'],
            'data_inv_app_export' => ['handler' => 'getDeviceStockApp', 'view' => 'inventory/data/data_inv_app_export'],
            'data_inv_ecct_app_export' => ['handler' => 'getDeviceStockApp', 'view' => 'inventory/data/data_inv_app_export', 'tech' => 'ecct'],
            'data_inv_ecbs_app_export' => ['handler' => 'getDeviceStockApp', 'view' => 'inventory/data/data_inv_app_export', 'tech' => 'ecbs'],
            // OSC
            'data_inv_osc_show' => ['handler' => 'getDeviceStockOsc', 'view' => 'inventory/data/data_inv_osc_show'],
            'data_inv_ecct_osc_show' => ['handler' => 'getDeviceStockOsc', 'view' => 'inventory/data/data_inv_osc_show', 'tech' => 'ecct'],
            'data_inv_ecbs_osc_show' => ['handler' => 'getDeviceStockOsc', 'view' => 'inventory/data/data_inv_osc_show', 'tech' => 'ecbs'],
            'data_inv_osc_export' => ['handler' => 'getDeviceStockOsc', 'view' => 'inventory/data/data_inv_osc_export'],
            'data_inv_ecct_osc_export' => ['handler' => 'getDeviceStockOsc', 'view' => 'inventory/data/data_inv_osc_export', 'tech' => 'ecct'],
            'data_inv_ecbs_osc_export' => ['handler' => 'getDeviceStockOsc', 'view' => 'inventory/data/data_inv_osc_export', 'tech' => 'ecbs'],
        ];

        if (isset($type_map[$type])) {
            $tech = isset($type_map[$type]['tech']) ? $type_map[$type]['tech'] : (isset($_GET['tech']) ? $_GET['tech'] : (isset($_POST['tech']) ? $_POST['tech'] : 'ecct'));
            $handler = $type_map[$type]['handler'];
            $view = $type_map[$type]['view'];
            $data['data'] = $this->data_model->$handler($tech, 999999);
            $this->load->view($view, $data);
            $this->load_bot($data, "no_view");
            return;
        }

        switch ($type) {
            case 'data_item_show':
                $data['data'] = $this->data_model->getAllItemByTech('ecct', 10);
                $this->load->view('inventory/data/data_item_show', $data);
                break;
            case 'data_item_show_ecbs':
                $data['data'] = $this->data_model->getAllItemByTech('ecbs', 10);
                $this->load->view('inventory/data/data_item_show', $data);
                break;
            case 'data_item_export':
                if (isset($_GET['context']) && $_GET['context'] === 'inv_ecct') {
                    $data['data'] = $this->data_model->getAllItemByTech('ecct',999999);
                } elseif (isset($_GET['context']) && $_GET['context'] === 'inv_ecbs') {
                    $data['data'] = $this->data_model->getAllItemByTech('ecbs', 999999);
                } else {
                    $data['data'] = $this->data_model->getAllItemByTech('ecct',999999);
                }
                $this->load->view('inventory/data/data_item_export', $data);
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
}
