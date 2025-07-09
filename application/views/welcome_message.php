<div class="row">
    <div class="col-xs-12 col-md-3"></div>

        <div class="col-xs-12 col-md-6" id="inti" style="margin-top:calc((100vh - 680px)/2);">
          <center style="margin-bottom:20px;">
            <a href="<?php echo $config['base_url'];?>">
            <img src="images/logo_banner.png" width="45%"/>
            </a>
          </center>

<?php if($info['jenis']!=null){ ?>
<div class="alert alert-default" role="alert" style="background-color:#F8F8F8;">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  <strong><?php echo $info['judul']; ?></strong> <?php echo $info['pesan']; ?>
</div>
<?php } ?>

      <div class="list-group" style="margin-bottom:5px;">
        <li class="list-group-item" style="font-size:16px;background-color:#337AB7;border-color:#477BB5;font-size:14px;color:#FFF;">
          <span class="glyphicon glyphicon-export"></span> <b>Data Export</b>
        </li>
        
        <li class="list-group-item" style="background-color:#F8F8F8;">
          <div class="row">
            <div class="col-xs-12 col-md-12" style="">
               <label>Menu List</label>
                  <select class="form-control" id="menu_list" onChange="inputMenu();">
                    <option value=""></option>
                    <option value="delivery_order">Delivery Order</option>
                    <option value="recap_invoice">Recap Invoice</option>
                    <option value="delivery_note">Delivery Note</option>
                    <option value="delivery_name">Delivery Name</option>
                    <option value="delivery_address">Delivery Address</option>
                  </select>
            </div>

            <!-- RECAP INVOICE -->
            <div id="ri_input" style="display:none;">
              <div class="col-xs-12 col-md-12" style="">
                <div class="page-header" style="margin:10px 0 10px 0;border-color:#CCC;"></div>
              </div>
              <div class="col-xs-12 col-md-12" style="">
                <div class="row" style="display:;">
                    <div class="col-xs-12 col-md-12" style="">
                      <div class="row" style="display:;">
                        <div class="col-xs-12 col-md-4" style="">
                          <label>Date From</label>
                          <input onChange="inputValueRI();"  type="date" class="form-control" name="date_from" id="date_from" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-xs-12 col-md-4" style="">
                           <label>Date To</label>
                            <input onChange="inputValueRI();"  type="date" class="form-control" name="date_to" id="date_to" value="<?php echo date('Y-m-d'); ?>">
                        </div> 
                        <div class="col-xs-12 col-md-4" style="">
                          <label>Invoice Release</label>
                          <input onKeyDown="inputValueRI();" onChange="inputValueRI();" type="date" class="form-control" name="invoice_release" id="invoice_release" value="<?php echo date('Y-m-d'); ?>">
                        </div>  
                        <div class="col-xs-12 col-md-4" style="">
                           <label>Patient Code</label>
                            <input onKeyDown="inputValueRI();" onChange="inputValueRI();" type="text" class="form-control" name="patient_code" id="patient_code">
                        </div>
                        <div class="col-xs-12 col-md-4" style="">
                           <label>Patient NIP</label>
                            <input onKeyDown="inputValueRI();" onChange="inputValueRI();" type="text" class="form-control" name="patient_nip" id="patient_nip">
                        </div>
                        <div class="col-xs-12 col-md-4" style="">
                          <label>Language</label>
                          <select onChange="inputValueRI();" type="date" class="form-control" name="lang" id="lang">
                            <option value="EN">English</option>
                            <option value="JP">Japan</option>
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="col-xs-12 col-md-12" style="">
                        <label>Shipping Cost</label>
                        <textarea style="height:50px;" onKeyDown="inputValueRI();" onChange="inputValueRI();" class="form-control" name="scost" id="scost" placeholder="Format : kode/nip-biaya; 0102/7014-12.16;"></textarea>
                    </div> 
                </div>
              </div>

              <div class="col-xs-12 col-md-12" style="">
                <div class="row">
                  <div class="col-xs-12 col-md-12" style="">
                  <div class="row" style="display:;">
                      <div class="col-xs-12 col-md-6" style="display:none;">
                        <label>To</label>
                        <input onKeyDown="inputValueRI();" onChange="inputValueRI();" type="text" class="form-control" name="tomr" id="tomr">
                      </div>  
                      <div class="col-xs-12 col-md-4" style="display:none;">
                        <label>Periode</label>
                        <input onKeyDown="inputValueRI();" onChange="inputValueRI();" type="text" class="form-control" name="periode" id="periode">
                      </div>
                      
                   </div>
                  </div>
                  <div class="col-xs-12 col-md-4" style="display:none;">
                    <label>Address</label>
                    <textarea style="height:93px;" onKeyDown="inputValueRI();" onChange="inputValueRI();" class="form-control" name="address" id="address"></textarea>
                  </div> 
              </div>   
            </div>
          </div>
          <!-- END RECAP INVOICE -->

          <!-- DELIVERY NOTE -->
          <div id="dn_input" style="display:none;">
            <div class="col-xs-12 col-md-12" style="">
              <div class="page-header" style="margin:10px 0 10px 0;border-color:#CCC;"></div>
            </div>
            <div class="col-xs-12 col-md-6">
              <label>NIP</label>
              <input type="text" class="form-control" id="dn_nip" value="" onBlur="reqInvoiceDateDN();">
            </div>
            <div class="col-xs-8 col-md-6">
              <label>Invoice Date</label>
              <select class="form-control" id="dn_date" onChange="inputValueDN();">
              </select>
            </div>
          </div>
          <!-- END DELIVERY NOTE -->

          <!-- DELIVERY NAME -->
          <div id="dnm_input" style="display:none;">
            <div class="col-xs-12 col-md-12" style="">
              <div class="page-header" style="margin:10px 0 10px 0;border-color:#CCC;"></div>
            </div>
            <div class="col-xs-12 col-md-12">
              <label>NIP</label>
              <input type="text" class="form-control" id="dnm_nip" value="" onBlur="inputValueDNM();" onKeyDown="inputValueDNM();">
            </div>
          </div>
          <!-- END DELIVERY NAME -->

          <!-- DELIVERY ADDRESS -->
          <div id="da_input" style="display:none;">
            <div class="col-xs-12 col-md-12" style="">
              <div class="page-header" style="margin:10px 0 10px 0;border-color:#CCC;"></div>
            </div>
            <div class="col-xs-12 col-md-12">
              <label>NIP</label>
              <input type="text" class="form-control" id="da_nip" value="" onBlur="inputValueDA();" onKeyDown="inputValueDA();">
            </div>
          </div>
          <!-- END DELIVERY ADDRESS -->


        </li>

        <li class="list-group-item" style="font-size:16px;background-color:#EEE;font-size:14px;color:#63317B;height:55px;">
            <div id="ri_action" style="display:none;float:left;">
              <a href="#" id="link_save" onClick="inputValueRI();"><button class="btn btn-primary" type="button" onClick="reqContent();"><span class="glyphicon glyphicon-floppy-save"></span> <b>Save</b></button></a>
              <a href="#" id="link_print" target="" onClick="inputValueRI();"><button class="btn btn-primary" type="button" onClick="reqContent();"><span class="glyphicon glyphicon-print"></span> <b>Print</b></button></a>
              <a href="#" id="link_printb" target="" onClick="inputValueRI();"><button class="btn btn-primary" type="button" onClick="reqContent();"><span class="glyphicon glyphicon-print"></span> <b>Print B</b></button></a>
            </div>

            <div id="dn_action" style="display:none;float:left;">
              <a href="#" id="link_dn" target="" onClick="inputValueDN();"><button class="btn btn-primary" type="button" onClick="reqContentDN();"><span class="glyphicon glyphicon-print"></span> <b>Print Delivery Note</b></button></a>
            </div>

            <div id="dnm_action" style="display:none;float:left;">
              <a href="#" id="link_dnm" target="" onClick="inputValueDNM();"><button class="btn btn-primary" type="button" onClick="reqContentDNM();"><span class="glyphicon glyphicon-print"></span> <b>Print Delivery Name</b></button></a>
            </div>

            <div id="da_action" style="display:none;float:left;">
              <a href="#" id="link_da" target="" onClick="inputValueDA();"><button class="btn btn-primary" type="button" onClick="reqContentDA();"><span class="glyphicon glyphicon-print"></span> <b>Print Delivery Address</b></button></a>
            </div>

            <div class="btn-group dropup" style="float:right;">
              <button  class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" type="button">Custom <span class="caret"></span></button>
              <ul class="dropdown-menu dropdown-menu-right">
                <li><a  data-toggle="modal" data-target="#Delivery_Data">Delivery Data</a></li>
              </ul>
            </div>
        </li>
        

      </div>
        </div>

        <div class="col-xs-12 col-md-3"></div>

