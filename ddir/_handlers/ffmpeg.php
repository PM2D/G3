<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
$path = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : NULL;
if(FALSE!==strpos($path, ':')) exit;
if(extension_loaded('ffmpeg')){
  $cached = $_SERVER['DOCUMENT_ROOT'].'/var/cache/imgs/'.abs(crc32($path)).'.jpg';
  if(!file_exists($cached)){
    $mov = new ffmpeg_movie($_SERVER['DOCUMENT_ROOT'].urldecode($path));
    $pos = round($mov->getFrameRate()*12);
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
    } else {
      Header('HTTP/1.1 204 No Content', TRUE, 204);
      exit;
    }
  };
  header('Content-Type: image/jpeg');
  readfile($cached);
};
?>