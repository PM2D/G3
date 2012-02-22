<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$postid =& getvar('p');
$postid = intval($postid);

$roomid =& getvar('r');
$roomid = intval($roomid);

$mysql = new mysql;
$post = $mysql->GetRow('`uid`,`msg`', 'chat', '`id`='.$postid);
if($USER['id']!=$post['uid'])
  raise_error('Paзрешено редактировать только свои сообщения.');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

$smiles = new smiles;
$tags = new tags;

if(isset($_POST['msg'])){

  $text = stripslashes(htmlspecialchars($_POST['msg']));
  if(!trim($text)) raise_error('He зaпoлнeнo тeкcтoвoe пoлe.', 'pe.php?p='.$postid.''.SID);
  $smiles->ToImg($text);
  $tags->ToHtm($text);
  $text = nl2br($text);
  $text = preg_replace('/(&[a-z]{2,4})</', '$1;<', $text);
  $text = $mysql->EscapeString($text);
  $mysql->Update('chat', array('msg'=>$text), '`id`='.$postid.' LIMIT 1');
  $tmpl->Vars['TITLE'] = 'Обновление сообщения';
  $tmpl->Vars['MESSAGE'] = 'Сообщение изменено.';
  $tmpl->Vars['BACK'] = 'room.php?r='.$roomid.''.SID;
  echo $tmpl->Parse('notice.tmpl');

} else {

  $smiles->FromImg($post['msg']);
  $tags->FromHtm($post['msg']);
  $post['msg'] = strip_tags($post['msg']);
  $tmpl->Vars['TITLE'] = 'Редактирование сообщения';
  $tmpl->Vars['PID'] = $postid;
  $tmpl->Vars['ROOMID'] = $roomid;
  $tmpl->Vars['TEXT'] = $post['msg'];
  echo $tmpl->Parse('chat/edit.tmpl');

}

?>