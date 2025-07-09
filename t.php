<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// use a switch statement for basic checking and flexibility  
/*switch($_SERVER['PATH_INFO']) {
    case '/css':
        echo file_get_contents('./test.css');
        exit;
    case '/js':
        echo file_get_contents('./test.js');
        exit;
}*/
echo file_get_contents("./".$_GET['d']);
?>