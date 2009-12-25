<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$n =& getvar('n');
$n = intval($n);

$uid =& getvar('uid');
$uid = intval($uid);
if(!$uid) $uid = $USER['id'];

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Топ файлов';

$cur = floor($TIME/86400);

$mysql = new mysql;

$tmpl->Vars['UID'] = $uid;
$tmpl->Vars['LOGIN'] = $mysql->GetField('login', 'users', '`id`='.$uid);

$mysql->Query('SELECT SQL_CALC_FOUND_ROWS * FROM `filex_files` WHERE `uid`='.$uid.' ORDER BY `dloads` DESC LIMIT '.$n.','.$USER['np']);

$tmpl->Vars['FILES'] = array();

while($arr = $mysql->FetchAssoc()){

  $day = floor($arr['time']/86400);
  if($cur==$day) $arr['time'] = 'ceгoдня в '.date('G:i', $arr['time']);
  elseif(($cur-1)==$day) $arr['time'] = 'вчepa в '.date('G:i', $arr['time']);
  elseif(($cur-2)==$day) $arr['time'] = 'пoзaвчepa в '.date('G:i', $arr['time']);
  else $arr['time'] = date('d.m.y в G:i', $arr['time']);
  $tmpl->Vars['FILES'][] = $arr;

}

$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = $mysql->GetFoundRows();
$tmpl->Vars['NAV']['limit'] = $USER['np'];
$tmpl->Vars['NAV']['add'] = 'uid='.$uid;

$online->Add('Обменник (файлы пользователя)');

echo $tmpl->Parse('filex/userfiles.tmpl');

?>