<?php
// The file
//$filename = 'http://localhost/C-Consult/attachment/cconsult_1_fulan_arfaq_20160415121453.jpg';
$filename = $_GET['u'];
$filename = str_replace(" ", "%20", $filename);
$percent = 1; // percentage of resize

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
/*header("Pragma: no-cache");
header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
*/

// Content type
header('Content-type: image/jpeg');

// Get new dimensions
list($width, $height) = getimagesize($filename);
//$new_width = $width * $percent;
//$new_height = $height * $percent;

$new_width = $width;
$new_height = $height;

if(isset($_GET['w'])){
	$new_width = $_GET['w'];
	$new_height = ($new_width*$height)/$width;
}elseif(isset($_GET['h'])){
	$new_height = $_GET['h'];
	$new_width = ($new_height*$width)/$height;
}else{
	//$new_width = $width;
}




/*if($width>7000){
	$new_width = 7000;
	$new_height = ($new_width*$height)/$width;
} else {
	$new_width = $width;
	$new_height = $height;
}*/

// Resample
$image_p = imagecreatetruecolor($new_width, $new_height);
$image = imagecreatefromjpeg($filename);
imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

// Output
imagejpeg($image_p, null, 110);
?>