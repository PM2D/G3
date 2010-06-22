<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

if($USER['jentered']) exit;

$n = intval(@$_SERVER['QUERY_STRING']);

if(1>$n) {
  $n = 1;
} elseif(100<$n) {
  $n = 100;
}

$r = $n;
$g = 100-$r;
if(0>$g) $g = 0;
$r += 20;

if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/ico/'.$USER['icons'].'/vote_gray.gif')) {
  Header('HTTP/1.1 204 No Content', TRUE, 204);
  exit;
}

$img = imagecreatefromgif($_SERVER['DOCUMENT_ROOT'].'/ico/'.$USER['icons'].'/vote_gray.gif');
$colors = imagecolorstotal($img);
for($i=0; $i<$colors; $i++) {
  $color = imagecolorsforindex($img, $i);
  $color['red'] += $r;
  $color['green'] += $g;
  if(250<$color['red']) $color['red'] = 250;
  if(250<$color['green']) $color['green'] = 250;
  imagecolorset($img, $i, $color['red'], $color['green'], $color['blue']);
}

header('Cache-Control: no-cache, must-revalidate');
if(function_exists('imagegif')) {
  header('Content-Type: image/gif');
  imagegif($img);
} elseif(function_exists('imagepng')) {
  header('Content-Type: image/x-png');
  imagepng($img);
} elseif(function_exists('imagejpeg')) {
  header('Content-Type: image/jpeg');
  imagejpeg($img);
}

imagedestroy($img);

?>
