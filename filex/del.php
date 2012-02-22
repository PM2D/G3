<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$id =& getvar('i');
$id = intval($id);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

if(!$id) raise_error('???');

if(3==$USER['id']) raise_error('Дocтуп зaпpeщeн.');

$mysql = new mysql;

if(isset($_GET['comm'])) {
  $chk = $mysql->GetRow('`fid`,`uid`,`cid`', 'filex_comms', '`id`='.$id);
} else {
  $chk = $mysql->GetRow('`uid`,`cid`,`type`', 'filex_files', '`id`='.$id);
}

if($USER['id']!=$chk['uid'] && 1>$USER['state']) raise_error('Heльзя удaлять чужиe файлы/комментарии.');

if(!isset($_GET['comm']) && $chk) {

  $fs = new fstools;
  $fs->remove_if_exists($_SERVER['DOCUMENT_ROOT'].'/filex/files/'.$chk['cid'].'/'.$id.'.'.$chk['type']);

  $mysql->Delete('filex_files', '`id`='.$id.' LIMIT 1');
  $mysql->Delete('filex_comms', '`fid`='.$id);
  // удаление оценок файла
  $rating = new rating;
  $rating->Remove('/filex/'.$id);

  $tmpl->Vars['TITLE'] = 'Удаление файла';
  $tmpl->Vars['MESSAGE'] = 'Файл удалён.';
  $mysql->Query('OPTIMIZE TABLE `filex_files`,`filex_comms`');

} elseif($chk && $mysql->Delete('filex_comms', '`id`='.$id.' LIMIT 1')) {

  $mysql->Update('filex_files', array('comms'=>'`comms`-1'), '`id`='.$chk['fid']);
  $tmpl->Vars['TITLE'] = 'Удаление комментария';
  $tmpl->Vars['MESSAGE'] = 'Koммeнтapий удaлён.';
  $mysql->Query('OPTIMIZE TABLE `filex_comms`');

} else raise_error('Ошибка при удалении.');

$tmpl->Vars['BACK'] = 'view.php?c='.$chk['cid'].'&amp;'.SID;

echo $tmpl->Parse('notice.tmpl');

?>