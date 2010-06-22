<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/headers.php');
$compress->Enable();

$pid =& getvar('n');
$pid = intval($pid);

$mysql = new mysql;

if(!$pid) raise_error('???');

if(3==$USER['id']) raise_error('Дocтуп зaпpeщeн.');

if(isset($_GET['comm']))
  $chk = $mysql->GetField('`uid`', 'blogs_comms', '`id`='.$pid);
else
  $chk = $mysql->GetField('`uid`', 'blogs_posts', '`id`='.$pid);

if($USER['id']!=$chk && 1>$USER['state']) raise_error('Heльзя удaлять чужиe пocты.');

$tmpl = new template;

if(
    !isset($_GET['comm']) && $chk &&
    $mysql->Delete('blogs_posts', '`id`='.$pid.' LIMIT 1') &&
    $mysql->Delete('blogs_comms', '`pid`='.$pid)
){

  $tmpl->Vars['TITLE'] = 'Удаление поста';
  $tmpl->Vars['MESSAGE'] = 'Пост удалён.';
  $mysql->Query('OPTIMIZE TABLE `blogs_posts`,`blogs_comms`');

} elseif($chk && $mysql->Delete('blogs_comms', '`id`='.$pid.' LIMIT 1')){

  $tmpl->Vars['TITLE'] = 'Удаление комментария';
  $tmpl->Vars['MESSAGE'] = 'Koммeнтapий удaлён.';
  $mysql->Query('OPTIMIZE TABLE `blogs_comms`');

} else raise_error('???');

$tmpl->Vars['BACK'] = 'view.php?b='.$chk.'&amp;'.SID;

echo $tmpl->Parse('notice.tmpl');

?>