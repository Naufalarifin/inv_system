<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class config_model extends CI_Model {

	public function getConfig(){
		//if(isset($_SESSION['user'])){
			date_default_timezone_set("Asia/Jakarta");
			$config['base_url']="http://".$_SERVER['SERVER_NAME']."/cdummy/";
			$config['base_ip']="http://".$_SERVER['SERVER_NAME']."/";


			$this->load->helper('url');
			$array= explode("/", uri_string());
			$config['hal']=$array[0];
			if(isset($array[1])){ $config['hal_sub']=$array[1];}
			$config['url']=$config['base_url'].$array[0].(isset($array[1]) ? ("/".$array[1]) : "");
			$config['url_menu']=$config['base_url'].$array[0]."/";
			$config['url_full']="http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];


			$config['adm_id']="123";
			$config['adm_name']="Dummy";
			$config['adm_fullname']="Dummy Dummy";
			$config['adm_access']="inventory";
			$config['access']['inventory']=true;
			$config['access']['laporan'] = true;
			$config['access']['history'] = true;
			$config['access']['report'] = true;


			return $config;
	}

	
}
?>