</div>

<div id="content">
</div>

<!-- Modal -->
<div class="modal fade" id="Delivery_Data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Delivery Data</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12 col-md-5">
            <label>NIP</label>
            <input type="text" class="form-control" name="dd_nip" id="dd_nip" value="" onBlur="reqInvoiceDate();">
          </div>
          <div class="col-xs-8 col-md-4">
            <label>Invoice Date</label>
            <select class="form-control" name="dd_date" id="dd_date" onChange="reqDeliveryData();">
            </select>
          </div>
          <div class="col-xs-12 col-md-3">
            <label>Search</label>
            <button class="btn btn-default" type="button" onClick="reqDeliveryData();"><span class="glyphicon glyphicon-search"></span>  Search</button>
          </div>
          
          <div class="col-xs-12 col-md-12" id="req_delivery_data"></div>
          <div class="col-xs-12 col-md-12" id="req_delivery_data_save"></div>
        </div>
          
         
      </div>
      <div class="modal-footer">
       
      </div>
    </div>
  </div>
</div>


<script language="JAVASCRIPT">

function inputMenu(){
  var menu_list = document.getElementById("menu_list").value;

  hideMenu();

  if(menu_list=="recap_invoice"){
    document.getElementById("ri_input").style.display="";
    document.getElementById("ri_action").style.display="";
  }else if(menu_list=="delivery_note"){
    document.getElementById("dn_input").style.display="";
    document.getElementById("dn_action").style.display="";
  }else if(menu_list=="delivery_name"){
    document.getElementById("dnm_input").style.display="";
    document.getElementById("dnm_action").style.display="";
  }else if(menu_list=="delivery_address"){
    document.getElementById("da_input").style.display="";
    document.getElementById("da_action").style.display="";
  }else if(menu_list=="delivery_order"){
    window.location.replace(window.location.href+"order");
  }

}

