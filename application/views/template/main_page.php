<?php if(isset($data['page'])){ ?>

<?php 
$sum=$data['page']['sum'];
$show=$data['page']['show'];
$last=($sum+($show-($sum%$show)))/$show;
if($sum%$show==0){ $last-=1; }
?>

<?php if($sum>$show){ ?>

<div class="col-md-12" style="padding:0px 0px 20px 0px;">

<div style="float:left;width:100px;height:35px;">
<?php if($_GET['p']>1){ ?>
<a class="btn btn-default" href="<?php echo $page['link']."&p=1"; ?>" role="button" style="float:left;margin-right:4px;"><b><<</b></a>
<?php } ?>

<?php if(($_GET['p']-1)>=1){ ?>
<a class="btn btn-default" href="<?php echo $page['link']."&p=".($_GET['p']-1); ?>" role="button" style="float:left;"><b><</b></a>
<?php } ?>
</div>

<div style="float:left;width:calc(100% - 200px);height:35px;">
<center>
<?php for($i=1;$i<=$last;$i++){ if($last!=1){?>
  <?php if(($i>=($_GET['p']-1)) && ($i<=($_GET['p']+2)) ){ ?>
  <a class="btn btn-<?php if($i==$_GET['p']){ echo "primary"; }else { echo "default"; } ?> hidden-xsx" href="<?php echo $page['link']."&p=".$i; ?>" role="button"><b><?php echo $i; ?></b></a>
  <?php } ?>
<?php } } ?>
</center>
</div>

<div style="width:100px;float:right;height:35px;">
<?php if($_GET['p']<$last){ ?>
<a class="btn btn-default" href="<?php echo $page['link']."&p=".$last; ?>" role="button" style="float:right;margin-left:4px;"><b>>></b></a>
<?php } ?>

<?php if(($_GET['p']+1)<=$last){ ?>
<a class="btn btn-default" href="<?php echo $page['link']."&p=".($_GET['p']+1); ?>" role="button" style="float:right;"><b>></b></a>
<?php } ?>
</div>


<div style="width:100%;text-align: center;margin-top:20px;height:50px;vertical-align: middle;">
	<?php echo "Data <b>".((($_GET['p']-1)*$show)+1). "</b> - <b>".min(($_GET['p']*$show),$sum)."</b> / <b>".$sum."</b> Total Data";?>
	<?php echo $l_time!="" ? (" | Loadtime : <b>".$l_time."s</b>") : "";?>
</div>


</div>

<?php } ?>


<?php } ?>

 