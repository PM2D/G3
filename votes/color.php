<?php
// This file is a part of GIII (g3.steelwap.org)
$n = intval(@$_SERVER['QUERY_STRING']);
if(1>$n) $n = 1;
elseif(100<$n) $n = 100;
$r = 2*$n;
$g = 100-$r;
if(0>$g) $g = 0;
$r += 50;
if(250<$r) $r=250;
$img = imagecreatetruecolor(10, 10);
$col = imagecolorallocate($img, $r, $g, 0);
imagefilledrectangle($img, 1, 1, 8, 8, $col);
$col = imagecolorallocate($img, 64, 64, 64);
imagerectangle($img, 0, 0, 9, 9, $col);
header('Cache-Control: no-cache, must-revalidate');
if(function_exists('imagegif')){
  header('Content-Type: image/gif');
  imagegif($img);
}elseif(function_exists('imagejpeg')){
  header('Content-Type: image/jpeg');
  imagejpeg($img);
}elseif(function_exists('imagepng')){
  header('Content-Type: image/x-png');
  imagepng($img);
}
?>