function hideMenu(){
  document.getElementById("ri_input").style.display="none";
  document.getElementById("ri_action").style.display="none";
  document.getElementById("dn_input").style.display="none";
  document.getElementById("dn_action").style.display="none";
  document.getElementById("dnm_input").style.display="none";
  document.getElementById("dnm_action").style.display="none";
  document.getElementById("da_input").style.display="none";
  document.getElementById("da_action").style.display="none";
}

function inputValueDN(){
      var dn_nip = document.getElementById("dn_nip").value;
      var dn_date = document.getElementById("dn_date").value;

      if(dn_nip!='' && dn_date!=''){
        for(var i=0;i<10;i++){
          dn_nip=dn_nip.replace('/','%2D');
        }

        document.getElementById("link_dn").href="data/delivery_note_print/"+dn_nip+"/"+dn_date;
        document.getElementById("link_dn").target="_blank";
      }else{
        document.getElementById("link_dn").href="#";
        document.getElementById("link_dn").target="";
      }
}

function inputValueDNM(){
      var dnm_nip = document.getElementById("dnm_nip").value;

      if(dnm_nip!=''){
        for(var i=0;i<10;i++){
          dnm_nip=dnm_nip.replace('/','%2D');
        }

        document.getElementById("link_dnm").href="data/delivery_name_print/"+dnm_nip;
        document.getElementById("link_dnm").target="_blank";
      }else{
        document.getElementById("link_dnm").href="#";
        document.getElementById("link_dnm").target="";
      }
}

