<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class History extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model(['data_model', 'config_model', 'History_model']);
        $this->load->helper('url');
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        $config = $this->config_model->getConfig();
        redirect($config['base_url'] . 'history/all_history');
    }

    /**
     * SUBFITUR 1: All History
     */
    public function all_history() {
        $data['onload'] = "showAllHistory();";
        $data = $this->load_top($data);
        $data['title_page'] = "All History";
        
        // Get history data
        $filters = $this->_get_filters();
        $limit = 20;
        $offset = $this->input->get('offset') ? (int)$this->input->get('offset') : 0;
        
        $data['history_data'] = $this->History_model->get_all_history($filters, $limit, $offset);
        $data['total_records'] = $this->History_model->count_all_history($filters);
        $data['activity_type'] = 'ALL';
        $data['filters'] = $filters;
        
        $this->load->view('history/banner', $data);
        $this->load->view('history/all_history', $data);
        $this->load_bot($data);
    }

    /**
     * SUBFITUR 2: IN History
     */
    public function in_history() {
        $data['onload'] = "showInHistory();";
        $data = $this->load_top($data);
        $data['title_page'] = "IN History";
        
        $filters = $this->_get_filters();
        $limit = 20;
        $offset = $this->input->get('offset') ? (int)$this->input->get('offset') : 0;
        
        $data['history_data'] = $this->History_model->get_in_history($filters, $limit, $offset);
        $data['total_records'] = $this->History_model->count_in_history($filters);
        $data['activity_type'] = 'IN';
        $data['filters'] = $filters;
        
        $this->load->view('history/banner', $data);
        $this->load->view('history/in_history', $data);
        $this->load_bot($data);
    }

    /**
     * SUBFITUR 3: MOVE History
     */
    public function move_history() {
        $data['onload'] = "showMoveHistory();";
        $data = $this->load_top($data);
        $data['title_page'] = "MOVE History";
        
        $filters = $this->_get_filters();
        $limit = 20;
        $offset = $this->input->get('offset') ? (int)$this->input->get('offset') : 0;
        
        $data['history_data'] = $this->History_model->get_move_history($filters, $limit, $offset);
        $data['total_records'] = $this->History_model->count_move_history($filters);
        $data['activity_type'] = 'MOVE';
        $data['filters'] = $filters;
        
        $this->load->view('history/banner', $data);
        $this->load->view('history/move_history', $data);
        $this->load_bot($data);
    }

    /**
     * SUBFITUR 4: OUT History
     */
    public function out_history() {
        $data['onload'] = "showOutHistory();";
        $data = $this->load_top($data);
        $data['title_page'] = "OUT History";
        
        $filters = $this->_get_filters();
        $limit = 20;
        $offset = $this->input->get('offset') ? (int)$this->input->get('offset') : 0;
        
        $data['history_data'] = $this->History_model->get_out_history($filters, $limit, $offset);
        $data['total_records'] = $this->History_model->count_out_history($filters);
        $data['activity_type'] = 'OUT';
        $data['filters'] = $filters;
        
        $this->load->view('history/banner', $data);
        $this->load->view('history/out_history', $data);
        $this->load_bot($data);
    }

    /**
     * SUBFITUR 5: RELEASE History
     */
    public function release_history() {
        $data['onload'] = "showReleaseHistory();";
        $data = $this->load_top($data);
        $data['title_page'] = "RELEASE History";
        
        $filters = $this->_get_filters();
        $limit = 20;
        $offset = $this->input->get('offset') ? (int)$this->input->get('offset') : 0;
        
        $data['history_data'] = $this->History_model->get_release_history($filters, $limit, $offset);
        $data['total_records'] = $this->History_model->count_release_history($filters);
        $data['activity_type'] = 'RELEASE';
        $data['filters'] = $filters;
        
        $this->load->view('history/banner', $data);
        $this->load->view('history/release_history', $data);
        $this->load_bot($data);
    }

    /**
     * IN History page
     */
    public function in() {
        $data = $this->load_top("IN History", "history");
        $this->load->view('history/in_history', $data);
        $this->load_bot($data);
    }
    
    /**
     * OUT History page
     */
    public function out() {
        $data = $this->load_top("OUT History", "history");
        $this->load->view('history/out_history', $data);
        $this->load_bot($data);
    }
    
    /**
     * MOVE History page
     */
    public function move() {
        $data = $this->load_top("MOVE History", "history");
        $this->load->view('history/move_history', $data);
        $this->load_bot($data);
    }
    
    /**
     * RELEASE History page
     */
    public function release() {
        $data = $this->load_top("RELEASE History", "history");
        $this->load->view('history/release_history', $data);
        $this->load_bot($data);
    }

    /**
     * Data method untuk AJAX requests (mirip dengan inventory)
     */
    public function data($type = "", $input = "") {
        $data = $this->load_top("", "no_view");
        
        switch ($type) {
            case 'history_show':
                $filters = $this->_get_filters();
                $limit = $this->input->get('data_view_history') ? (int)$this->input->get('data_view_history') : 10;
                $offset = $this->input->get('p') ? (int)$this->input->get('p') * $limit : 0;
                
                $history_data = $this->History_model->get_all_history($filters, $limit, $offset);
                $total_records = $this->History_model->count_all_history($filters);
                
                $data['data'] = $history_data;
                $data['page']['sum'] = $total_records;
                $data['page']['show'] = $limit;
                $data['page']['first'] = $offset;
                
                $this->load->view('history/data/history_show', $data);
                break;
                
            case 'history_export':
                $filters = $this->_get_filters();
                $data['data'] = $this->History_model->get_all_history($filters, 999999);
                $this->load->view('history/data/history_export', $data);
                break;
                
            default:
                show_404();
                break;
        }
        
        $this->load_bot($data, "no_view");
    }

    /**
     * Get filters from GET parameters
     */
    private function _get_filters() {
        $filters = array();
        
        // Search key - search across multiple fields
        if ($this->input->get('key_history')) {
            $filters['search'] = $this->input->get('key_history');
        }
        
        // Device filters
        if ($this->input->get('dvc_sn')) {
            $filters['dvc_sn'] = $this->input->get('dvc_sn');
        }
        if ($this->input->get('dvc_name')) {
            $filters['dvc_name'] = $this->input->get('dvc_name');
        }
        
        // Admin filter
        if ($this->input->get('admin')) {
            $filters['admin'] = $this->input->get('admin');
        }
        
        // Date filters
        if ($this->input->get('date_from')) {
            $filters['date_from'] = $this->input->get('date_from');
        }
        if ($this->input->get('date_to')) {
            $filters['date_to'] = $this->input->get('date_to');
        }
        
        // Activity type filter
        if ($this->input->get('activity_type')) {
            $filters['activity_type'] = $this->input->get('activity_type');
        }
        
        // Sort filter
        if ($this->input->get('sort_by')) {
            $filters['sort_by'] = $this->input->get('sort_by');
        }
        
        return $filters;
    }

    /**
     * Helper methods untuk JSON handling (mirip dengan inventory)
     */
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

    /**
     * Load top dan bottom methods (mirip dengan inventory)
     */
    function load_top($data = "", $view = "", $access = "") {
        $this->load->model("load_model");
        $data = $this->load_model->load_top_v3($data, $view, $access);
        return $data;
    }

    function load_bot($data = "", $view = "") {
        $this->load->model("load_model");
        $this->load_model->load_bot_v3($data, $view);
    }

    /**
     * Method untuk testing
     */
    public function test() {
        echo "<h2>Testing History Controller</h2>";
        echo "<p>URL Base: http://localhost/cdummy/</p>";
        
        // Test model
        $this->load->model('History_model');
        echo "<h3>Test Model - Get 3 All History:</h3>";
        $test_data = $this->History_model->get_all_history(array(), 3);
        echo "<pre>";
        print_r($test_data);
        echo "</pre>";
        
        echo "<h3>Test Statistics:</h3>";
        $stats = $this->History_model->get_activity_stats();
        echo "<pre>";
        print_r($stats);
        echo "</pre>";
        
        echo "<p><strong>Jika data muncul di atas, berarti Model OK!</strong></p>";
        echo "<p>Sekarang coba akses: <a href='http://localhost/cdummy/history/all_history'>http://localhost/cdummy/history/all_history</a></p>";
    }
}
?>