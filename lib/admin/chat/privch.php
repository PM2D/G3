<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
$n =& getvar('n');
$n = intval($n);

$mysql = new mysql;

$mysql->Query('SELECT SQL_CALC_FOUND_ROWS `chat`.*,`users`.`login`
FROM `chat` LEFT JOIN `users` ON `chat`.`uid`=`users`.`id`
WHERE `to`>0 ORDER BY `id` DESC LIMIT '.$n.','.$USER['np']);

$tmpl->Vars['POSTS'] = array();
while($arr = $mysql->FetchAssoc()){
  $arr['time'] = date('d.m G:i', $m['time']);
  $tmpl->Vars['POSTS'][] = $arr;
}

include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/nav.php');
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = $mysql->GetFoundRows();
$tmpl->Vars['NAV']['limit'] = $USER['np'];
$tmpl->Vars['NAV']['add'] = 'mod='.$mod;

echo $tmpl->Parse('admin/privch.tmpl');

?>