function inputValueDA(){
      var da_nip = document.getElementById("da_nip").value;

      if(da_nip!=''){
        for(var i=0;i<10;i++){
          da_nip=da_nip.replace('/','%2D');
        }

        document.getElementById("link_da").href="data/delivery_address_print/"+da_nip;
        document.getElementById("link_da").target="_blank";
      }else{
        document.getElementById("link_da").href="#";
        document.getElementById("link_da").target="";
      }
}

function inputValueRI(){
      var patient_code = document.getElementById("patient_code").value;
      var date_from = document.getElementById("date_from").value;
      var date_to = document.getElementById("date_to").value;
      var patient_nip = document.getElementById("patient_nip").value;
      var scost = document.getElementById("scost").value;

      var tomr = document.getElementById("tomr").value;
      var address = document.getElementById("address").value;
      var periode = document.getElementById("periode").value;
      var invoice_release = document.getElementById("invoice_release").value;
      var lang = document.getElementById("lang").value;

      address = address.replace(/(?:\r\n|\r|\n)/g, '<br/>');

      if(patient_code!='' && date_from!='' && date_to!=''){
        document.getElementById("link_save").href="data/recap_invoice/"+patient_code+"/"+date_from+"/"+date_to+"/"+patient_nip+"?scost="+scost;
        document.getElementById("link_print").href="data/recap_invoice_print/"+patient_code+"/"+date_from+"/"+date_to+"/"+patient_nip+"?scost="+scost+"&tomr="+tomr+"&address="+address+"&periode="+periode+"&invoice_release="+invoice_release+"&lang="+lang+"";
        document.getElementById("link_print").target="_blank";
        document.getElementById("link_printb").href="data/recap_invoice_printb/"+patient_code+"/"+date_from+"/"+date_to+"/"+patient_nip+"?scost="+scost+"&tomr="+tomr+"&address="+address+"&periode="+periode+"&invoice_release="+invoice_release+"&lang="+lang+"";
        document.getElementById("link_printb").target="_blank";
      }else{
        document.getElementById("link_save").href="#";
        document.getElementById("link_print").href="#";
        document.getElementById("link_print").target="";
        document.getElementById("link_printb").href="#";
        document.getElementById("link_printb").target="";
      }

      var offsetHeight = document.getElementById('inti').offsetHeight;
      //alert(offsetHeight);
}

function reqDeliveryData(){
      var nip = document.getElementById("dd_nip").value;
      var date = document.getElementById("dd_date").value;

      if(nip!="" && date!=""){
        for(var i=0;i<10;i++){
          nip=nip.replace('/','%2D');
        }

        $("#req_delivery_data").load("req/delivery_data/"+nip+"/"+date);
      }else{
        alert("Please input valid data");
      }
     
}

function reqInvoiceDate(){
      var nip = document.getElementById("dd_nip").value;

      for(var i=0;i<10;i++){
        nip=nip.replace('/','%2D');
      }
      document.getElementById("dd_date").innerHTML="";
      closeDeliveryData();
      $("#dd_date").load("req/date_invoice/"+nip);
}

function reqInvoiceDateDN(){
      var nip = document.getElementById("dn_nip").value;

      for(var i=0;i<10;i++){
        nip=nip.replace('/','%2D');
      }
      document.getElementById("dn_date").innerHTML="";
      $("#dn_date").load("req/date_delivery/"+nip);
}

function closeDeliveryData(){
      document.getElementById("req_delivery_data").innerHTML="";
      document.getElementById("req_delivery_data_save").innerHTML="";
}

function reqDeliveryDataSave(){
      var dd_datein = document.getElementById("dd_datein").value;
      var dd_idwo = document.getElementById("dd_idwo").value;

      $("#req_delivery_data_save").load("req/delivery_data_save/"+dd_datein+"/"+dd_idwo);
}


</script>