<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model(array('data_model', 'config_model', 'inventory_model','report_model'));
        $this->load->helper('url');
        session_start();
    }
    
    public function index() {
        $data = $this->load_top($data);
        $config = $this->config_model->getConfig();
        redirect($config['base_url'] . 'inventory/inv_ecct');
    }
    
    public function all_item() {
        $data['onload'] = "showDataItem();";
        $data = $this->load_top($data);
        $data['title_page'] = "All Items";
        $this->load->view('inventory/banner', $data);
        $this->load->view('inventory/all_item', $data);
        $this->load->view('inventory/javascript', $data);
        $this->load_bot($data);
    }
    
    public function massive_input() {
        $data['onload'] = "";
        $data = $this->load_top($data);
        $data['title_page'] = "Massive Inventory Input";
        $this->load->view('inventory/banner', $data);
        $this->load->view('inventory/massive_input', $data);
        $this->load->view('inventory/javascript', $data);
        $this->load_bot($data);
    }
    
    public function inv_ecct() {
        $data['onload'] = "showDataEcct();";
        $data = $this->load_top($data);
        $data['title_page'] = "Inventory ECCT";
        $data['dvc_code'] = $this->inventory_model->getDeviceTypes('ecct');
        $this->load->view('inventory/banner', $data);
        $this->load->view('inventory/inv_ecct', $data);
        $this->load->view('inventory/javascript', $data);
        $this->load_bot($data);
    }
    
    public function inv_ecbs() {
        $data['onload'] = "showDataEcbs();";
        $data = $this->load_top($data);
        $data['title_page'] = "Inventory ECBS";
        $data['dvc_code'] = $this->inventory_model->getDeviceTypes('ecbs');
        $this->load->view('inventory/banner', $data);
        $this->load->view('inventory/inv_ecbs', $data);
        $this->load->view('inventory/javascript', $data);
        $this->load_bot($data);
    }
    
    public function inv_report_needs() {
        $data['onload'] = "";
        $data = $this->load_top($data);
        $data['title_page'] = "Inventory Report Needs";
        $this->load->view('inventory/banner', $data);
        $this->load->view('report/inv_report_needs', $data);
        $this->load_bot($data);
    }
    
    public function input_process() {
        try {
            $input_data = $this->_get_json_input();
            if (!$input_data) {
                return $this->_output_json($this->_json_response(false, 'Invalid JSON input'));
            }
            
            $type = isset($input_data['type']) ? $input_data['type'] : null;
            
            switch ($type) {
                case 'in':
                    $result = $this->inventory_model->processInventoryIn($input_data);
                    break;
                case 'out':
                    $result = $this->inventory_model->processInventoryOut($input_data);
                    break;
                case 'move':
                    $result = $this->inventory_model->processInventoryMove($input_data);
                    break;
                default:
                    $result = $this->_json_response(false, 'Invalid process type');
            }
            
            return $this->_output_json($result);
        } catch (Exception $e) {
            return $this->_output_json($this->_json_response(false, 'Error: ' . $e->getMessage()));
        }
    }
    
    public function data($type = "", $input = "") {
        $data = $this->load_top("", "no_view");
        
        // Mapping type ke handler dan parameter
        $type_map = array(
            // APP
            'data_inv_app_show' => array('handler' => 'getDeviceStockApp', 'view' => 'inventory/data/data_inv_app_show'),
            'data_inv_ecct_app_show' => array('handler' => 'getDeviceStockApp', 'view' => 'inventory/data/data_inv_app_show', 'tech' => 'ecct'),
            'data_inv_ecbs_app_show' => array('handler' => 'getDeviceStockApp', 'view' => 'inventory/data/data_inv_app_show', 'tech' => 'ecbs'),
            'data_inv_app_export' => array('handler' => 'getDeviceStockApp', 'view' => 'inventory/data/data_inv_app_export'),
            'data_inv_ecct_app_export' => array('handler' => 'getDeviceStockApp', 'view' => 'inventory/data/data_inv_app_export', 'tech' => 'ecct'),
            'data_inv_ecbs_app_export' => array('handler' => 'getDeviceStockApp', 'view' => 'inventory/data/data_inv_app_export', 'tech' => 'ecbs'),
            
            // OSC
            'data_inv_osc_show' => array('handler' => 'getDeviceStockOsc', 'view' => 'inventory/data/data_inv_osc_show'),
            'data_inv_ecct_osc_show' => array('handler' => 'getDeviceStockOsc', 'view' => 'inventory/data/data_inv_osc_show', 'tech' => 'ecct'),
            'data_inv_ecbs_osc_show' => array('handler' => 'getDeviceStockOsc', 'view' => 'inventory/data/data_inv_osc_show', 'tech' => 'ecbs'),
            'data_inv_osc_export' => array('handler' => 'getDeviceStockOsc', 'view' => 'inventory/data/data_inv_osc_export'),
            'data_inv_ecct_osc_export' => array('handler' => 'getDeviceStockOsc', 'view' => 'inventory/data/data_inv_osc_export', 'tech' => 'ecct'),
            'data_inv_ecbs_osc_export' => array('handler' => 'getDeviceStockOsc', 'view' => 'inventory/data/data_inv_osc_export', 'tech' => 'ecbs'),
        );
        
        if (isset($type_map[$type])) {
            $tech = isset($type_map[$type]['tech']) ? $type_map[$type]['tech'] : (isset($_GET['tech']) ? $_GET['tech'] : (isset($_POST['tech']) ? $_POST['tech'] : 'ecct'));
            $handler = $type_map[$type]['handler'];
            $view = $type_map[$type]['view'];
            $data['data'] = $this->inventory_model->$handler($tech, 999999);
            $this->load->view($view, $data);
            $this->load_bot($data, "no_view");
            return;
        }
        
        switch ($type) {
            case 'data_item_show':
                $data['data'] = $this->inventory_model->getAllItemByTech('ecct', 10);
                $this->load->view('inventory/data/data_item_show', $data);
                break;
            case 'data_item_show_ecbs':
                $data['data'] = $this->inventory_model->getAllItemByTech('ecbs', 10);
                $this->load->view('inventory/data/data_item_show', $data);
                break;
            case 'data_item_export':
                if (isset($_GET['context']) && $_GET['context'] === 'inv_ecct') {
                    $data['data'] = $this->inventory_model->getAllItemByTech('ecct',999999);
                } elseif (isset($_GET['context']) && $_GET['context'] === 'inv_ecbs') {
                    $data['data'] = $this->inventory_model->getAllItemByTech('ecbs', 999999);
                } else {
                    $data['data'] = $this->inventory_model->getAllItemByTech('ecct',999999);
                }
                $this->load->view('inventory/data/data_item_export', $data);
                break;
            case 'osc_sync_differences':
                $tech = isset($_GET['tech']) ? $_GET['tech'] : 'ecct';
                $data['differences'] = $this->inventory_model->getOscSyncDifferences($tech);
                $this->load->view('inventory/data/osc_sync_differences', $data);
                break;
            default:
                show_404();
                break;
        }
        
        $this->load_bot($data, "no_view");
    }
    
    public function report($type = "", $input = "") {
        $this->load->model('report_model');
        
        if (!$this->input->is_ajax_request()) {
            $data = $this->load_top("", "no_view");
        } else {
            $data = array();
        }

        $report_map = array(
            'report_ecbs_app' => array('tech' => 'ecbs', 'type' => 'app', 'view' => 'report/needs/report_app_show', 'export_filename' => 'ECBS_APP_Report'),
            'report_ecct_app' => array('tech' => 'ecct', 'type' => 'app', 'view' => 'report/needs/report_app_show', 'export_filename' => 'ECCT_APP_Report'),
            'report_ecbs_osc' => array('tech' => 'ecbs', 'type' => 'osc', 'view' => 'report/needs/report_osc_show', 'export_filename' => 'ECBS_OSC_Report'),
            'report_ecct_osc' => array('tech' => 'ecct', 'type' => 'osc', 'view' => 'report/needs/report_osc_show', 'export_filename' => 'ECCT_OSC_Report'),
        );

        // Use strpos for PHP 5 compatibility
        $base_type = str_replace(array('_show', '_export'), '', $type);
        $action = (strpos($type, '_export') !== false) ? 'export' : 'show';

        if (isset($report_map[$base_type])) {
            $config = $report_map[$base_type];
            $data['data'] = $this->report_model->getReportData($config['tech'], $config['type']);
            
            if ($action === 'show') {
                $data['existing_needs'] = $this->report_model->getExistingNeedsData($config['tech'], $config['type']);
                $data['tech'] = $config['tech'];
                $data['device_type'] = $config['type'];
                $this->load->view($config['view'], $data);
            } elseif ($action === 'export') {
                $this->exportReportData($data['data'], $config['export_filename']);
            }
        } else {
            echo "<div style='padding: 20px; text-align: center;'><h3>Report not found</h3></div>";
            return;
        }
        
        if (!$this->input->is_ajax_request()) {
            $this->load_bot($data, "no_view");
        }
    }
    
    private function exportReportData($data, $filename) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        fputcsv($output, array('ID', 'Device Code', 'Device Name', 'Status'));
        
        foreach ($data as $row) {
            fputcsv($output, array(
                $row['id_dvc'],
                $row['dvc_code'],
                $row['dvc_name'],
                $row['status']
            ));
        }
        
        fclose($output);
        exit;
    }
    
    public function save_needs_data() {
        $this->load->model('report_model');
        
        $data = array(
            'id_dvc' => $this->input->post('id_dvc'),
            'dvc_size' => $this->input->post('dvc_size'),
            'dvc_col' => $this->input->post('dvc_col'),
            'dvc_qc' => $this->input->post('dvc_qc'),
            'needs_qty' => $this->input->post('needs_qty')
        );
        
        if (empty($data['id_dvc']) || $data['id_dvc'] == '0') {
            echo json_encode(array('success' => false, 'message' => 'Invalid device ID'));
            return;
        }
        
        // If quantity is 0 or empty, delete the record
        if (empty($data['needs_qty']) || $data['needs_qty'] <= 0) {
            $result = $this->report_model->deleteNeedsData(
                $data['id_dvc'], 
                $data['dvc_size'], 
                $data['dvc_col'], 
                $data['dvc_qc']
            );
            echo json_encode(array('success' => $result, 'action' => 'deleted'));
            return;
        }
        
        // Check if record exists
        $existing = $this->report_model->getNeedsData(
            $data['id_dvc'], 
            $data['dvc_size'], 
            $data['dvc_col'], 
            $data['dvc_qc']
        );
        
        if ($existing) {
            // Update existing record
            $result = $this->report_model->updateNeedsData($existing['id_needs'], array('needs_qty' => $data['needs_qty']));
            echo json_encode(array('success' => $result, 'action' => 'updated'));
        } else {
            // Insert new record
            $result = $this->report_model->saveNeedsData($data);
            echo json_encode(array('success' => $result, 'action' => 'inserted'));
        }
    }
    
    public function save_all_needs_data() {
        $this->load->model('report_model');
        
        $data = $this->input->post('data');
        $success_count = 0;
        $actions = array();
        
        foreach ($data as $item) {
            if (empty($item['id_dvc']) || $item['id_dvc'] == '0') {
                continue;
            }
            
            if (empty($item['needs_qty']) || $item['needs_qty'] <= 0) {
                $result = $this->report_model->deleteNeedsData(
                    $item['id_dvc'], 
                    $item['dvc_size'], 
                    $item['dvc_col'], 
                    $item['dvc_qc']
                );
                if ($result) {
                    $success_count++;
                    $actions[] = 'deleted';
                }
                continue;
            }
            
            // Check if record exists
            $existing = $this->report_model->getNeedsData(
                $item['id_dvc'], 
                $item['dvc_size'], 
                $item['dvc_col'], 
                $item['dvc_qc']
            );
            
            if ($existing) {
                // Update existing record
                $result = $this->report_model->updateNeedsData($existing['id_needs'], array('needs_qty' => $item['needs_qty']));
                if ($result) {
                    $success_count++;
                    $actions[] = 'updated';
                }
            } else {
                // Insert new record
                $result = $this->report_model->saveNeedsData($item);
                if ($result) {
                    $success_count++;
                    $actions[] = 'inserted';
                }
            }
        }
        
        echo json_encode(array(
            'success' => $success_count, 
            'total' => count($data),
            'actions' => array_count_values($actions)
        ));
    }
    
    protected function _get_json_input() {
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
?>
