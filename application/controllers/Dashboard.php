<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        session_start();
        $this->load->database();
        $this->load->model("data_model");
        $this->load->model("config_model");
    }
    
    public function index() {
        $data['config'] = $this->config_model->getConfig();
        $data['page_title'] = 'Dashboard';
        
        $this->load->view('dashboard/main_dashboard', $data);
    }
}
