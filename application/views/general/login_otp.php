<style type="text/css">
  select { width: 400px; text-align-last:center; }

  .txt_lbl { margin-bottom: 15px;text-align: center;font-size:16px; }
  .txt_btn { margin-bottom: 15px;text-align: center;font-size:16px; }
</style>

<div class="row">
  <div class="col-xs-12 col-md-4 col-sm-3"></div>

  <div class="col-xs-12 col-md-4 col-sm-6">
      <div style="width:100%;margin-bottom:0px;margin-top:calc((100vh - 450px)/2);margin-bottom: 0px;">
        <div style="width:300px;margin:auto;"><a href="<?php echo $config['base_url']; ?>"><img src="<?php echo $config['base_url']; ?>images/edwar_healthcare.png" width="300px"></a></div>
      </div>

      
      
    <?php if($login_step=="0"){ ?>
    <form action="" method="POST">
      <input type="text" class="form-control txt_lbl" name="username" required="" placeholder="Username" 
      value="<?php if(isset($_POST['username'])){ echo $_POST['username'];} ?>">
      <div class="form-group">
        <input type="submit" class="form-control btn-primary txt_btn" name="send_otp" value="Kirim OTP">
      </div>
    </form>
    <?php } ?>

    <?php if($login_step=="1"){ ?>
    <form action="" method="POST">
      <input type="text" name="username" required="" placeholder="Username" 
      value="<?php if(isset($_POST['username'])){ echo $_POST['username'];} ?>" hidden="">
      <input type="text" class="form-control txt_lbl" name="otp" id="otp" placeholder="Kode OTP" onkeypress="inputOTP('key');" onchange="inputOTP('copas');" onpaste="inputOTP('copas');">
      <div class="form-group" style="display: none;">
        <input type="submit" class="form-control btn-primary txt_btn" name="login" value="Login" id="btn_login" hidden="">
      </div>
      <div class="form-group" id="send_otp" style="display: ;">
        <input type="submit" class="form-control btn-primary txt_btn" name="send_otp" value="Kirim Ulang OTP">
      </div>
    </form>
    <?php } ?>

    <?php if($info['jenis2']!=null){ ?>
      <div class="alert alert-<?php echo $info['jenis2']; ?>" role="alert" style="text-align: left;">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        <strong><?php echo $info['judul2']; ?></strong> <?php echo $info['pesan2']; ?>
      </div>
      <?php }  ?>



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


<script type="text/javascript">
  function inputOTP(mode){
    
    //alert(otp);

    setTimeout(function () { autologin(mode); }, 300);

    document.getElementById("send_otp").style.display = "none";
    
    
    
  }

  function autologin(mode){
    var otp = document.getElementById('otp').value;

    if(otp.length == 0){
      document.getElementById("send_otp").style.display = "";
    }
    
    if(mode=='key'){
      if(otp.length == 4){
        //alert('run key ' + otp);
        document.getElementById('btn_login').click();
      }
    }else{
      if(otp.length == 4){
       // alert('run copas' + otp);
        document.getElementById('btn_login').click();
      }
    }
  }


</script>

