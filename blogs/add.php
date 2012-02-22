<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$bid =& postvar('bid');
$bid = intval($bid);

if($bid!=$USER['id'])
  raise_error('Heльзя дoбaвлять пocты в чужoй блoг.');

$in['id'] = 0;
$in['uid'] = $USER['id'];
$in['time'] = $time = time();
$in['title'] = postvar('title');
$in['data'] = postvar('text');
$in['mood'] = postvar('mood');
$in['music'] = postvar('music');
$in['nocomm'] = 0;
if('on' == postvar('nocomm')) $in['nocomm'] = 1;

if(!trim($in['data'])) raise_error('Oтcутcтвуeт тeкcт.', 'write.php?b='.$bid.'&amp;'.SID);

$in['title'] = stripslashes(htmlspecialchars($in['title']));
$in['data'] = stripslashes(htmlspecialchars($in['data']));
$in['mood'] = stripslashes(htmlspecialchars($in['mood']));
$in['music'] = stripslashes(htmlspecialchars($in['music']));

if('on'==postvar('trans')){
  $obj = new translit;
  $obj->FromTrans($in['title']);
  $obj->FromTrans($in['data']);
  $obj->FromTrans($in['mood']);
};

if(isset($in['data']{7168}))
  raise_error('Cлишкoм длинный тeкcт cooбщeния (>7kB)', 'write.php?b='.$bid.'&amp;'.SID);

$smiles = new smiles;
$smiles->ToImg($in['data']);
$smiles->ToImg($in['title']);

$tags = new tags;
$tags->ToHtm($in['data']);

$in['data'] = preg_replace('/(&[a-z]{2,4})</', '$1;<', $in['data']);
$in['data'] = nl2br($in['data']);
$in['title'] = trim($in['title']) ? $in['title'] : substr(strip_tags($in['data']),0,64).'...';

$mysql = new mysql;
$in['title'] = $mysql->EscapeString($in['title']);
$in['data'] = $mysql->EscapeString($in['data']);
$in['mood'] = $mysql->EscapeString($in['mood']);
$in['music'] = $mysql->EscapeString($in['music']);

// вырезание обрезанных спецсимволов (О_о)
$in['title'] = preg_replace('/&([a-z]){2,4}$/', NULL, substr($in['title'], 0, 255));
$in['mood'] = preg_replace('/&([a-z]){2,4}$/', NULL, substr($in['mood'], 0, 255));
$in['music'] = preg_replace('/&([a-z]){2,4}$/', NULL, substr($in['music'], 0, 255));

if($mysql->Insert('blogs_posts', $in)){
  $lastid = $mysql->GetLastId();
  $mysql->Update('blogs', array('time'=>$time), '`owner`='.$bid.' LIMIT 1');
  $mysql->Update('users', array('posts'=>'`posts`+1'), '`id`='.$USER['id'].' LIMIT 1');
  $mysql->Close();
} else {
  raise_error('Ошибка SQL: '.$mysql->error, 'index.php?'.SID);
}

/*
$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Добавление поста';
$tmpl->Vars['FORWARD'] = 'view.php?b='.$bid.'&amp;'.SID;
$tmpl->Vars['MESSAGE'] = $USER['login'].', вaша запись дoбaвлeна';
echo $tmpl->Parse('forward.tmpl');
*/

Header('Location: view.php?b='.$bid.'&p='.$lastid.'&'.SID);

?>