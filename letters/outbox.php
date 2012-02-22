<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$n =& getvar('n');
$n = intval($n);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Исходящие записки';

$mysql = new mysql;
$mysql->Query('SELECT SQL_CALC_FOUND_ROWS `letters`.*,`users`.`login`
FROM `letters` LEFT JOIN `users` ON `letters`.`to`=`users`.`id`
WHERE `uid`='.$USER['id'].' ORDER BY `letters`.`id` DESC LIMIT '.$n.','.$USER['np']);

$tmpl->Vars['LETTERS'] = array();
while($arr = $mysql->FetchAssoc()){
  $tmpl->Vars['LETTERS'][] = $arr;
}

$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = $mysql->GetFoundRows();
$tmpl->Vars['NAV']['limit'] = $USER['np'];

$mysql->Close();

echo $tmpl->Parse('letters/outbox.tmpl');

?>