<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Обратная связь';

$mysql = new mysql;
$mysql->Query('SELECT `login`,`email` FROM `users` WHERE `state`>1');
$tmpl->Vars['NICKS'] = array();
$tmpl->Vars['EMAILS'] = array();
while($arr = $mysql->FetchAssoc()){
  $tmpl->Vars['NICKS'][] = $arr['login'];
  if($arr['email']) $tmpl->Vars['EMAILS'][] = $arr['email'];
}

echo $tmpl->Parse('feedback.tmpl');

?>