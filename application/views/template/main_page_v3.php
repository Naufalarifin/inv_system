<?php if(isset($data['page'])){ ?>

<?php 
$sum=$data['page']['sum'];
$show=$data['page']['show'];
$last=($sum+($show-($sum%$show)))/$show;
if($sum%$show==0){ $last-=1; }
if(!isset($func_show)){$func_show="showData";}
?>

<?php if($sum>$show){ ?>

<div class="col-md-12" style="padding:0px 0px 0px 0px;">

<?php if(in_array($func_show, array("showDataMedrec","showDataMonit") )){ ?>
<nav aria-label="nav" style="margin-bottom:-20px;margin-top:-20px;">
  <ul class="pager">

  	<?php if(($_GET['p']-1)>=1){ ?>
    <li class="previous"><a href="#" onclick="<?php echo $func_show."('".($_GET['p']-1)."');"; ?>">
    	<span aria-hidden="true">&larr;</span> Prev</a>
    </li>
    <?php } ?>

    <li style="padding-top:20px;vertical-align: middle;">
	   <a><?php echo "Data <b>".((($_GET['p']-1)*$show)+1). "</b> - <b>".min(($_GET['p']*$show),$sum)."</b> / <b>".$sum."</b>";?></a>
    </li>

    <?php if(($_GET['p']+1)<=$last){ ?>
    <li class="next"><a href="#" onclick="<?php echo $func_show."('".($_GET['p']+1)."');"; ?>">Next 
    	<span aria-hidden="true">&rarr;</span></a>
    </li>
    <?php } ?>

  </ul>
</nav>

<?php }else{ ?>

<div style="float:left;width:100px;height:35px;">
<?php if($_GET['p']>1){ ?>
<a class="btn btn-default" onclick="<?php echo $func_show."('1');"; ?>" role="button" style="float:left;margin-right:4px;"><b><<</b></a>
<?php } ?>

<?php if(($_GET['p']-1)>=1){ ?>
<a class="btn btn-default" onclick="<?php echo $func_show."('".($_GET['p']-1)."');"; ?>" role="button" style="float:left;"><b><</b></a>
<?php } ?>
</div>

<div style="float:left;width:calc(100% - 200px);height:35px;">
<center style="padding-top:10px;">
<?php for($i=1;$i<=$last;$i++){ if($last!=1){?>
  <?php if(($i>=($_GET['p']-1)) && ($i<=($_GET['p']+2)) ){ ?>
  <a role="button" onclick="<?php echo $func_show."('".($i)."');"; ?>" ><b><?php echo $i; ?></b></a>
  <?php } ?>
<?php } } ?>
</center>
</div>

<div style="width:100px;float:right;height:35px;">
<?php if($_GET['p']<$last){ ?>
<a class="btn btn-default" onclick="<?php echo $func_show."('".($last)."');"; ?>" role="button" style="float:right;margin-left:4px;"><b>>></b></a>
<?php } ?>

<?php if(($_GET['p']+1)<=$last){ ?>
<a class="btn btn-default" onclick="<?php echo $func_show."('".($_GET['p']+1)."');"; ?>" role="button" style="float:right;"><b>></b></a>
<?php } ?>
</div>



<div style="width:100%;text-align: center;margin-top:20px;height:50px;vertical-align: middle;">
	<?php echo "Data <b>".((($_GET['p']-1)*$show)+1). "</b> - <b>".min(($_GET['p']*$show),$sum)."</b> / <b>".$sum."</b> Total Data";?>
	<?php echo $l_time!="" ? (" | Loadtime : <b>".$l_time."s</b>") : "";?>
</div>



<?php } ?>


</div>

<?php } ?>


<?php } ?>

 