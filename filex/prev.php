<?php
// This file is a part of GIII (g3.steelwap.org)
if(!isset($_GET['f']) || empty($_GET['f']) || FALSE!==strpos($_GET['f'], '..')) exit;
$file = $_SERVER['DOCUMENT_ROOT'].'/filex/files/'.$_GET['f'];
if(!file_exists($file)) exit;
$ext = substr($file, -4);
$cached = $_SERVER['DOCUMENT_ROOT'].'/var/cache/imgs/'.md5($file).'.jpg';

if($ext=='.3gp'){

  if(!file_exists($cached)){
    $mov = new ffmpeg_movie($file);
    $pos = $mov->getFrameRate()*12;
    $frame = $mov->getFrame($pos);
    if($frame){
      $ox = $frame->getWidth();
      $oy = $frame->getHeight();
      // расчитываем высоту согласно пропорциям и изменяем размер изображения
      $px = 122;
      $py = round($oy/round($ox/$px, 1));
      $img = imagecreatetruecolor($px, $py);
      imagecopyresampled($img, $frame->ToGdImage(), 0, 0, 0, 0, $px, $py, $ox, $oy);
      imagejpeg($img, $cached);
    };
  };

} else {

  if(!file_exists($cached)){
   if($ext=='.gif'){
    $img1 = imagecreatefromgif($file);
   } elseif($ext=='.jpg' || $ext=='jpeg'){
    $img1 = imagecreatefromjpeg($file);
   } elseif($ext=='.png'){
    $img1 = imagecreatefrompng($file);
   } else exit;
   $ox = imagesx($img1);
   $oy = imagesy($img1);
   $px = 92;
   $py = round($oy/round($ox/$px, 1));
   $img2 = imagecreatetruecolor($px, $py);
   imagecopyresampled($img2, $img1, 0, 0, 0, 0, $px, $py, $ox, $oy);
   imagejpeg($img2, $cached);
  };

};

header('Content-Type: image/jpeg');
readfile($cached);

?>