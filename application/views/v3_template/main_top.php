<!DOCTYPE html>
<html class="h-full" data-theme="true" data-theme-mode="light" dir="ltr" lang="en">
 <head><base href="../../">
  <title>
   <?php echo isset($doc_title) ? $doc_title : "EDWAR Healthcare"; ?>
  </title>
  <meta charset="utf-8"/>
  <meta content="follow, index" name="robots"/>
  <link href="https://127.0.0.1:8001/metronic-tailwind-html/demo8/index.html" rel="canonical"/>
  <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport"/>
  <meta content="" name="description"/>
  <meta content="@keenthemes" name="twitter:site"/>
  <meta content="@keenthemes" name="twitter:creator"/>
  <meta content="summary_large_image" name="twitter:card"/>
  <meta content="Metronic - Tailwind CSS " name="twitter:title"/>
  <meta content="" name="twitter:description"/>
  <meta content="<?php echo $config['base_url']; ?>assets/media/app/og-image.png" name="twitter:image"/>
  <meta content="https://127.0.0.1:8001/metronic-tailwind-html/demo8/index.html" property="og:url"/>
  <meta content="en_US" property="og:locale"/>
  <meta content="website" property="og:type"/>
  <meta content="@keenthemes" property="og:site_name"/>
  <meta content="Metronic - Tailwind CSS " property="og:title"/>
  <meta content="" property="og:description"/>
  <meta content="<?php echo $config['base_url']; ?>assets/media/app/og-image.png" property="og:image"/>
  <link rel="icon" href="<?php echo $config['base_url']; ?>images/favicon.png">
  <link href="<?php echo $config['base_url']; ?>images/favicon.png" rel="icon" sizes="32x32" type="image/png"/>
  <link href="<?php echo $config['base_url']; ?>images/favicon.png" rel="icon" sizes="16x16" type="image/png"/>
  <link href="<?php echo $config['base_url']; ?>images/favicon.png" rel="shortcut icon"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="<?php echo $config['base_url']; ?>assets/vendors/apexcharts/apexcharts.css" rel="stylesheet"/>
  <link href="<?php echo $config['base_url']; ?>assets/vendors/keenicons/styles.bundle.css" rel="stylesheet"/>
  <link href="<?php echo $config['base_url']; ?>assets/css/styles.css" rel="stylesheet"/>



    <link rel="stylesheet" type="text/css" href="<?php echo $config['base_url']; ?>css/calendar/jquery.datetimepicker.css"/>
    <script src="<?php echo $config['base_url']; ?>css/calendar/jquery.js"></script>
    <script src="<?php echo $config['base_url']; ?>css/calendar/build/jquery.datetimepicker.full.js"></script>

    <style type="text/css">
      .right { float:right; }
    </style>
    
 </head>
 <body class="antialiased flex h-full text-base text-gray-700 [--tw-page-bg:#F6F6F9] [--tw-page-bg-dark:var(--tw-coal-200)] [--tw-content-bg:var(--tw-light)] [--tw-content-bg-dark:var(--tw-coal-500)] [--tw-content-scrollbar-color:#e8e8e8] [--tw-header-height:60px] [--tw-sidebar-width:90px] bg-[--tw-page-bg] dark:bg-[--tw-page-bg-dark]"  onload="<?php echo isset($onload) ? $onload : ''; ?>">

  <script>
   const defaultThemeMode = 'light'; // light|dark|system
    let themeMode;

    if ( document.documentElement ) {
      if ( localStorage.getItem('theme')) {
          themeMode = localStorage.getItem('theme');
      } else if ( document.documentElement.hasAttribute('data-theme-mode')) {
        themeMode = document.documentElement.getAttribute('data-theme-mode');
      } else {
        themeMode = defaultThemeMode;
      }

      if (themeMode === 'system') {
        themeMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
      }

      document.documentElement.classList.add(themeMode);
    }
  </script>



  <!-- Base -->
  <div class="flex grow">
   <!-- Header -->
   <header class="flex lg:hidden items-center fixed z-10 top-0 start-0 end-0 shrink-0 bg-[--tw-page-bg] dark:bg-[--tw-page-bg-dark] h-[--tw-header-height]" id="header">
    <!-- Container -->
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
     <a href="html/demo8.html">
      <img class="dark:hidden min-h-[30px]" src="<?php echo $config['base_url']; ?>images/favicon.png" width="30px"/>
      <img class="hidden dark:block min-h-[30px]" src="<?php echo $config['base_url']; ?>images/favicon.png" width="30px"/>
     </a>
     <button class="btn btn-icon btn-light btn-clear btn-sm -me-1" data-drawer-toggle="#sidebar">
      <i class="ki-filled ki-menu">
      </i>
     </button>
    </div>
    <!-- End of Container -->
   </header>
   <!-- End of Header -->
   <!-- Wrapper -->
   <div class="flex flex-col lg:flex-row grow pt-[--tw-header-height] lg:pt-0">

    