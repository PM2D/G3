<?php
// This file is a part of GIII (g3.steelwap.org)
$arr['handler'] = '/ddir'.$arr['path'];
switch($arr['type']) {
  case 'jpeg':
  case 'jpg':
  case 'gif':
  case 'png':
   $arr['handler'] = '/ddir/_handlers/img.php?f='.$arr['id'];
  break;
  case 'zip':
   $arr['handler'] = $CFG['DDIR']['unzip'] ? '/ddir/_handlers/zip.php?f='.$arr['id'] : $arr['handler'].'?';
  break;
  case 'jar':
   $arr['handler'] = '/ddir/_handlers/java.php?f='.$arr['id'];
  break;
  case 'txt':
   $arr['handler'] = '/ddir/_handlers/reader.php?f='.$arr['id'];
  break;
  case 'mp3':
   $arr['handler'] = '/ddir/_handlers/mp3.php?f='.$arr['id'];
  break;
  case '3gp':
  case 'mp4':
  case 'flv':
  case 'ogm':
  case 'avi':
  case 'mpg':
  case 'wmv':
  case 'mkv':
   $arr['handler'] = $CFG['DDIR']['ffmpeg'] ? '/ddir/_handlers/video.php?f='.$arr['id'] : $arr['handler'].'?';
  break;
  case 'thm':
   $arr['handler'] = '/ddir/_handlers/thm.php?f='.$arr['id'];
  break;
  default:
   $arr['handler'] = $arr['handler'].'?';
  break;
}
?>