<html lang="ar" dir="ltr" base="id-en" class="is-desktop js-global-newsflash-visible translated-rtl">
<head>
    <meta name="google" content="translate">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="theme-color" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="mobile-web-app-capable" content="yes">

    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?php echo $config['base_url']; ?>t.php?d=/images/favicon.png">

    <title><?php echo isset($doc_title) ? $doc_title : "EDWAR Healthcare"; ?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo $config['base_url']; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $config['base_url']; ?>css/custom.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="<?php echo $config['base_url']; ?>css/calendar/jquery.datetimepicker.css"/>
    <script src="<?php echo $config['base_url']; ?>css/calendar/jquery.js"></script>
    <script src="<?php echo $config['base_url']; ?>css/calendar/build/jquery.datetimepicker.full.js"></script>
  
    <link href="<?php echo $config['base_url']; ?>/css/bootstrap-select.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo $config['base_url']; ?>css/preview/blueimp-gallery.min.css">
    <link rel="stylesheet" href="<?php echo $config['base_url']; ?>css/preview/bootstrap-image-gallery.css">

    <style>
      @font-face{font-family:'effra'; src:url(<?php echo $config['base_url']; ?>font/Effra-Regular.woff);}
      @font-face{font-family:'Avenir'; src:url(<?php echo $config['base_url']; ?>font/AvenirLTStd-Light.otf);}


      body {font-family:effra;color: #333;}
      a { font-family: effra; }
      span { font-family: effra; }
      option { font-family: effra; }
      label { margin-top: 10px;margin-bottom: 0px; }

      ::selection { background-color: #204F75; color: white; }
      ::-moz-selection { background-color: #204F75; color: white; }
      .custom-range.disabled-range input[type='range']::-webkit-slider-thumb {display: none;}

      td { vertical-align: middle; text-align: center; }
      th { vertical-align: middle; text-align: center; }
      #td_mid { vertical-align: middle; text-align: center; }
      .td_mid { vertical-align: middle; text-align: center; }
      .left { text-align: left; }

      .right {float: right;}
      .w_100 {width: 100%;text-align: center;}

      #nav_link { color: #FFF; }
      #nav_link:hover { font-weight: bold; }

      .goog-te-banner-frame.skiptranslate {
        display: none !important;
      } 
      body {
        top: 0px !important; 
       }

    </style>

    


</head>
<div id="user_lang" style="display: none;"><?php echo isset($_SESSION['ccare']['lang']) ? $_SESSION['ccare']['lang'] : "" ?></div>

<body style="background:<?php echo isset($bg_color) ? $bg_color : "#FFF"; ?>;" onload="<?php echo isset($onload) ? $onload : ''; ?>">
  <div class="container" style="width: 100%;margin-top: <?php echo isset($mg_top) ? $mg_top : "50"; ?>px;">




