<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
if (!isset($_SERVER['QUERY_STRING']) || empty($_SERVER['QUERY_STRING'])) exit;
$file = $_SERVER['DOCUMENT_ROOT'].urldecode($_SERVER['QUERY_STRING']);
if (file_exists($file) && filesize($file)>3072){
  $ext = substr($file, -4);
  if ($ext=='.gif') {
    $img1 = imagecreatefromgif($file);
  } elseif ($ext=='.jpg' || $ext=='jpeg') {
    $img1 = imagecreatefromjpeg($file);
  } elseif ($ext=='.png') {
    $img1 = imagecreatefrompng($file);
  } else exit;
  $ox = imagesx($img1);
  $oy = imagesy($img1);
  if (isset($_POST['w'])) {
    if (641>$_POST['w'] && 0<$_POST['w']) $px=intval($_POST['w']); else $px=92;
    $py = round($oy / round($ox/$px, 1));
    $img2 = imagecreatetruecolor($px, $py);
    imagecopyresampled($img2, $img1, 0, 0, 0, 0, $px, $py, $ox, $oy);
    header('Content-Type: image/jpeg');
    header('Content-Disposition: ; filename="'.basename($file).'"');
    imagejpeg($img2);
  } else {
    $cached = $_SERVER['DOCUMENT_ROOT'].'/var/cache/imgs/'.abs(crc32($file)).'.jpg';
    if (!file_exists($cached)) {
      $px = 92;
      $py = round($oy / round($ox/$px, 1));
      $img2 = imagecreatetruecolor($px, $py);
      imagecopyresampled($img2, $img1, 0, 0, 0, 0, $px, $py, $ox, $oy);
      imagejpeg($img2, $cached);
    }
    header('Content-Type: image/jpeg');
    readfile($cached);
  }
} else header('Location: '.$file);
?>
