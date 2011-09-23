<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$pid = getvar('p');
$pid = intval($pid);

$mysql = new mysql;
$post = $mysql->GetRow('*', 'gbook', '`id`='.$pid);

if(3==$USER['id'])
  raise_error('Доступ запрещен.', '/gbook/?'.SID);

if($USER['id']!=$post['uid'] && 1>$USER['state'])
  raise_error('Paзрешено редактировать только свои сообщения.', '/gbook/?'.SID);

if(1>$USER['state'] && $post['time']<($TIME-21600))
  raise_error('Извините, время редактирования поста истекло (6ч).', '/gbook/?'.SID);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

$smiles = new smiles;

if(isset($_POST['msg'])) {

  $text =& postvar('msg');
  $podp =& postvar('sign');
  $text = stripslashes(htmlspecialchars($text));
  $podp = stripslashes(htmlspecialchars($podp));

  if(!$text) raise_error('He зaпoлнeнo тeкcтoвoe пoлe.');

  $smiles->ToImg($text);
  $tags = new tags;
  $tags->ToHtm($text);
  $text = preg_replace('/(&[a-z]{2,4})</', '$1;<', $text);
  $text = $mysql->EscapeString(nl2br(trim($text)));
  $podp = $mysql->EscapeString($podp);
  $podp = preg_replace('/&([a-z]){2,4}$/', NULL, substr($podp, 0, 255));
  $mysql->Update('gbook', array('msg'=>$text,'sign'=>$podp), '`id`='.$pid.' LIMIT 1');

  $tmpl->Vars['TITLE'] = 'Обновление сообщения';
  $tmpl->Vars['MESSAGE'] = 'Сообщение измeнeнo.';
  $tmpl->Vars['BACK'] = 'index.php?'.SID;
  echo $tmpl->Parse('notice.tmpl');

} else {

  $text = $post['msg'];
  $smiles->FromImg($text);
  $tags = new tags;
  $tags->FromHtm($text);
  $text = strip_tags($text);

  $tmpl->Vars['TITLE'] = 'Редактирование сообщения';
  $tmpl->Vars['PID'] = $pid;
  $tmpl->Vars['TEXT'] = $text;
  $tmpl->Vars['SIGN'] = $post['sign'];
  echo $tmpl->Parse('gbook/edit.tmpl');

}

?>