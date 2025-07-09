      </main>
     </div>
     <!-- Footer -->
     <footer class="footer">
      <!-- Container -->
      <div class="container-fixed">
       <div class="flex flex-col md:flex-row justify-center md:justify-between items-center gap-3 py-5">
        <div class="flex order-2 md:order-1 gap-2 font-normal text-2sm">
         <span class="text-gray-500">
          <?php echo "".date('Y'); ?>©
         </span>
         <a class="text-gray-600 hover:text-primary" href="https://edwarmedika.com" target="_blank">
          <b>C-Techlabs</b> x <b>Edwar Medika</b>
          | System 2.1 | Load Time : <?php echo $l_time; ?>s
         </a>
        </div>
        <nav class="flex order-1 md:order-2 gap-4 font-normal text-2sm text-gray-600">
         <a class="hover:text-primary" href="#">
          Support
         </a>
        </nav>
       </div>
      </div>
      <!-- End of Container -->
     </footer>
     <!-- End of Footer -->
    </div>
    <!-- End of Main -->
   </div>
   <!-- End of Wrapper -->
  </div>
  <!-- End of Base -->

<div style="display:none;" id="loading">
  <center style="padding: 100px;"><img src="<?php echo $config['base_url']."images/loading_edwar.gif"; ?>" width="200px;"></center>
</div>


<!-- Scripts -->
  <script src="<?php echo $config['base_url']; ?>assets/js/core.bundle.js">
  </script>
  <script src="<?php echo $config['base_url']; ?>assets/vendors/apexcharts/apexcharts.min.js">
  </script>
  <script src="<?php echo $config['base_url']; ?>assets/js/widgets/general.js">
  </script>
  <!-- End of Scripts -->

  <script>
$('.calendar').datetimepicker({
  timepicker:false,
  format:'Y-m-d',
  defaultTime:'00:00'
});
</script>

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

  $("#key").keyup(function(event) {
    if (event.keyCode === 13) {
        $("#btn_search").click();
    }
  });
</script>

  <script>window.jQuery || document.write('<script src="<?php echo $config['base_url']; ?>css/jquery.min.js"><\/script>')</script>

 </body>

 </html>