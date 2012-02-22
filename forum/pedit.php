<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$pid =& getvar('p');
$pid = intval($pid);

$mysql = new mysql;
$post = $mysql->GetRow('*', 'forum_posts', '`id`='.$pid);

if(3==$USER['id'])
  raise_error('Гocтям зaпpeщeнo peдaктиpoвaть cooбщeния.', '/forum/view.php?t='.$pid.'&amp;'.SID);

if($USER['id']!=$post['uid'] && 1>$USER['state'])
  raise_error('Paзрешено редактировать только свои сообщения.', '/forum/view.php?t='.$pid.'&amp;'.SID);

if(1>$USER['state'] && $post['time']<($TIME-21600))
  raise_error('Извините, но время редактирования поста истекло (6ч).', '/forum/view.php?t='.$pid.'&amp;'.SID);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Редактирование сообщения';

$smiles = new smiles;
$tags = new tags;

if(isset($_POST['msg'])) {

  $upd['msg'] =& postvar('msg');
  $upd['sign'] =& postvar('sign');

  if(!trim($upd['msg'])) raise_error('He зaпoлнeнo тeкcтoвoe пoлe.');

  $upd['msg'] = stripslashes(htmlspecialchars($upd['msg']));
  $upd['sign'] = stripslashes(htmlspecialchars($upd['sign']));
  $smiles->ToImg($upd['msg']);
  $tags->ToHtm($upd['msg']);
  $upd['msg'] = preg_replace('/(&[a-z]{2,4})</', '$1;<', $upd['msg']);
  $upd['msg'] = nl2br(trim($upd['msg']));
  $upd['msg'] = $mysql->EscapeString($upd['msg']);
  $upd['sign'] = $mysql->EscapeString($upd['sign']);
  $upd['sign'] = substr($upd['sign'], 0, 255);
  $upd['sign'] = preg_replace('/&([a-z]){2,4}$/', NULL, $upd['sign']);

  $mysql->Update('forum_posts', $upd, '`id`='.$pid.' LIMIT 1');

  to_log($USER['login'].' отредактировал сообщение "'.$post['msg'].'" на форуме');

  $tmpl->Vars['MESSAGE'] = 'Сообщение изменено';
  $tmpl->Vars['BACK'] = 'view.php?t='.$post['tid'].'&amp;'.SID;
  echo $tmpl->Parse('notice.tmpl');

} else {

  $smiles->FromImg($post['msg']);
  $tags->FromHtm($post['msg']);
  $post['msg'] = strip_tags($post['msg']);

  $tmpl->Vars['POST'] = $post;
  echo $tmpl->Parse('forum/pedit.tmpl');

}

?>