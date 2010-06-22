<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');
if(1>$USER['state']) include($_SERVER['DOCUMENT_ROOT'].'/etc/bancheck.php');

$n = &getvar('n');
$n = intval($n);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Гостевая книга';

$mysql = new mysql;
$mysql->Query('SELECT SQL_CALC_FOUND_ROWS `gbook`.*,`login`,`state`,`status`,`sex`,`posts`,`avatar`
FROM `gbook` LEFT JOIN `users` ON `gbook`.`uid`=`users`.`id` ORDER BY `gbook`.`id` DESC LIMIT '.$n.','.$USER['np']);

$tmpl->Vars['POSTS'] = array();
while($arr = $mysql->FetchAssoc()) {
  if(3 == $USER['id']) {
    $arr['editable'] = FALSE;
  } else {
    $arr['editable'] = ($arr['time']>$TIME-21600 OR 0<$USER['state'] OR $USER['id']==$arr['uid']) ? TRUE : FALSE;
  }
  $arr['time'] = format_date($arr['time']);
  $arr['online'] = $online->GetStatus($arr['uid']);
  $tmpl->Vars['POSTS'][] = $arr;
}

$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = $mysql->GetFoundRows();
$tmpl->Vars['NAV']['limit'] = $USER['np'];

$online->Add('B гocтeвoй');

echo $tmpl->Parse('gbook/gbook.tmpl');

?>