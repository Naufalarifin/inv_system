<?php

class load_model extends CI_Model {

	public function load_top_v3($data="",$view="",$access=""){
		$data['lt_start']=microtime(true);

		$this->load->database();
		$this->load->model("data_model");
		$this->load->model("config_model");
		$this->load->model("format_model");

		$data['config']=$this->config_model->getConfig();

		if($access!=""){
			if($access=="public"){

			}elseif($access=="all" && isset($_SESSION['ccare']['id_user'])){

			}elseif(!isset($data['config']['access'][$access]) || !$data['config']['access'][$access] ){
				redirect($data['config']['base_url'].'');
			}
		}

		if($view!="no_view"){
			if($view=="db_clean" && false){
				$this->load->view('template/main_top_db_clean', $data);
			}else{
				if($view!="docuprint" || true){
					$this->load->view('v3_template/main_top', $data);
				} 
			}

			if($view!="no_panel" && $view!="docuprint"){
				//$this->load->view('template/main_nav', $data);
				$this->load->view('v3_template/main_menu', $data);
			}

			if($view=="docuprint"){
				//echo "<center><div style='width:800px;background-color:#999;'>";
				$this->load->view('template/main_top_docuprint', $data);
			}


		}
		return $data;
	}

	public function load_bot_v3($data="",$view=""){
		$data['lt_finish']=microtime(true);
		$data['l_time']=number_format(($data['lt_finish']-$data['lt_start']), 2, '.', '');

		$data['page']=$this->format_model->getPageData();
		$this->load->view('v3_template/main_page', $data);


			if($view=="docuprint"){
				//echo "</div></center>";
				$this->load->view('template/main_bot_docuprint', $data);
			}

		if($view!="no_view" && $view!="docuprint"){
			$this->load->view('v3_template/main_bot', $data);
			//echo "<br/><br/><br/><br/>Load Time : ".$data['l_time'];
		} 

	}

}