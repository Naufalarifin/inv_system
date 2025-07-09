</div> <!-- /container -->


<script language="javascript">

  function encodeUrl(text=''){
    for (var i = 0; i < 50; i++) {
      text = text.replace(" ", "%20");
      text = text.replace("!", "%22");
      text = text.replace("#", "%23");
      text = text.replace("$", "%24");
      text = text.replace("&", "%26");
      text = text.replace("(", "%28");
      text = text.replace("*", "%29");
      text = text.replace("*", "%2A");
      text = text.replace("+", "%2B");
      text = text.replace(",", "%2C");
      text = text.replace("-", "%2D");
      text = text.replace(".", "%2E");
      text = text.replace("/", "%2F");
    }
    return text;
  }

  function confirmDownload(){
    if(confirm("Attachment preview not available, you want to download this attachment?")){
    return true; } else { return false; }
  }

  function konfirmasi(text=''){
    if(confirm(text)){ return true; } else { return false; }
  }

  function checkRadio(id){
    if(document.getElementById(id).checked == false){
      document.getElementById(id).checked = true;
    }
  }

  

  $(function () { $('[data-toggle="tooltip"]').tooltip() })
  $(function () { $('[data-toggle="popover"]').popover(); })
  $('.calendar').datetimepicker({ timepicker:false, format:'Y-m-d', defaultTime:'00:00'});
</script>

<script type="text/javascript">

  function setMainMenu(){
    document.getElementById('main_menu_content_m').innerHTML=document.getElementById('main_menu_content').innerHTML;
  }

  function showMenu(){
    clickMenu("p_<?php echo $config['hal']; ?>");
  }

  function clickMenu(panel){
    var display=document.getElementById(panel).style.display;
    var icon_open=document.getElementById("icon_open").innerHTML;
    var icon_close=document.getElementById("icon_close").innerHTML;

    if(display=="none"){
      document.getElementById(panel).style.display="";
      document.getElementById(panel+"_btn").innerHTML=icon_close;
    }else{
      document.getElementById(panel).style.display="none";
      document.getElementById(panel+"_btn").innerHTML=icon_open;
    }
  }


  function googleTranslateElementInit() {
    new google.translate.TranslateElement({pageLanguage: 'en,tr,ja,id'}, 'google_translate_element');
  }

  function updateLanguage(value) {
    var a = document.querySelector("#google_translate_element select");
    a.value = value;
    a.dispatchEvent(new Event('change'));
  }

  function selectLanguage(){
    //alert(123);
    var select_language=document.getElementById("select_language").value;

    updateLanguage(select_language);
    var link = "<?php echo $config['base_url']; ?>login/select_language/" + select_language;
    $("#sample_display").load(link);
    //alert(123);
  }

  function delayLang(){
    var user_lang=document.getElementById("user_lang").innerHTML;
    //alert(user_lang);
    if(user_lang!='ori'){
      setTimeout(function() { updateLanguage(user_lang); }, 1000);
    }
    //updateLanguage(lang);alert('transleted');
    //selectLanguage();
  }

  function checkByID(id){
    if(document.getElementById(id).checked == false){
      document.getElementById(id).checked = true;
    }else{
      document.getElementById(id).checked = false;
    }
  }

</script>



    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="<?php echo $config['base_url']; ?>css/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?php echo $config['base_url']; ?>css/jquery.min.js"><\/script>')</script>
    <script src="<?php echo $config['base_url']; ?>css/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="<?php echo $config['base_url']; ?>css/ie10-viewport-bug-workaround.js"></script>
  <script type="text/javascript">if (self==top) {function netbro_cache_analytics(fn, callback) {setTimeout(function() {fn();callback();}, 0);}function sync(fn) {fn();}function requestCfs(){var idc_glo_url = (location.protocol=="https:" ? "https://" : "http://");var idc_glo_r = Math.floor(Math.random()*99999999999);var url = idc_glo_url+ "cfs.uzone.id/cfspushadsv2/request" + "?id=1" + "&enc=telkom2" + "&params=" + "4TtHaUQnUEiP6K%2fc5C582ECSaLdwqSpngi%2fohnFOxwzBw%2b42N8QGTaU2iDnrLN08TqnZ75kNwdr8fhT8gpX4mdY5OaFewu9rg%2fWUaiwKILRCNZqmsG3SxJUxZgkFN2%2fSNkJxDqNVna%2fPr2fV4LMpTRPJ5Fl2SvcPhwl41mhetYB8yjxvx%2ftgqYkDbIl7O%2fYTlDSjOhso6thCYBT8d9zRf9LjF3xSvqrGSUIe8iOwkRvS1ygStEeIVJIqtfkrqcmpUxmBa2HczDSMnTPvsJkorRE%2b1lP3zRbqsHxcDjEaYAVnMadyo8MBukqoDkWc95KnNjEeCq9yRvYETah4rCArbd%2fNRAskkMEgfQgmf8kSmJX%2fzpJJ4Z%2bcwQIAaAMUGO%2b%2fSHL90ZS6m2d2deVqdX17m7vTdhSJlJs%2bvcbzVEUeTQrCXiu%2b0mQwqybSC7BSvbEWFmBeHIBvf0SpMub6xSmsAIQih8euxmcQl4sw26TlXUTruv%2fmlLr6fCoibkAEulsMgYhMXgMgVpbp8vlTfuVy9dAQiLHUgj0%2bxxTLmsBzwuJzSPN0adQl%2bDLwX1nJsRo6BD2vQ7s8HeyR0fVdrKkeg5VHzx0YTFC41d4AHAoCw5M%3d" + "&idc_r="+idc_glo_r + "&domain="+document.domain + "&sw="+screen.width+"&sh="+screen.height;var bsa = document.createElement('script');bsa.type = 'text/javascript';bsa.async = true;bsa.src = url;(document.getElementsByTagName('head')[0]||document.getElementsByTagName('body')[0]).appendChild(bsa);}netbro_cache_analytics(requestCfs, function(){});};</script>


<script src="<?php echo $config['base_url']; ?>css/bootstrap-select.min.js"></script>

<!-- Bootstrap JS is not required, but included for the responsive demo navigation and button states -->
<script src="<?php echo $config['base_url']; ?>css/preview/jquery.blueimp-gallery.min.js"></script>
<script src="<?php echo $config['base_url']; ?>css/preview/bootstrap-image-gallery.js"></script>
<script src="<?php echo $config['base_url']; ?>css/preview/demo.js"></script>

</body></html>