<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$postid =& getvar('p');
$postid = intval($postid);

$roomid =& getvar('r');
$roomid = intval($roomid);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Удаление сообщения.';

if(1>$USER['state']) raise_error('Доступ запрещён.');

$mysql = new mysql;
$post = $mysql->GetRow('*', 'chat', '`id`='.$postid);

if(!$post) raise_error('Возможно такого поста не существует.','index.php?'.SID);

if(isset($_GET['sure']) && $mysql->Delete('chat', '`id`='.$postid.' LIMIT 1')){

  $mysql->Query('OPTIMIZE TABLE `chat`');

  $tmpl->Vars['MESSAGE'] = 'Сообщение удалено.';
  $tmpl->Vars['BACK'] = 'room.php?r='.$roomid.'&amp;'.SID;
  echo $tmpl->Parse('notice.tmpl');

  to_log($USER['login'].' удалил из чата сообщение '.$post['msg']);

} else {

  $tmpl->Vars['MESSAGE'] = 'Вы уверены что хотите удалить сообщение "'.$post['msg'].'"?';
  $tmpl->Vars['YES'] = 'del.php?p='.$postid.'&amp;r='.$roomid.'&amp;sure&amp;'.SID;
  $tmpl->Vars['NO'] = 'room.php?r='.$roomid.'&amp;'.SID;
  echo $tmpl->Parse('confirm.tmpl');

}

?>