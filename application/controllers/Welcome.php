<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		session_start();
		$this->load->database();
		$this->load->model("data_model");
		$this->load->model("config_model");

		$data['config']=$this->config_model->getConfig();

		redirect($data['config']['base_url'].'dashboard');
	}




}


