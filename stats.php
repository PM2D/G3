<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$mysql = new mysql;

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Статистика';
$tmpl->Vars['USERS'] = $mysql->GetField('COUNT(*)', 'users', '1');
$tmpl->Vars['MALE'] = $mysql->GetField('COUNT(*)', 'users', '`sex`=1');
$tmpl->Vars['FEMALE'] = $mysql->GetField('COUNT(*)', 'users', '`sex`=2');
$tmpl->Vars['STYLES'] = array();
$mysql->Query('SELECT COUNT(*),`style` FROM `users` GROUP BY `style`');

$percent = $tmpl->Vars['USERS'] / 100;

while($arr = $mysql->FetchRow()){
  $tmpl->Vars['STYLES'][] = array('count'=>$arr[0], 'perc'=>round($arr[0]/$percent, 1), 'name'=>$arr[1]);
}

rsort($tmpl->Vars['STYLES']);

echo $tmpl->Parse('stats.tmpl');

?>