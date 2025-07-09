<style type="text/css">
  select { width: 400px; text-align-last:center; }
</style>

<div class="row">
  <div class="col-xs-12 col-md-4 col-sm-3"></div>

  <div class="col-xs-12 col-md-4 col-sm-6">
    <form action="" method="POST">
      <div style="width:100%;margin-bottom:0px;margin-top:calc((100vh - 450px)/2);margin-bottom: 0px;">
        <div style="width:300px;margin:auto;"><a href="<?php echo $config['base_url']; ?>"><img src="<?php echo $config['base_url']; ?>images/edwar_healthcare.png" width="300px"></a></div>
      </div>

      
      

      <input style="margin-bottom: 15px;text-align: center;font-size:16px;" type="text" class="form-control" name="username" required="" placeholder="Username" 
      value="<?php if(isset($_POST['username'])){ echo $_POST['username'];} ?>">

      <input style="margin-bottom: 15px;text-align: center;font-size:16px;" type="password" class="form-control" name="password" required="" placeholder="Password">


      <div class="form-group">
        <input type="submit" class="form-control btn-primary" name="login" value="LOGIN" style="font-size:16px;height:40px;">
      </div>
    </form>



    <div class="page-header" style="margin:-10px 0 15px 0;border-color:#CCC;"></div>

    <?php if($info['jenis']!=null){ ?>
      <div class="alert alert-<?php echo $info['jenis']; ?>" role="alert" style="text-align: center;">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        <strong><?php echo $info['judul']; ?></strong> <?php echo $info['pesan']; ?>
      </div>
      <?php }  ?>


    <!--
    <a href="<?php echo $config['base_url']; ?>registration" style="text-decoration: none;">
      <button class="form-control btn-default" style="height:40px;font-size:16px;color:#555;">
        <b>DAFTAR</b>
      </button>
    </a>
    -->
    
   


  </div>


  <div class="col-xs-12 col-md-4"></div>
</div>


