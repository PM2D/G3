<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$cid =& getvar('c');

if(!$cid || 2>$USER['state'])
  raise_error('Дocтуп зaпpeщён.');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Удаление категории';

if(!isset($_GET['sure'])) {

  $tmpl->Vars['MESSAGE'] = 'Bы увepeны чтo xoтитe уничтoжить категорию со всеми входящими в нее файлами?';
  $tmpl->Vars['YES'] = 'destroy.php?c='.$cid.'&amp;sure&amp;'.SID;
  $tmpl->Vars['NO'] = 'index.php?'.SID;
  echo $tmpl->Parse('confirm.tmpl');

} else {

  $mysql = new mysql;
  $rating = new rating;
  // удаление комментариев
  $mysql->Delete('filex_comms', '`cid`='.$cid);
  // удаление оценок
  $mysql->Query('SELECT `id` FROM `filex_files` WHERE `cid`='.$cid);
  while($arr = $mysql->FetchRow()) {
    $rating->Remove('/filex/'.$arr[0]);
  }
  // удаление файлов
  $mysql->Delete('filex_files', '`cid`='.$cid);
  // удаление категории
  $mysql->Delete('filex_cats', '`id`='.$cid);
  $mysql->Query('OPTIMIZE TABLE `filex_cats`,`filex_files`,`filex_comms`');
  // удаление папки категории
  $fs = new fstools;
  $fs->remove_if_exists($_SERVER['DOCUMENT_ROOT'].'/filex/files/'.$cid);
  $tmpl->Vars['MESSAGE'] = 'Категория удалёна.';
  $tmpl->Vars['BACK'] = 'index.php?'.SID;
  echo $tmpl->Parse('notice.tmpl');

}
?>