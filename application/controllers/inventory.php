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
        $data['onload'] = "showDataReportNeeds();";
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

        // Handle data_inv_week_show data display
        if ($type == 'data_inv_week_show') {
            $year = $this->uri->segment(4); // Get year from URL segment
            $month = $this->input->get('month');
            
            if ($year && $month) {
                $data['data'] = $this->report_model->get_inv_week_data($year, $month);
            } else {
                $data['data'] = array();
            }
            
            $this->load->view('inventory/data/data_inv_week_show', $data);
            $this->load_bot($data, "no_view");
            return;
        }

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

    // Weekly period management methods
    public function inv_week() {
        $data['onload'] = "showInvWeekData();";
        $data = $this->load_top($data);
        $data['title_page'] = "Inventory Weekly Period Management";
        $this->load->view('inventory/banner', $data);
        $this->load->view('inventory/inv_week', $data);
        $this->load->view('inventory/javascript', $data);
        $this->load_bot($data);
    }

    public function generate_inv_week_periods() {
        try {
            $input_data = $this->_get_json_input();
            if (!$input_data) {
                return $this->_output_json($this->_json_response(false, 'Invalid JSON input'));
            }

            $year = isset($input_data['year']) ? intval($input_data['year']) : null;
            $month = isset($input_data['month']) ? intval($input_data['month']) : null;

            if (!$year || !$month) {
                return $this->_output_json($this->_json_response(false, 'Year and month are required'));
            }

            // Validate year and month ranges
            if ($year < 2020 || $year > 2030) {
                return $this->_output_json($this->_json_response(false, 'Invalid year range'));
            }
            
            if ($month < 1 || $month > 12) {
                return $this->_output_json($this->_json_response(false, 'Invalid month range'));
            }

            $result = $this->report_model->generate_weekly_periods($year, $month);
            
            $message = "Periods generated successfully with 27th-26th logic and 08:00-17:00 time schedule. Total periods: " . count($result);
            return $this->_output_json($this->_json_response(true, $message, $result));

        } catch (Exception $e) {
            return $this->_output_json($this->_json_response(false, 'Error: ' . $e->getMessage()));
        }
    }

    public function update_inv_week_period() {
        try {
            $input_data = $this->_get_json_input();
            if (!$input_data) {
                return $this->_output_json($this->_json_response(false, 'Invalid JSON input'));
            }

            $id_week = isset($input_data['id_week']) ? intval($input_data['id_week']) : null;
            $date_start = isset($input_data['date_start']) ? $input_data['date_start'] : null;
            $date_finish = isset($input_data['date_finish']) ? $input_data['date_finish'] : null;

            if (!$id_week || !$date_start || !$date_finish) {
                return $this->_output_json($this->_json_response(false, 'All fields are required'));
            }

            // Validate date format
            $start_dt = DateTime::createFromFormat('Y-m-d\TH:i', $date_start);
            $finish_dt = DateTime::createFromFormat('Y-m-d\TH:i', $date_finish);
            
            if (!$start_dt || !$finish_dt) {
                return $this->_output_json($this->_json_response(false, 'Invalid date format'));
            }

            if ($start_dt >= $finish_dt) {
                return $this->_output_json($this->_json_response(false, 'Start date must be before finish date'));
            }

            $result = $this->report_model->update_inv_week($id_week, $date_start, $date_finish);
            
            if ($result) {
                $message = "Period updated successfully. Times automatically set to 08:00-17:00";
                return $this->_output_json($this->_json_response(true, $message));
            } else {
                return $this->_output_json($this->_json_response(false, 'Failed to update period'));
            }

        } catch (Exception $e) {
            return $this->_output_json($this->_json_response(false, 'Error: ' . $e->getMessage()));
        }
    }

    public function export_inv_week() {
        try {
            $year = $this->input->get('year');
            $month = $this->input->get('month');
            
            if (!$year || !$month) {
                redirect('inventory/inv_week');
            }

            $data = $this->report_model->get_inv_week_data($year, $month);
            $this->exportInvWeekData($data, 'inv_week_' . $year . '_' . $month);

        } catch (Exception $e) {
            redirect('inventory/inv_week');
        }
    }

    private function exportInvWeekData($data, $filename) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Header
        fputcsv($output, array('ID Week', 'Year', 'Month', 'Week', 'Date Start', 'Date Finish', 'Duration (Days)'));
        
        // Data
        foreach ($data as $row) {
            $start_date = new DateTime($row['date_start']);
            $finish_date = new DateTime($row['date_finish']);
            $duration = $start_date->diff($finish_date)->days + 1;
            
            fputcsv($output, array(
                $row['id_week'],
                $row['period_y'],
                $row['period_m'],
                $row['period_w'],
                $start_date->format('d/m/Y H:i'),
                $finish_date->format('d/m/Y H:i'),
                $duration
            ));
        }
        
        fclose($output);
        exit;
    }

    // Helper methods
    private function _handle_json_request($callback) {
        try {
            $result = $callback();
            return $this->_output_json($result);
        } catch (Exception $e) {
            return $this->_output_json($this->_json_response(false, 'Error: ' . $e->getMessage()));
        }
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

    // Other existing methods...
    public function report($type = "", $input = "") {
        $data = $this->load_top("", "no_view");
        
        switch ($type) {
            case 'report_ecbs_app_show':
                $data['data'] = $this->report_model->getReportData('ecbs', 'app');
                $this->load->view('report/needs/report_app_show', $data);
                break;
                
            case 'report_ecct_app_show':
                $data['data'] = $this->report_model->getReportData('ecct', 'app');
                $this->load->view('report/needs/report_app_show', $data);
                break;
                
            case 'report_ecbs_osc_show':
                $data['data'] = $this->report_model->getReportData('ecbs', 'osc');
                $this->load->view('report/needs/report_osc_show', $data);
                break;
                
            case 'report_ecct_osc_show':
                $data['data'] = $this->report_model->getReportData('ecct', 'osc');
                $this->load->view('report/needs/report_osc_show', $data);
                break;
                
            case 'report_ecbs_app_export':
                $data['data'] = $this->report_model->getReportData('ecbs', 'app');
                $this->exportReportData($data['data'], 'ECBS_APP_Report');
                break;
                
            case 'report_ecct_app_export':
                $data['data'] = $this->report_model->getReportData('ecct', 'app');
                $this->exportReportData($data['data'], 'ECCT_APP_Report');
                break;
                
            case 'report_ecbs_osc_export':
                $data['data'] = $this->report_model->getReportData('ecbs', 'osc');
                $this->exportReportData($data['data'], 'ECBS_OSC_Report');
                break;
                
            case 'report_ecct_osc_export':
                $data['data'] = $this->report_model->getReportData('ecct', 'osc');
                $this->exportReportData($data['data'], 'ECCT_OSC_Report');
                break;
                
            default:
                break;
        }
        
        $this->load_bot($data, "no_view");
    }
    
    private function exportReportData($data, $filename) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Header
        fputcsv($output, array('ID', 'Device Code', 'Device Name', 'Status'));
        
        // Data
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
        $data = array(
            'id_dvc' => $this->input->post('id_dvc'),
            'dvc_size' => $this->input->post('dvc_size'),
            'dvc_col' => $this->input->post('dvc_col'),
            'dvc_qc' => $this->input->post('dvc_qc'),
            'needs_qty' => $this->input->post('needs_qty')
        );
        
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
        } else {
            // Insert new record
            $result = $this->report_model->saveNeedsData($data);
        }
        
        echo json_encode(array('success' => $result));
    }

    public function save_all_needs_data() {
        $data = $this->input->post('data');
        $success_count = 0;
        
        foreach ($data as $item) {
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
            } else {
                // Insert new record
                $result = $this->report_model->saveNeedsData($item);
            }
            
            if ($result) {
                $success_count++;
            }
        }
        
        echo json_encode(array('success' => $success_count, 'total' => count($data)));
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
