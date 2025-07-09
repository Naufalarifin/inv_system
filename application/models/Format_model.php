<?php

class format_model extends CI_Model {

	public function getPageData(){
		$page['link']="?";
		$link_list=array("s");

		for($i=0;$i<sizeof($link_list);$i++){
			if(isset($_GET[$link_list[$i]])){
				$page['link'].="&".$link_list[$i]."=".$_GET[$link_list[$i]];
			}
		}

		return $page;
	}



	public function urlExist($url){
		$result=false;
		$url=str_replace(" ", "%20", $url);
		$temp_header=get_headers($url);
		$HttpRes = substr($temp_header[0], 9, 3);
		if($HttpRes=="200"){ $result=true; }

		return $result;
	}

	public function getDateDiff($date1,$date2){
		//$date1="1994-04-18";
		//$date2="2022-04-19";

		$diff_year=date('Y',strtotime($date2)) - date('Y',strtotime($date1));
		$diff_month=date('m',strtotime($date2)) - date('m',strtotime($date1));
		$diff_day=date('d',strtotime($date2)) - date('d',strtotime($date1));

		if($diff_day<0){ $diff_day+=30; $diff_month--;  }
		if($diff_month<0){ $diff_month+=12; $diff_year--;  }

		$diff['d']=$diff_day;
		$diff['m']=$diff_month;
		$diff['y']=$diff_year;

		return $diff;
	}

	public function getDateDiffShow($date1,$date2,$mode=""){
		//$date1="1994-04-18";
		//$date2="2022-04-19";

		$diff_year=date('Y',strtotime($date2)) - date('Y',strtotime($date1));
		$diff_month=date('m',strtotime($date2)) - date('m',strtotime($date1));
		$diff_day=date('d',strtotime($date2)) - date('d',strtotime($date1));

		if($diff_day<0){ $diff_day+=30; $diff_month--;  }
		if($diff_month<0){ $diff_month+=12; $diff_year--;  }

		$diff['d']=$diff_day;
		$diff['m']=$diff_month;
		$diff['y']=$diff_year;

		$result ="";

		if($mode=="idn"){
			$result.=$diff_year>0 ? ($diff_year." Tahun ") : "";
			$result.=$diff_month>0 ? ($diff_month." Bulan") : "";
			$result.=($diff_day>0 && $diff_year==0) ? (" ".$diff_day." Hari") : "";
			//$result.=$diff_day>0 ? ($diff_day."D ") : "";
		}elseif($mode=="ymd"){
			$result.=$diff_year>0 ? ($diff_year."Y ") : "";
			$result.=$diff_month>0 ? ($diff_month."M ") : "";
			$result.=($diff_year<1) ? ($diff_day."D ") : "";
		}elseif($mode=="ymd_full"){
			$result.=($diff_year."Y ");
			$result.=($diff_month."M ");
			$result.=($diff_day."D");
		}elseif($mode=="age"){
			$result.=$diff_year>0 ? ($diff_year) : "";
		}else{
			$result.=$diff_year>0 ? ($diff_year."Y ") : "";
			$result.=$diff_month>0 ? ($diff_month."M") : "";
			//$result.=$diff_day>0 ? ($diff_day."D ") : "";
		}

		return $result;
	}

	

}


?>