<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="theme-color" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="mobile-web-app-capable" content="yes">
    <link rel="shortcut icon" sizes="192x192" href="images/favicon.png">

    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?php echo $config['base_url']; ?>t.php?d=/images/favicon.png">

    <title>
      <?php 
        if(isset($doc_title)){
          echo $doc_title;
        } else{
          echo "EDWAR Healthcare";
        }
      ?>
    </title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo $config['base_url']; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $config['base_url']; ?>css/custom.css" rel="stylesheet">
     <link href="<?php echo $config['base_url']; ?>/css/bootstrap-select.min.css" rel="stylesheet">

    <style>
      @font-face{font-family:'effra'; src:url(<?php echo $config['base_url']; ?>font/Effra-Regular.woff);}

      body {font-family:effra;color: #333;}
      a { font-family: effra; }
      span { font-family: effra; }
      option { font-family: effra; }
      label { margin-top: 10px;margin-bottom: 0px; }

      ::selection { background-color: #204F75; color: white; }
      ::-moz-selection { background-color: #204F75; color: white; }
      .custom-range.disabled-range input[type='range']::-webkit-slider-thumb {display: none;}
    </style>


</head>

<body style="width:100%;height:100vh;background-image:url(<?php echo $config['base_url']; ?>images/white_background.jpg);background-size: 100% 100%;background-repeat: no-repeat;background-size: contain;display: table; position: relative; background-size: cover; background-repeat: no-repeat; background-position: center; text-align: center; min-height: 350px;">
  <div class="container" style="width: 100%;margin-top: 50px;">




