<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Список именинников';

$mysql = new mysql;
$mysql->Query('SELECT `id`,`login`,`bday`,`bmonth`,`byear`
FROM `users` WHERE `bmonth`='.date('m').' AND `bday`='.date('d'));
$tmpl->Vars['TODAY'] = array();
while($arr = $mysql->FetchAssoc()){
  $tmpl->Vars['TODAY'][] = $arr;
}

$mysql->Query('SELECT `id`,`login`,`bday`,`bmonth`,`byear`
FROM `users` WHERE `bmonth`='.date('m').' ORDER BY `bday`');
$tmpl->Vars['MONTH'] = array();
while($arr = $mysql->FetchAssoc()){
  $tmpl->Vars['MONTH'][] = $arr;
}

$mysql->Close();

$online->Add('Cпиcoк именинников');

echo $tmpl->Parse('bday.tmpl');

?>