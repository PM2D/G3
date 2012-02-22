<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

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