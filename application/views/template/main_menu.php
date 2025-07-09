<div class="row" style="border-top:1px solid #DDD;">

<div class="col-xs-12 col-lg-2 hidden-md hidden-xs hidden-sm" style="margin-bottom:-20px;height: calc(100vh - 120px - 60px);" id="main_menu_content">
  <div class="row" style="">
    <div class="list-group" style="padding-right:0px;">

      <div class="list-group-item menu_clear" 
      style="padding-top: 10px;border-radius:0px;border:0px solid #FFF;">
        <div class="row">
          <div class="col-xs-3 col-md-3" style="padding-right: 0px;margin-bottom: 10px;margin-top: 5px;">

            <?php if($_SESSION['ccare']['id_user']=="108"){ ?>
              <img class="img-rounded" src="<?php echo $config['base_url']; ?>images/admin/108.jpg"
            width="100%"/>
            <?php }else{ ?>
            <img class="img-rounded" src="<?php echo $config['base_url']; ?>images/icon/user.png"
            width="100%"/>
            <?php } ?>


          </div>
          <div class="col-xs-8 col-md-9" style="">
            <h4 style="margin-top: 5px;margin-bottom: 0px;font-size:18px;">
              <div style="font-size:14px;margin-bottom: 5px;margin-top: 5px;">Wellcome</div>
              <div><?php echo $config['user']['full_name']; ?></div></h4>
            <a href="<?php echo $config['base_url']."user"; ?>">
              <button type="button" class="btn btn-xs btn-success" style="width:47%;margin-right:4%;float: left;padding-top:3px; ">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Setting
              </button>
            </a>
            <a href="<?php echo $config['base_url']."login"; ?>">
              <button type="button" class="btn btn-xs btn-danger" style="width:47%;float: left;padding-top:3px;">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span> Logout
              </button>
            </a>
          </div>
        </div>
      </div>

      <style type="text/css">
        .lbl_menu {font-size:12px;height:30px;padding-top:7px;background-color:#FFF;border-radius: 0px;}
      </style>


      <?php
        $dis_save=json_decode($config['user']['display_panel'],true);
      ?>

      <div style="" id="accordion22" role="tablist" aria-multiselectable="true">
        <li href="#" class="list-group-item" style="display: none;"></li>



        <?php if($config['access']['management']==true && isset($dis_save['management']) ){ ?>
          <li href="#" class="list-group-item menu_clear lbl_menu" style="" onclick="clickMenu('p_management');">
            <b>Management Panel</b>
            <b class="right btn btn-xs btn-default" id="p_ccare_btn" >
              <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
            </b>
          </li>

          <x id="p_management" style="display: none;">
            <li href="#" class="list-group-item" style="display: none;"></li>

            <a href="<?php echo $config['base_url']."management/ecct_ln"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="management" && ($config['hal_sub']=="ecct_ln")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> ECCT LN</a>

            <a href="<?php echo $config['base_url']."management/ecbs"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="management" && ($config['hal_sub']=="ecbs")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> ECBS</a>

            <li href="#" class="list-group-item" style="display: none;"></li>
          </x>
        <?php } ?>

        <?php if($config['access']['ccare_management']==true && isset($dis_save['ccare_management']) ){ ?>
          <li href="#" class="list-group-item menu_clear lbl_menu" style="" onclick="clickMenu('p_ccare_management');">
            <b>C-Care Management Panel</b>
            <b class="right btn btn-xs btn-default" id="p_ccare_btn" >
              <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
            </b>
          </li>

          <x id="p_ccare_management" style="display: none;">
            <li href="#" class="list-group-item" style="display: none;"></li>

            <a href="<?php echo $config['base_url']."ccare_management/dashboard"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="ccare_management" && ($config['hal_sub']=="dashboard")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> Dashboard</a>

            <li href="#" class="list-group-item" style="display: none;"></li>
          </x>
        <?php } ?>

        

        <?php if($config['access']['executive']==true && isset($dis_save['executive']) ){ ?>
          <li href="#" class="list-group-item menu_clear lbl_menu" style="" onclick="clickMenu('p_executive');">
            <b data-toggle="collapse" data-parent="#accordion22" href="#collapseOne22" aria-expanded="true" aria-controls="collapseOne22">Executive Panel</b>
            <b class="right btn btn-xs btn-default" id="p_executive_btn">
              <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
            </b>
          </li>

          <x id="p_executive" style="display: none;">
              <li href="#" class="list-group-item" style="display: none;"></li>
              <a href="<?php echo $config['base_url']."executive/knowledge"; ?>" class="list-group-item menu_clear 
              <?php if($config['hal']=="executive" && ($config['hal_sub']=="knowledge")){ echo "menu_active"; }?> ">
              <span class="glyphicon glyphicon-apple" aria-hidden="true"></span> ECCT Dashboard</a>

              <a href="<?php echo $config['base_url']."executive/active_users"; ?>" class="list-group-item menu_clear 
              <?php if($config['hal']=="executive" && ($config['hal_sub']=="active_users")){ echo "menu_active"; }?> ">
              <span class="glyphicon glyphicon-user" aria-hidden="true"></span> ECCT Active Users</a>

              <a href="<?php echo $config['base_url']."executive/patientlist"; ?>" class="list-group-item menu_clear 
              <?php if($config['hal']=="executive" && (in_array($config['hal_sub'], array("patientlist","patientdetails")) )){ echo "menu_active"; }?> ">
              <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> Patient List</a>

              <li href="#" class="list-group-item" style="display: none;"></li>
          </x>
        <?php } ?>

        <?php if($config['access']['doctor']==true && isset($dis_save['doctor']) ){ ?>
          <li href="#" class="list-group-item menu_clear lbl_menu" style="" onclick="clickMenu('p_doctor');">
            <b data-toggle="collapse" data-parent="#accordion22" href="#collapseOne22" aria-expanded="true" aria-controls="collapseOne22">Doctor Panel</b>
            <b class="right btn btn-xs btn-default" id="p_doctor_btn">
              <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
            </b>
          </li>

          <x id="p_doctor" style="display: none;">
              <li href="#" class="list-group-item" style="display: none;"></li>
              <a href="<?php echo $config['base_url']."doctor/filtering"; ?>" class="list-group-item menu_clear 
              <?php if($config['hal']=="doctor" && ($config['hal_sub']=="filtering")){ echo "menu_active"; }?> ">
              <span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Patient Filtering</a>

              <a href="<?php echo $config['base_url']."doctor/patientlist"; ?>" class="list-group-item menu_clear 
              <?php if($config['hal']=="doctor" && (in_array($config['hal_sub'], array("patientlist","patientdetails")) )){ echo "menu_active"; }?> ">
              <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> Patient List</a>

              <li href="#" class="list-group-item" style="display: none;"></li>
          </x>
        <?php } ?>


        <?php if($config['access']['fismed']==true && isset($dis_save['fismed'])){ ?>
          <li href="#" class="list-group-item menu_clear lbl_menu" style="" onclick="clickMenu('p_fismed');">
            <b data-toggle="collapse" data-parent="#accordion22" href="#collapseOne22" aria-expanded="true" aria-controls="collapseOne22">Fismed Panel</b>
            <b class="right btn btn-xs btn-default" id="p_fismed_btn">
              <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
            </b>
          </li>

          <x id="p_fismed" style="display: none;">
              <li href="#" class="list-group-item" style="display: none;"></li>

              <a href="<?php echo $config['base_url']."fismed/knowledge"; ?>" class="list-group-item menu_clear 
              <?php if($config['hal']=="fismed" && ($config['hal_sub']=="knowledge")){ echo "menu_active"; }?> ">
              <span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> ECCT Dashboard</a>

              <a href="<?php echo $config['base_url']."fismed/active_users"; ?>" class="list-group-item menu_clear 
              <?php if($config['hal']=="fismed" && ($config['hal_sub']=="active_users")){ echo "menu_active"; }?> ">
              <span class="glyphicon glyphicon-user" aria-hidden="true"></span> ECCT Active Users</a>
              
              <a href="<?php echo $config['base_url']."fismed/patientlist"; ?>" class="list-group-item menu_clear 
              <?php if($config['hal']=="fismed" && (in_array($config['hal_sub'], array("patientlist","patientdetails")) )){ echo "menu_active"; }?> ">
              <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> Patient List</a>

              <a href="<?php echo $config['base_url']."fismed/survivor"; ?>" class="list-group-item menu_clear 
              <?php if($config['hal']=="fismed" && ($config['hal_sub']=="survivor")){ echo "menu_active"; }?> ">
              <span class="glyphicon glyphicon-grain" aria-hidden="true"></span> 5 Years Survivor</a>
              <li href="#" class="list-group-item" style="display: none;"></li>

          </x>
        <?php } ?>


        <?php if($config['access']['visiting_researcher']==true && isset($dis_save['visiting_researcher'])){ ?>
          <li href="#" class="list-group-item menu_clear lbl_menu" style="" onclick="clickMenu('p_visiting_researcher');">
            <b data-toggle="collapse" data-parent="#accordion22" href="#collapseOne22" aria-expanded="true" aria-controls="collapseOne22">Visiting Researcher Panel</b>
            <b class="right btn btn-xs btn-default" id="p_visiting_researcher_btn">
              <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
            </b>
          </li>

          <x id="p_visiting_researcher" style="display: none;">
              <li href="#" class="list-group-item" style="display: none;"></li>
              <a href="<?php echo $config['base_url']."visiting_researcher/patientlist"; ?>" class="list-group-item menu_clear 
              <?php if($config['hal']=="visiting_researcher" && (in_array($config['hal_sub'], array("patientlist","patientdetails")) )){ echo "menu_active"; }?> ">
              <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> Patient List</a>
              <li href="#" class="list-group-item" style="display: none;"></li>

          </x>
        <?php } ?>


        <?php if($config['access']['super_01']==true && isset($dis_save['super_01']) ){ ?>
          <li href="#" class="list-group-item menu_clear lbl_menu" style="" onclick="clickMenu('p_super_01');">
            <b data-toggle="collapse" data-parent="#accordion22" href="#collapseOne22" aria-expanded="true" aria-controls="collapseOne22">SuperAdmin #01 Panel</b>
            <b class="right btn btn-xs btn-default" id="p_fismed_btn">
              <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
            </b>
          </li>

          <x id="p_super_01" style="display: none;">
              <li href="#" class="list-group-item" style="display: none;"></li>

              <a href="<?php echo $config['base_url']."super_01/log_medrec_cat"; ?>" class="list-group-item menu_clear 
              <?php if($config['hal']=="super_01" && ($config['hal_sub']=="log_medrec_cat")){ echo "menu_active"; }?> ">
              <span class="glyphicon glyphicon-floppy-save" aria-hidden="true"></span> Log : Medrec Category</a>
              <li href="#" class="list-group-item" style="display: none;"></li>
          </x>
        <?php } ?>


        <?php if($config['access']['consult']==true && isset($dis_save['consult']) ){ ?>
          <li href="#" class="list-group-item menu_clear lbl_menu" style="" onclick="clickMenu('p_consult');">
            <b>Consult Panel</b>
            <b class="right btn btn-xs btn-default" id="p_consult_btn" >
              <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
            </b>
          </li>

          <x id="p_consult" style="display: none;">
            <li href="#" class="list-group-item" style="display: none;"></li>
            <a href="<?php echo $config['base_url']."consult/formlist"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="consult" && ($config['hal_sub']=="formlist")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> Form List</a>
            <li href="#" class="list-group-item" style="display: none;"></li>
          </x>
        <?php } ?>

        <?php if($config['access']['finance']==true && isset($dis_save['finance']) ){ ?>
          <li href="#" class="list-group-item menu_clear lbl_menu" style="" onclick="clickMenu('p_finance');">
            <b>Finance Panel</b>
            <b class="right btn btn-xs btn-default" id="p_finance_btn" >
              <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
            </b>
          </li>

          <x id="p_finance" style="display: none;">
            <li href="#" class="list-group-item" style="display: none;"></li>

            <a href="<?php echo $config['base_url']."finance/dashboard"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="finance" && ($config['hal_sub']=="dashboard")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> Dashboard</a>

            <a href="<?php echo $config['base_url']."finance/invoicelist"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="finance" && ($config['hal_sub']=="invoicelist")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-file" aria-hidden="true"></span> Invoice ECCT</a>

            <a href="<?php echo $config['base_url']."finance/invoice_ecbs"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="finance" && ($config['hal_sub']=="invoice_ecbs")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-file" aria-hidden="true"></span> Invoice ECBS</a>

            <li href="#" class="list-group-item" style="display: none;"></li>
          </x>
        <?php } ?>

        <?php if($config['access']['international']==true && isset($dis_save['international']) ){ ?>
          <li href="#" class="list-group-item menu_clear lbl_menu" style="" onclick="clickMenu('p_international');">
            <b>International Panel</b>
            <b class="right btn btn-xs btn-default" id="p_international_btn" >
              <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
            </b>
          </li>

          <x id="p_international" style="display: none;">
            <li href="#" class="list-group-item" style="display: none;"></li>
            <a href="<?php echo $config['base_url']."international/order"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="international" && ($config['hal_sub']=="order")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-plane" aria-hidden="true"></span> Int. Order</a>
            <li href="#" class="list-group-item" style="display: none;"></li>
          </x>
        <?php } ?>



        


        <?php if($config['access']['ccare']==true && isset($dis_save['ccare']) ){ ?>
          <li href="#" class="list-group-item menu_clear lbl_menu" style="" onclick="clickMenu('p_ccare');">
            <b>C-Care Panel</b>
            <b class="right btn btn-xs btn-default" id="p_ccare_btn" >
              <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
            </b>
          </li>

          <x id="p_ccare" style="display: none;">
            <li href="#" class="list-group-item" style="display: none;"></li>

            <!--
            <a href="<?php echo $config['base_url']."ccare/dashboard"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="ccare" && ($config['hal_sub']=="dashboard")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> Dashboard</a>
            -->

            <a href="<?php echo $config['base_url']."ccare/cus_service"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="ccare" && ($config['hal_sub']=="cus_service")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-phone" aria-hidden="true"></span> Customer Service</a>

            <a href="<?php echo $config['base_url']."ccare/membership"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="ccare" && ($config['hal_sub']=="membership")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Membership</a>

            <a href="<?php echo $config['base_url']."ccare/invoicelist"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="ccare" && ($config['hal_sub']=="invoicelist")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-file" aria-hidden="true"></span> Invoice List</a>
              
            <a href="<?php echo $config['base_url']."ccare/patientlist"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="ccare" && (in_array($config['hal_sub'], array("patientlist","patientdetails")) )){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> Patient List</a>

            <a href="<?php echo $config['base_url']."ccare/klaim_garansi"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="ccare" && ($config['hal_sub']=="klaim_garansi")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Klaim Garansi</a>


            <li href="#" class="list-group-item" style="display: none;"></li>
          </x>
        <?php } ?>

        <?php if($config['access']['csport']==true && isset($dis_save['csport']) ){ ?>
          <li href="#" class="list-group-item menu_clear lbl_menu" style="" onclick="clickMenu('p_csport');">
            <b>cSport Panel</b>
            <b class="right btn btn-xs btn-default" id="p_csport_btn" >
              <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
            </b>
          </li>

          <x id="p_csport" style="display: none;">
            <li href="#" class="list-group-item" style="display: none;"></li>

            <a href="<?php echo $config['base_url']."csport/onlineregis"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="csport" && ($config['hal_sub']=="onlineregis")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-globe" aria-hidden="true"></span> Online Regis</a>

            <a href="<?php echo $config['base_url']."csport/invoice_ecbs"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="csport" && ($config['hal_sub']=="invoice_ecbs")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-file" aria-hidden="true"></span> Invoice List</a>

            <a href="<?php echo $config['base_url']."csport/send_notif"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="csport" && ($config['hal_sub']=="send_notif")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-bullhorn" aria-hidden="true"></span> Pemberitahuan</a>


            <li href="#" class="list-group-item" style="display: none;"></li>
          </x>
        <?php } ?>

        <?php if($config['access']['tools']==true && isset($dis_save['tools']) ){ ?>
          <li href="#" class="list-group-item menu_clear lbl_menu" style="" onclick="clickMenu('p_tools');">
            <b>Tools Panel</b>
            <b class="right btn btn-xs btn-default" id="p_tools_btn">
              <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
            </b>
          </li>

          <x id="p_tools" style="display: none;">
            <li href="#" class="list-group-item" style="display: none;"></li>
            <a href="<?php echo $config['base_url']."tools/clientreminder"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="tools" && ($config['hal_sub']=="clientreminder")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Client Reminder</a>

            <a href="<?php echo $config['base_url']."tools/wa_sender"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="tools" && ($config['hal_sub']=="wa_sender")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> WA Sender</a>

            <a href="<?php echo $config['base_url']."tools/updatecontact"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="tools" && ($config['hal_sub']=="updatecontact")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-phone" aria-hidden="true"></span> Update Contact</a>

            <li href="#" class="list-group-item" style="display: none;"></li>
            <a href="<?php echo $config['base_url']."tools/qrscan"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="tools" && ($config['hal_sub']=="qrscan")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-qrcode" aria-hidden="true"></span> QR Scan</a>

            <?php if($config['access']['fismed']==true){ ?>
            <a href="<?php echo $config['base_url']."tools/medreccheck"; ?>" class="list-group-item menu_clear 
            <?php if($config['hal']=="tools" && ($config['hal_sub']=="medreccheck")){ echo "menu_active"; }?> ">
            <span class="glyphicon glyphicon-picture" aria-hidden="true"></span> Medical Record Check</a>
            <?php } ?>

            <li href="#" class="list-group-item" style="display: none;"></li>
          </x>
        <?php } ?>

        <?php $lang=isset($_SESSION['ccare']['lang']) ? $_SESSION['ccare']['lang'] : "en"; ?>
          <li href="#" class="list-group-item menu_clear lbl_menu" style="padding-top:10px;height:50px; 
          <?php echo (isset($dis_save['language']) ) ? "" : "display:none;"; ?>">
            <b>Language</b>

            
            <select class="right form-control" style="padding-top:-5px;height:30px;width:100px;font-size:12px;" 
            id="select_language" onchange="selectLanguage();">
              <option value="ori" <?php echo $lang=="ori" ? "selected" : ""; ?>>Original</option>
              <option value="en" <?php echo $lang=="en" ? "selected" : ""; ?>>English</option>
              <option value="tr" <?php echo $lang=="tr" ? "selected" : ""; ?>>Turkey</option>
              <option value="ja" <?php echo $lang=="ja" ? "selected" : ""; ?>>Japanese</option>
              <option value="id" <?php echo $lang=="id" ? "selected" : ""; ?>>Indonesia</option>
            </select>


            <div id="google_translate_element" style="display: none;"></div>
            <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
          </li>


          <li href="#" class="list-group-item menu_clear lbl_menu hidden-sm hidden-xs" style="padding-top:10px;height:50px;">
            <button class="btn btn-sm btn-default" style="width:100%;" data-toggle="modal" data-target="#panel_display">
            Panel Display</button>
          </li>


      </div>
    </div>
  </div>
</div>

<!-- Modal -->
 
<div class="modal fade" id="panel_display" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <form action="" method="POST">
    <div class="modal-content">
      <div class="modal-header">
        Panel Display
      </div>
      <div class="modal-body" style="padding-top:-20px;padding-bottom:-20px;">
        <?php for ($i=0;$i<sizeof($config['all_panel']);$i++) { ?>
        <?php $pnl_name="display_panel_".$config['all_panel'][$i]; ?>
        <?php if($config['access'][$config['all_panel'][$i]]==true ){ ?>
          <div style="font-size:18px;vertical-align: middle;">
            <input type="checkbox" style="width:20px;height:20px;" 
            name="<?php echo $pnl_name; ?>" id="<?php echo $pnl_name; ?>" 
            <?php echo isset($dis_save[$config['all_panel'][$i]]) ? "checked" : ""; ?>> 
            <b onclick="checkByID('<?php echo $pnl_name; ?>')"> Panel <?php echo ucfirst($config['all_panel'][$i]); ?></b>
          </div>
        <?php } ?>
        <?php } ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" style="float: left;">Close</button>
        <input type="submit" class="btn btn-default" name="display_panel_save" value="Save" />
      </div>
    </div>
    </form>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="main_menu_v2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body" style="padding-top:-20px;padding-bottom:-20px;" id="main_menu_v2">
        <div class="row" style="padding-left:10px;padding-right: 10px;">

          <?php
            $arr_menu=array("ccare","csport");

            $m_sub['ccare']   =array("dashboard","cus_service");
            $m_icon['ccare']  =array("dashboard","phone");
            $m_title['ccare'] =array("Dashboard","Customer Service");

            $m_sub['csport']  =array("onlineregis");
            $m_icon['csport'] =array("globe");
            $m_title['csport']  =array("Online Regis");

          ?>
          <?php foreach ($arr_menu as $menu) { ?>
            <?php if($config['access'][$menu]==true && isset($dis_save[$menu]) ){ ?>
            <div class="col-xs-12 col-md-12"><?php echo $menu; ?></div>
            <?php $m=-1; foreach ($m_sub[$menu] as $sub) { $m++; ?>
              <div class="col-xs-6 col-md-4" style="padding:5px;">
                <a href="<?php echo $config['base_url'].$menu."/".$sub; ?>">
                <button class="btn btn-lg btn-default" style="width:100%;">
                  <div style="text-align: center;font-size:50px;">
                    <span class="glyphicon glyphicon-<?php echo $m_icon[$menu][$m]; ?>" aria-hidden="true"></span></div>
                  <div style="text-align: center;font-size:18px;"><?php echo $m_title[$menu][$m]; ?></div>
                </button>
                </a>
              </div>
            <?php } ?>
            <?php } ?>
          <?php } ?>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-sm btn-default" style="float: left;width:calc(50% - 4px);" data-toggle="modal" data-target="#panel_display">Panel Display</button>
        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal" style="float: right;width:calc(50% - 4px);">Close</button>
      </div>
    </div>
  </div>
</div>


<div id="icon_open" style="display:none;"><span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span></div>
<div id="icon_close" style="display:none;"><span class="glyphicon glyphicon-menu-up" aria-hidden="true"></span></div>
<div style="display: none;" id="sample_display"></div>

<div class="col-xs-12 col-md-12 col-lg-10" class="container" style="border-left:1px solid #DDD;padding-top:20px;
  min-height: calc(100vh - 0px);background-color: #F3F3F3;">

