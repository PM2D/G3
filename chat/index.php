<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');
if(1>$USER['state']) include($_SERVER['DOCUMENT_ROOT'].'/etc/bancheck.php');

if(!IsModInstalled('chat'))
  raise_error('Данный модуль на данный момент не установлен.');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Чат - Прихожая';

$online->Add('В прихожей чата');

$mysql = new mysql;
$mysql->Query('SELECT * FROM `chatrooms`');

$tmpl->Vars['ROOMS'] = array();
while($row = $mysql->FetchAssoc()){
  $row['online'] = $online->CountIn('/chat/room.php?r='.$row['id']);
  $tmpl->Vars['ROOMS'][] = $row;
}

echo $tmpl->Parse('chat/rooms.tmpl');
?>