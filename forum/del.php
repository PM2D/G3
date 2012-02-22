<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/headers.php');
$compress->Enable();

$pid =& getvar('p');
$pid = intval($pid);

if(1>$USER['state']) raise_error('Доступ запрещён.');

$mysql = new mysql;
$post = $mysql->GetRow('*', 'forum_posts', '`id`='.$pid);

if(!$post) raise_error('Нет такого поста');

if(isset($_GET['sure']) && $mysql->Delete('forum_posts', '`id`='.$pid.' LIMIT 1')){

  $mysql->Update('forum_themes', array('count'=>'`count`-1'), '`id`='.$post['tid'].' LIMIT 1');
  $mysql->Query('OPTIMIZE TABLE `forum_posts`');
  to_log($USER['login'].' удaлил из фopумa сообщение '.$post['msg']);

  $tmpl = new template;
  $tmpl->Vars['TITLE'] = 'Удаление сообщения';
  $tmpl->Vars['MESSAGE'] = 'Сообщение удалено';
  $tmpl->Vars['BACK'] = 'index.php?'.SID;
  echo $tmpl->Parse('notice.tmpl');

} else {

  $tmpl = new template;
  $tmpl->Vars['TITLE'] = 'Удаление сообщения';
  $tmpl->Vars['MESSAGE'] = 'Удалить сообщение "'.$post['msg'].'"?';
  $tmpl->Vars['YES'] = 'del.php?sure&amp;p='.$pid.'&amp;'.SID;
  $tmpl->Vars['NO'] =
		isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : '/?'.SID;
  echo $tmpl->Parse('confirm.tmpl');

}

?>