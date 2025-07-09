<nav class="navbar navbar-inverse navbar-fixed-top" style="background-color:#2D689D;border-color:#2D689D;">
  <div class="container-fluid" style="padding:0px;">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header" style="width: calc(100% + 10px);">

      <a href="<?php echo $config['base_url']; ?>">
        <img class="hidden-xs hidden-sm" src="<?php echo $config['base_url']; ?>images/ehc_icon_001.png" style="height:30px;margin: 10px 15px 0px 15px;"/>

        <img class="hidden-md hidden-lg hidden-sm" src="<?php echo $config['base_url']; ?>images/ehc_icon_001.png" style="height:22px;margin: 13px 0px 0px 30px;"/>

        <img class="hidden-md hidden-lg hidden-xs" src="<?php echo $config['base_url']; ?>images/ehc_icon_001.png" style="height:30px;margin: 10px 15px 0px 15px;"/>
      </a>


      <button class="btn btn-default hidden-lg hidden-sm" style="float: right;margin:10px 10px 0 0;padding:4px 5px 6px 5px;font-size:20px;" 
       data-toggle="modal" data-target="#main_menu_v2">
        <span class="glyphicon glyphicon-user"></span>
      </button>

      <button class="btn btn-default hidden-md hidden-lg hidden-sm" style="float: right;margin:10px 10px 0 0;padding:4px 5px 6px 5px;font-size:20px;" 
       data-toggle="modal" data-target="#main_menu" onclick="setMainMenu();">
        <span class="glyphicon glyphicon-menu-hamburger"></span>
      </button>

      <button class="btn btn-default hidden-lg hidden-xs" style="float: right;margin:10px 24px 0 0;padding:4px 5px 6px 5px;font-size:20px;" 
       data-toggle="modal" data-target="#main_menu" onclick="setMainMenu();">
        <span class="glyphicon glyphicon-menu-hamburger"></span>
      </button>


      <a href="<?php echo $config['base_url']; ?>">
        <?php if(false){ ?>
        <img class="right hidden-xs hidden-sm" src="<?php echo $config['base_url']; ?>images/c-tech.png" 
        style="height:36px;margin: 7px 25px 0px 0px;background-color:#FFF;padding:9px 10px 5px 10px;border-radius:10px;"/>

        <img class="right hidden-xs hidden-sm" src="<?php echo $config['base_url']; ?>images/ehc.png" 
        style="height:36px;margin: 7px 5px 0px 0px;background-color:#FFF;padding:4px 10px 4px 10px;border-radius:10px;"/>

        <img class="right hidden-xs hidden-sm" src="<?php echo $config['base_url']; ?>images/logo_ccare_color.png" 
        style="height:36px;margin: 7px 5px 0px 15px;background-color:#FFF;padding:5px 10px 5px 10px;border-radius:10px;"/>
        <?php } ?>


        <img class="right hidden-xs hidden-sm" src="<?php echo $config['base_url']; ?>images/ccare_ehc_ctech_white.png" 
        style="height:36px;margin: 7px 5px 0px 15px;padding:5px 20px 5px 10px;border-radius:10px;"/>


        
        <img class="left hidden-md hidden-lg hidden-sm" src="<?php echo $config['base_url']; ?>images/c-tech.png" 
        style="height:28px;margin: 10px 25px 0px 15px;background-color:#FFF;padding:5px 10px 5px 10px;border-radius:10px;"/>

        <img class="right hidden-md hidden-lg hidden-xs" src="<?php echo $config['base_url']; ?>images/c-tech.png" 
        style="height:40px;margin: 5px 25px 0px 15px;background-color:#FFF;padding:5px 10px 5px 10px;border-radius:10px;"/>
      </a>
    </div>

  </div><!-- /.container-fluid -->
</nav>

<!-- Modal -->
<div class="modal fade" id="main_menu" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body" style="padding-top:-20px;padding-bottom:-20px;" id="main_menu_content_m">
      </div>
      <div class="modal-footer">
        <button class="btn btn-sm btn-default" style="float: left;width:calc(50% - 4px);" data-toggle="modal" data-target="#panel_display">Panel Display</button>
        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal" style="float: right;width:calc(50% - 4px);">Close</button>
      </div>
    </div>
  </div>
</div>