<?php
// Perbaikan untuk main_page.php - bagian deteksi fungsi pagination
if (!isset($_GET['p']) || !is_numeric($_GET['p']) || $_GET['p'] < 1) {
    $_GET['p'] = 1;
}
?>

<?php if(isset($data['page'])){ ?>

<?php 
$sum=$data['page']['sum'];
$show=$data['page']['show'];
$last=($sum+($show-($sum%$show)))/$show;
if($sum%$show==0){ $last-=1; }

// Perbaikan logika deteksi fungsi pagination
if(!isset($func_show)){
  // Cek parameter URL untuk menentukan fungsi yang tepat
  if(isset($_GET['table']) && $_GET['table'] == 'allitem'){
    $func_show = 'showDataAllItem';
  } else if(isset($_GET['table']) && $_GET['table'] == 'ecct'){
    $func_show = 'showDataEcct';
  } else if(isset($_GET['mode']) && $_GET['mode'] == 'allitem'){
    $func_show = 'showDataAllItem';
  } else if(isset($_GET['mode']) && $_GET['mode'] == 'ecct'){
    $func_show = 'showDataEcct';
  } else {
    // Auto-detect berdasarkan URL saat ini
    $current_url = $_SERVER['REQUEST_URI'];
    if(strpos($current_url, 'inv_ecct') !== false) {
      // Jika di halaman inv_ecct, cek table yang aktif via JavaScript
      $func_show = 'getCurrentTableFunction()';
    } else if(strpos($current_url, 'inv_ecbs') !== false) {
      // Jika di halaman inv_ecbs, cek table yang aktif via JavaScript
      $func_show = 'getCurrentTableFunction()';
    } else if(strpos($current_url, 'all_item') !== false) {
      $func_show = 'showDataAllItem';
    } else {
      $func_show = 'showDataItem';
    }
  }
}
?>

<?php if($sum>$show){ ?>

<div class="col-md-12" style="padding:20px 20px 10px 20px;border-top:1px solid #EEE;">

<?php if(in_array($func_show, array("showDataMedrec","showDataMonit") )){ ?>
<nav aria-label="nav" style="margin-bottom:-20px;margin-top:-20px;">
  <ul class="pager">
    <?php if(($_GET['p']-1)>=1){ ?>
    <li class="previous"><a  style="cursor: pointer;" onclick="<?php echo $func_show."('".($_GET['p']-1)."');"; ?>">
      <span aria-hidden="true">&larr;</span> Prev</a>
    </li>
    <?php } ?>
    <li style="padding-top:20px;vertical-align: middle;">
  <a><?php echo "Data <b>".((($_GET['p']-1)*$show)+1). "</b> - <b>".min(($_GET['p']*$show),$sum)."</b> / <b>".$sum."</b>";?></a>
    </li>
    <?php if(($_GET['p']+1)<=$last){ ?>
    <li class="next"><a style="cursor: pointer;" onclick="<?php echo $func_show."('".($_GET['p']+1)."');"; ?>">Next 
      <span aria-hidden="true">&rarr;</span></a>
    </li>
    <?php } ?>
  </ul>
</nav>

<?php }else{ ?>

<div style="float:left;width:100px;height:35px;">
<?php if($_GET['p']>1){ ?>
<a class="btn btn-light btn-sm" onclick="<?php if($func_show == 'getCurrentTableFunction()') { echo 'handlePagination(1)'; } else { echo $func_show."('1')"; } ?>" role="button" style="float:left;margin-right:4px;"><b><<</b></a>
<?php } ?>

<?php if(($_GET['p']-1)>=1){ ?>
<a class="btn btn-light btn-sm" onclick="<?php if($func_show == 'getCurrentTableFunction()') { echo 'handlePagination('.($_GET['p']-1).')'; } else { echo $func_show."('".($_GET['p']-1)."')"; } ?>" role="button" style="float:left;"><b><</b></a>
<?php } ?>
</div>

<div style="float:left;width:calc(100% - 200px);height:35px;">

<center>
<?php for($i=1;$i<=$last;$i++){ if($last!=1){?>
  <?php if(($i>=($_GET['p']-3)) && ($i<=($_GET['p']+5)) ){ ?>
  <a class="btn btn-<?php if($i==$_GET['p']){ echo "primary"; }else { echo "light"; } ?> hidden-xsx  btn-sm" role="button"
    onclick="<?php if($func_show == 'getCurrentTableFunction()') { echo 'handlePagination('.$i.')'; } else { echo $func_show."('".$i."')"; } ?>" ><b><?php echo $i; ?></b></a>
  <?php } ?>
<?php } } ?>
</center>
</div>

<div style="width:100px;float:right;height:15px;">
<?php if($_GET['p']<$last){ ?>
<a class="btn btn-light btn-sm" onclick="<?php if($func_show == 'getCurrentTableFunction()') { echo 'handlePagination('.$last.')'; } else { echo $func_show."('".$last."')"; } ?>" role="button" style="float:right;margin-left:4px;"><b>>></b></a>
<?php } ?>

<?php if(($_GET['p']+1)<=$last){ ?>
<a class="btn btn-light btn-sm" onclick="<?php if($func_show == 'getCurrentTableFunction()') { echo 'handlePagination('.($_GET['p']+1).')'; } else { echo $func_show."('".($_GET['p']+1)."')"; } ?>" role="button" style="float:right;"><b>></b></a>
<?php } ?>
</div>

<div style="width:100%;text-align: center;padding-top:0px;height:10px;vertical-align: middle;float:left;margin-bottom: 30px;font-size:14px;">
  <?php echo $l_time!="" ? ("Loadtime : <b>".$l_time."s</b>") : "";?>
</div>

<?php } ?>

</div>

<?php } ?>

<?php } ?>

<script>
// Fungsi JavaScript untuk menangani pagination pada halaman inv_ecct
function getCurrentTableFunction() {
  if (typeof currentTable !== 'undefined') {
    if (currentTable === 'allitem') {
      return 'showDataAllItem';
    } else if (currentTable === 'ecct') {
      return 'showDataEcct';
    }
  }
  return 'showDataItem';
}

function handlePagination(page) {
  if (typeof currentTable !== 'undefined') {
    if (currentTable === 'allitem') {
      showDataAllItem(page);
    } else if (currentTable === 'ecct') {
      showDataEcct(page);
    }
  } else {
    showDataItem(page);
  }
}
</script>