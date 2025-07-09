  <?php if(isset($info['jenis'])){ ?>
  <div class="alert alert-<?php echo $info['jenis']; ?>" role="alert" style="margin:10px -15px 0px -15px;">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <strong><?php echo $info['judul']; ?></strong> <?php echo $info['pesan']; ?>
  </div>
<?php }  ?>