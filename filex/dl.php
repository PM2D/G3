<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');
if(!isset($_GET['f'])) exit;
$id = intval($_GET['f']);
$mysql = new mysql;
$file = $mysql->GetRow('*', 'filex_files', '`id`='.$id);
// путь к файлу
$fileurl = '/filex/files/'.$file['cid'].'/'.$file['id'].'.'.$file['type'];
// mime-тип по расширению
switch($file['type']){
  case 'jpg': case 'jpeg': $mime = 'image/jpeg'; break;
  case 'gif': $mime = 'image/gif'; break;
  case 'png': $mime = 'image/png'; break;
  case '3gp': $mime = 'video/3gpp'; break;
  case 'mp4': $mime = 'video/mp4'; break;
  case 'jar': $mime = 'application/java-archive'; break;
  case 'mid': case 'midi': $mime = 'audio/midi'; break;
  case 'mp3': $mime = 'audio/mpeg'; break;
  case 'amr': $mime = 'audio/amr'; break;
  case 'ogg': $mime = 'audio/ogg'; break;
  case 'zip': $mime = 'application/zip'; break;
  default: $mime = 'application/octet-stream'; break;
}
// отдача файла
fstools::download_file($_SERVER['DOCUMENT_ROOT'].$fileurl, $file['title'], $mime);
// обновление количества скачиваний
$mysql->Update('filex_files', array('dloads'=>$file['dloads']+1), '`id`='.$id.' LIMIT 1');

?>