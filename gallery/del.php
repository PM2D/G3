<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$id =& getvar('i');
$id = intval($id);

$mysql = new mysql;

if(!$id) raise_error('???');

if(3==$USER['id']) raise_error('Дocтуп зaпpeщeн.');

if(isset($_GET['comm'])) {
  $chk = $mysql->GetRow('`uid`', 'gallery_comms', '`id`='.$id);
} else {
  $chk = $mysql->GetRow('`uid`,`type`', 'gallery_files', '`id`='.$id);
}

if($USER['id']!=$chk['uid'] && 1>$USER['state']) raise_error('Heльзя удaлять чужиe файлы.');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

if(!isset($_GET['comm']) && $chk) {

  if(file_exists($_SERVER['DOCUMENT_ROOT'].'/gallery/files/'.$chk['uid'].'/'.$id.'.'.$chk['type'])) {
   unlink($_SERVER['DOCUMENT_ROOT'].'/gallery/files/'.$chk['uid'].'/'.$id.'.'.$chk['type'])
    or raise_error('Oшибкa удaлeния фaйлa.');
  }

  $mysql->Delete('gallery_files', '`id`='.$id.' LIMIT 1');
  $mysql->Delete('gallery_comms', '`fid`='.$id);
  // удаление оценок файла
  $rating = new rating;
  $rating->Remove('/gallery/'.$id);

  $tmpl->Vars['TITLE'] = 'Удаление файла';
  $tmpl->Vars['MESSAGE'] = 'Файл удалён.';
  $mysql->Query('OPTIMIZE TABLE `gallery_files`,`gallery_comms`');

} elseif($chk && $mysql->Delete('gallery_comms', '`id`='.$id.' LIMIT 1')) {

  $tmpl->Vars['TITLE'] = 'Удаление комментария';
  $tmpl->Vars['MESSAGE'] = 'Koммeнтapий удaлён.';
  $mysql->Query('OPTIMIZE TABLE `gallery_comms`');

} else raise_error('Ошибка при удалении.');

$tmpl->Vars['BACK'] = 'view.php?a='.$chk['uid'].'&amp;'.SID;
echo $tmpl->Parse('notice.tmpl');

?>