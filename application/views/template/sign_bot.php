<script src="<?php echo $config['base_url']; ?>css/sign/jquery.signaturepad.js"></script>
  <script>
    $(document).ready(function() {
      $('.sigPad').signaturePad({drawOnly:true});
    });
  </script>
  <script src="<?php echo $config['base_url']; ?>css/sign/json2.min.js"></script>