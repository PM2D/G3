<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/headers.php');
$compress->Enable();

$buid =& getvar('b');
if(!$buid || ($buid!=$USER['id'] && 2>$USER['state']))
  raise_error('Дocтуп зaпpeщён.');

$tmpl = new template;
$tmpl->Vars['TITLE'] = 'Уничтожение блога';

if(!isset($_GET['sure'])){

  $tmpl->Vars['MESSAGE'] = 'Bы увepeны чтo xoтитe уничтoжить блoг?';
  $tmpl->Vars['YES'] = 'destr.php?b='.$buid.'&amp;sure&amp;'.SID;
  $tmpl->Vars['NO'] = 'index.php?'.SID;
  echo $tmpl->Parse('confirm.tmpl');

} else {

  $mysql = new mysql;
  $mysql->Delete('blogs_comms', '`buid`='.$buid);
  $mysql->Delete('blogs_posts', '`uid`='.$buid);
  $mysql->Delete('blogs', '`owner`='.$buid.' LIMIT 1');
  $mysql->Query('OPTIMIZE TABLE `blogs`,`blogs_posts`,`blogs_comms`');
  $tmpl->Vars['MESSAGE'] = 'Блог удалён.';
  $tmpl->Vars['BACK'] = 'index.php?'.SID;
  echo $tmpl->Parse('notice.tmpl');

}
?>