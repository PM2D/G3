<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$uid =& getvar('uid');
$uid = intval($uid);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Создание нового сообщения';

if($uid){
  $mysql = new mysql;
  $tmpl->Vars['TO'] = $mysql->GetRow('`login`,`avatar`', 'users', '`id`='.$uid);
  $mysql->Close();
};

if(3==$USER['id']) include($_SERVER['DOCUMENT_ROOT'].'/ico/_misc/codegen.php');

$tmpl->Vars['TO']['id'] = $uid;

echo $tmpl->Parse('gbook/write.tmpl');

?>