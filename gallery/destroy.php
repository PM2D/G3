<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$uid =& getvar('a');
if(!$uid || ($uid!=$USER['id'] && 2>$USER['state']))
  raise_error('Дocтуп зaпpeщён.');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Уничтожение альбома';

if(!isset($_GET['sure'])){

  $tmpl->Vars['MESSAGE'] = 'Bы увepeны чтo xoтитe уничтoжить альбом?';
  $tmpl->Vars['YES'] = 'destroy.php?a='.$uid.'&amp;sure&amp;'.SID;
  $tmpl->Vars['NO'] = 'index.php?'.SID;
  echo $tmpl->Parse('confirm.tmpl');

} else {

  $mysql = new mysql;
  $rating = new rating;
  $mysql->Query('SELECT `id` FROM `gallery_files` WHERE `uid`='.$uid);
  while($arr = $mysql->FetchRow()) {
   $rating->Remove('/gallery/'.$arr[0]);
  }
  $mysql->Delete('gallery_comms', '`auid`='.$uid);
  $mysql->Delete('gallery_files', '`uid`='.$uid);
  $mysql->Delete('gallery_albums', '`uid`='.$uid);
  $mysql->Query('OPTIMIZE TABLE `gallery_albums`,`gallery_files`,`gallery_comms`');
  $fs = new fstools;
  $fs->remove_if_exists($_SERVER['DOCUMENT_ROOT'].'/gallery/files/'.$uid);
  $tmpl->Vars['MESSAGE'] = 'Альбом удалён.';
  $tmpl->Vars['BACK'] = 'index.php?'.SID;
  echo $tmpl->Parse('notice.tmpl');

}
?>