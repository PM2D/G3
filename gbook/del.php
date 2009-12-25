<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/headers.php');
$compress->Enable();

$pid =& getvar('p');
$pid = intval($pid);

if(1>$USER['state']) raise_error('Доступ запрещён.');

$mysql = new mysql;

$post = $mysql->GetRow('*', 'gbook', '`id`='.$pid);

if(!$post) raise_error('Возможно такого поста не существует.','index.php?'.SID);

if(isset($_GET['sure']) && $mysql->Delete('gbook', '`id`='.$pid.' LIMIT 1')){

  $mysql->Query('OPTIMIZE TABLE `gbook`');

  $tmpl = new template;
  $tmpl->Vars['TITLE'] = 'Удаление сообщения';
  $tmpl->Vars['MESSAGE'] = 'Сообщение удалено.';
  $tmpl->Vars['BACK'] = 'index.php?'.SID;
  echo $tmpl->Parse('notice.tmpl');

  to_log($USER['login'].' удалил из гocтeвoй сообщение '.htmlspecialchars($post['msg']));

} else {

  $tmpl = new template;
  $tmpl->Vars['TITLE'] = 'Удаление сообщения';
  $tmpl->Vars['MESSAGE'] = 'Вы уверены что хотите удалить сообщение "'.htmlspecialchars($post['msg']).'"?';
  $tmpl->Vars['YES'] = 'del.php?p='.$pid.'&amp;sure&amp;'.SID;
  $tmpl->Vars['NO'] = 'index.php?'.SID;
  echo $tmpl->Parse('confirm.tmpl');

}

?>