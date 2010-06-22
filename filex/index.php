<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

if(!IsModInstalled('filex'))
  raise_error('Данный модуль на данный момент не установлен.');

$n =& getvar('n');
$n = intval($n);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Файловый обменник';

$mysql = new mysql;

// смотрим новые комментарии, если не гость
if(3!=$USER['id']) {
  $que = $mysql->Query('SELECT COUNT(*),`fid` FROM `filex_comms`
WHERE `time`>'.$USER['last'].' AND `fuid`='.$USER['id'].'
AND `uid`!='.$USER['id'].' GROUP BY `fid`');
  $tmpl->Vars['NEWCOMMS'] = array();
  while($arr = $mysql->FetchRow($que)) {
    $title = $mysql->GetField('`title`', 'filex_files', '`id`='.$arr[1]);
    $tmpl->Vars['NEWCOMMS'][] = array('title'=>$title, 'fid'=>$arr[1], 'count'=>$arr[0]);
  }
}

$mysql->Query('SELECT * FROM `filex_cats` ORDER BY `time` DESC');

$tmpl->Vars['CATS'] = array();
while($arr = $mysql->FetchAssoc()) {
  $arr['time'] = format_date($arr['time']);
  $tmpl->Vars['CATS'][] = $arr;
}

$mysql->Close();

$online->Add('Обменник (категории)');

echo $tmpl->Parse('filex/index.tmpl');
?>