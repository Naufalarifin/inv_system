
		 <div class="card-table">
		  <table class="table table-border align-middle text-gray-700 font-medium text-sm">
		   <thead>
		    <tr>
		     <th align="center" align="center" width="30">No</th>
		     <th align="center" width="130">Date</th>
		     <th align="left" width="160">Device Code</th>
		     <th align="center" align="center" width="200">Serial Number</th>
		     <th align="center" width="120">Released</th>
		     <th align="center" width="120">
		     	<x onclick="document.getElementById('sort_lt').value='';$('#btn_search').click();">Lifetime</x> 
		     	<?php if(isset($_GET['sort_lt']) && $_GET['sort_lt']=="asc"){ ?>
			     	<button class="btn btn-xs btn-icon btn-clear btn-primary"   
			     	onclick="document.getElementById('sort_lt').value='desc';$('#btn_search').click();">
					 <i class="ki-outline ki-arrow-up"></i>
					</button>
				<?php }else{ ?>
			     	<button class="btn btn-xs btn-icon btn-clear btn-primary" 
			     	onclick="document.getElementById('sort_lt').value='asc';$('#btn_search').click();">
					 <i class="ki-outline ki-arrow-down"></i>
					</button>
				<?php } ?>

		     </th>
		     <th align="center" width="70">Electrode</th>
		     <th align="center" width="70">Cable</th>
		     <th align="center" width="70">Action</th>
		     <th align="center" width="70">Code</th>
		     <th></th>
		    </tr>
		   </thead>
		   <tbody>
		   	<?php $no=0; foreach ($data['query']->result_array() as $row){ $no++; ?>
		    <tr>
		     <td><?php echo $no+($data['page']['show']*(max($_GET['p'],1)-1)); ?></td>
		     <td><?php echo date("d M Y", strtotime($row['added'])); ?></td>
		     <td data-tooltip="#tooltip_<?php echo $row['dvc_id']; ?>">
		     	<?php if($row['dvc_type']=="app"){ ?>
		     		<span class="badge badge-sm badge-success badge-outline">APP</span>
		     	<?php }elseif($row['dvc_type']=="osc"){ ?>
		     		<span class="badge badge-sm badge-primary badge-outline">OSC</span>
		     	<?php }elseif($row['dvc_type']=="acc"){ ?>
		     		<span class="badge badge-sm badge-warning badge-outline">ACC</span>
		     	<?php } ?>

		     	<x><?php echo $row['dvc_code']; ?></x>
		     </td>
		     <td align="center"><?php echo $row['dvc_sn']; ?></td>
		     <td><?php echo date("d M Y", strtotime($row['dvc_rls'])); ?></td>

		     <?php 
		     	$diff=$this->format_model->getDateDiff($row['dvc_rls'],$row['act_date']);
		     	$bg_color="";

		     	if($diff['y']==0){
			     	if($diff['m']<1){
			     		$bg_color="danger";
			     	}else{
			     		$bg_color="warning";
			     	}
		     	}else{
			     	$bg_color="primary";
		     	}
		     ?>
		     <td align="center">
		     	<span class="badge badge-sm badge-<?php echo $bg_color; ?> badge-outline">
		     		<?php echo $this->format_model->getDateDiffShow($row['dvc_rls'],$row['act_date'],"ymd_full"); ?>
		     	</span>
		     </td>

		     <td align="center">
		     	<?php if($row['var1_status']=="2"){ ?>
		     		<span class="badge badge-sm badge-danger badge-outline">Damaged</span>
		     	<?php }elseif($row['var1_status']=="1"){ ?>
		     		<span class="badge badge-sm badge-success badge-outline">Normal</span>
		     	<?php } ?>
		     </td>
		     <td align="center">
		     	<?php if($row['var2_status']=="2"){ ?>
		     		<span class="badge badge-sm badge-danger badge-outline">Damaged</span>
		     	<?php }elseif($row['var2_status']=="1"){ ?>
		     		<span class="badge badge-sm badge-success badge-outline">Normal</span>
		     	<?php } ?>
		     </td>
		     <td align="center">
		     	<?php if($row['var3_status']=="2"){ ?>
		     		<span class="badge badge-sm badge-danger badge-outline">Replaced</span>
		     	<?php }elseif($row['var3_status']=="1"){ ?>
		     		<span class="badge badge-sm badge-primary badge-outline">Repaired</span>
		     	<?php } ?>
		     </td>
		     <td align="center" data-tooltip="#tooltip_code_<?php echo $row['dvc_id']; ?>"><?php echo $row['code_status']; ?></td>
		     <td></td>
		    </tr>
			<?php } ?>
			<?php if($no==0){ ?>
			<tr>
		     <td align="center" colspan="99"><i>Not Found</i></td>
		    </tr>
			<?php } ?>
		   </tbody>
		  </table>
		 </div>


<?php $no=0; foreach ($data['query']->result_array() as $row){ $no++; ?>
<div class="tooltip" id="tooltip_<?php echo $row['dvc_id']; ?>">
 <?php echo $row['dvc_name']; ?>
</div>

<?php if($row['code_status']!="" && in_array($row['dvc_type'], array("app","osc") ) ){ ?>
<?php
	$unit=$this->data_model->getCustom(($row['dvc_type']."_unit"),"kal_alat_kode","ucode",$row['code_status']);
	$spec=$this->data_model->getCustom(($row['dvc_type']."_spec"),"kal_alat_kode","ucode",$row['code_status']);

	//echo ($row['dvc_type']."_unit")."<br/><br/>";
?>
<div class="tooltip" id="tooltip_code_<?php echo $row['dvc_id']; ?>">
 <?php echo $unit." | ".$spec; ?>
</div>
<?php } ?>

<?php } ?>