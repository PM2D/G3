<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$mysql = new mysql;
$roomid =& getvar('r');
$roomid = intval($roomid);
// получаем данные комнаты
$room = $mysql->GetRow('*', 'chatrooms', '`id`='.$roomid);
// Если такой комнаты нет, то кидаем к списку комнат
if(!$room)
  raise_error('Такой комнаты не существует!', 'index.php?'.SID);

// добавление сообщения
if(isset($_POST['msg'])) {

  if(3==$USER['id'] && !$CFG['CHAT']['guests']) exit; 

  if(1>$USER['state']) include($_SERVER['DOCUMENT_ROOT'].'/etc/bancheck.php');

  $text =& $_POST['msg'];
  $trans =& postvar('trans');
  $priv =& postvar('priv');
  $priv = intval($priv);
  
  // если текста нет, или он слишком большой, то кидаем назад
  if(!trim($text) || isset($text[4000])){
    Header('Location: say.php?r='.$roomid.'&amp;'.SID);
    exit;
  };
  
  $text = stripslashes(htmlspecialchars($text));

  if($trans=='on'){
    $trans = new translit;
    $trans->FromTrans($text);
  };

  $smiles = new smiles;
  $smiles->ToImg($text);
  $tags = new tags;
  $tags->ToHtm($text);
  $text = preg_replace('/(&[a-z]{2,4})</', '$1;<', $text);
  $text = nl2br($text);

  // если такое же сообщение не существует, то добавляем его
  $ex = $mysql->GetRow('`msg`', 'chat', '`id` ORDER BY `id` DESC');
  if($ex['msg']!=$text) {
    $text = $mysql->EscapeString($text);
    $mysql->Insert('chat', array('roomid'=>$roomid,'id'=>0, 'uid'=>$USER['id'], 'to'=>$priv, 'time'=>$TIME, 'msg'=>$text));
    $mysql->Update('users', array('posts'=>'`posts`+1'), '`id`='.$USER['id'].' LIMIT 1');
    if('#'==$text[0] or '!'==$text[0] or '/'==$text[0] or 'терминатор'==mb_substr(mb_strtolower($text, 'UTF-8'), 0, 10, 'UTF-8')) include('bot.php');
  }

  header('Location: room.php?r='.$roomid, TRUE, 302);
  exit;

}

$n =& getvar('n');
$n = intval($n);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = $room['name'];
$tmpl->Vars['ROOM'] = $room;
$tmpl->Vars['POSTS'] = array();

$mysql->Query('SELECT `users`.`login`,`chat`.*
FROM `chat` LEFT JOIN `users` ON `chat`.`uid`=`users`.`id`
WHERE `chat`.`roomid`='.$roomid.' AND (`chat`.`to`=0 OR `chat`.`to`='.$USER['id'].' OR `chat`.`uid`='.$USER['id'].')
ORDER BY `chat`.`id` DESC LIMIT '.$n.','.$USER['np']);

while($arr = $mysql->FetchAssoc()) {
  if($arr['to']) $arr['time'] = '!';
  else $arr['time'] = date('d.m G:i', $arr['time']);
  $tmpl->Vars['POSTS'][] = $arr;
}

$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = $mysql->GetField('COUNT(*)', 'chat', '`roomid`='.$room['id']);
$tmpl->Vars['NAV']['limit'] = $USER['np'];
$tmpl->Vars['NAV']['add'] = 'r='.$room['id'];

$online->add('B чaтe ('.$room['name'].')');

echo $tmpl->Parse('chat/chat.tmpl');

?>