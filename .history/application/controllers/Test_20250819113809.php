<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {
    

    public function index() {
        $data['onload'] = ""; // No specific onload function needed for data display
        $data = $this->load_top($data, "no_view");
        $data['title_page'] = "Massive Inventory Input";

       // echo "123";

        $data = $this->data_model->getAllItemFull(1000);

        $no=1;
        $arr_size=array("XS","S","M","L","XL","XXL","3XL","ALL","CUS","-");
        $arr_col=array("Dark Gray","Black","Grey","Blue Navy","Green Army","Red Maroon","Custom","-");
        $arr_qc=array("1","0");
        foreach ($data->result_array() as $row){
            if($row['dvc_type']=="APP"){
                for($i=0;$i<sizeof($arr_size);$i++){
                    if($row['dvc_code']=="VOH"){
                        for($j=0;$j<sizeof($arr_col);$j++){
                            echo $no.". ".$row['dvc_code']." : ".$row['id_dvc']."_".$arr_size[$i]."_".$arr_col[$j]."_DN";
                            echo " : ".$this->data_model->getStockDvc($row['id_dvc'],$arr_size[$i],$arr_col[$j],"0");
                            echo "<br/>";
                            $no++;
                        } 
                    }else{
                        if($row['dvc_tech']=="ecct"){
                            echo $no.". ".$row['dvc_code']." : ".$row['id_dvc']."_".$arr_size[$i]."_Black"."_DN";
                            echo " : ".$this->data_model->getStockDvc($row['id_dvc'],$arr_size[$i],"","0");
                            echo "<br/>";
                            $no++;
                        }else{
                            echo $no.". ".$row['dvc_code']." : ".$row['id_dvc']."_".$arr_size[$i]."_Dark Gray"."_DN";
                            echo " : ".$this->data_model->getStockDvc($row['id_dvc'],$arr_size[$i],"Dark Gray","0");
                            echo "<br/>";
                            $no++;

                        }
                        
                    }
                    
                }
            }else{
                if($row['dvc_tech']=="ecct"){
                    for($k=0;$k<sizeof($arr_qc);$k++){
                        echo $no.". ".$row['dvc_code']." : ".$row['id_dvc']."_-_-".$arr_qc[$k];
                        echo " : ".$this->data_model->getStockDvc($row['id_dvc'],"","",$arr_qc[$k]);
                        echo "<br/>";
                        $no++;
                    }
                }else{
                    echo $no.". ".$row['dvc_code']." : ".$row['id_dvc']."_-_-_DN";
                    echo " : ".$this->data_model->getStockDvc($row['id_dvc'],"","","0");
                    echo "<br/>";
                    $no++;
                }
            }

        }

       

        $this->load_bot($data, "no_view");
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

    public function test_report_model() {
        $this->load->model("report_model");

        $week=$this->report_model->get_week_periods("2025", "9");

        echo "<pre>";
           print_r($week);
        echo "</pre>";
            
    }

}
