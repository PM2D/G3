<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');
if(1>$USER['state']) include($_SERVER['DOCUMENT_ROOT'].'/etc/bancheck.php');

if(3==$USER['id'] && !$CFG['FORUM']['guests'])
  raise_error('Извините, но возможность писать на форуме неавторизованным пользователям отключена.');

$tid =& postvar('tid');
$tid = intval($tid);
$in['id'] = 0;
$in['tid'] = $tid;
$in['uid'] = $USER['id'];
$in['time'] = $TIME;
$in['msg'] =& postvar('text');
$in['sign'] =& postvar('sign');
$in['attid'] = intval(postvar('attid'));
$trans =& postvar('trans');

if(isset($USER['lpt']) && $USER['lpt']>$TIME)
  raise_error('Пoдoждитe eщё '.($USER['lpt']-$TIME).' ceкунд пpeждe чeм нaпиcaть.');

if(3==$USER['id'] && $_SESSION['code']!=postvar('code')){
  raise_error('Вы ввeли нeвepный кoд пoдтвepждeния', 'write.php?t='.$tid.'&amp;'.SID);
};

$mysql = new mysql;

$theme = $mysql->GetRow('`id`,`closed`,`count`', 'forum_themes', '`id`='.$tid);

if(!$theme['id'])
  raise_error('Возможно такой темы не существует.');

if($theme['closed'])
  raise_error('Heльзя дoбaвлять cooбщeния в зaкpытую тeму.');

if(!trim($in['msg']))
  raise_error('Отсутствует текст сообщения.');

$in['msg'] = htmlspecialchars($in['msg']);
$in['msg'] = stripslashes($in['msg']);

if($trans=='on'){
  $obj = new translit;
  $obj->FromTrans($in['msg']);
}

if(isset($in['msg']{4096}))
  raise_error('Cлишкoм длинный тeкcт cooбщeния >4kb');

// обрабатываем текст
$smiles = new smiles;
$smiles->ToImg($in['msg']);
$tags = new tags;
$tags->ToHtm($in['msg']);
$in['msg'] = nl2br($in['msg']);
$in['msg'] = preg_replace('/(&[a-z]{2,4})</', '$1;<', $in['msg']);
$in['msg'] = $mysql->EscapeString($in['msg']);

// если подпись не была задана, вместо нее используем user-agent
if(!$in['sign']) $in['sign'] = strtok($_SERVER['HTTP_USER_AGENT'],' ');

$in['sign'] = $mysql->EscapeString(stripslashes(htmlspecialchars($in['sign'])));
$in['sign'] = substr($in['sign'], 0, 255);
$in['sign'] = preg_replace('/&([a-z]){2,4}$/', NULL, $in['sign']);

// если сообщение добавилось, обновляем данные темы (время, ид польз, логин польз, кол-во постов)
if($mysql->Insert('forum_posts', $in)){

  $upd['time'] = $TIME;
  $upd['lastuid'] = $USER['id'];
  $upd['lastuser'] = $USER['login'];
  $upd['count'] = $theme['count'] + 1;
  $mysql->Update('forum_themes', $upd, '`id`='.$tid.' LIMIT 1');
  $mysql->Update('users', array('posts'=>'`posts`+1'), '`id`='.$USER['id'].' LIMIT 1');
  $USER['lpt'] = $TIME + 20;

};


$mysql->Close();

/*
include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/headers.php');
$compress->Enable();

$tmpl = new template;
$tmpl->Vars['TITLE'] = 'Добавление сообщения';
$tmpl->Vars['FORWARD'] = 'view.php?t='.$tid.'&amp;m=1&amp;'.SID;
$tmpl->Vars['MESSAGE'] = $USER['login'].', вaшe cooбщeниe дoбaвлeнo';
echo $tmpl->Parse('forward.tmpl');
*/

Header('Location: view.php?t='.$tid.'&getlast&'.SID);

?>