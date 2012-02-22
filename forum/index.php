<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

if(!IsModInstalled('forum'))
  raise_error('Данный модуль на данный момент не установлен.');

$n =& getvar('n');
$n = intval($n);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Список форумов';
$tmpl->Vars['FORUMS'] = array();

$mysql = new mysql;

$que = $mysql->Query('SELECT * FROM `forums` WHERE `rid`=0');

while($arr = $mysql->FetchAssoc($que)){

  $cnt = $mysql->GetField('SUM(`count`)', 'forums', '`rid`='.$arr['id']);
  $arr['count'] += $cnt;

  $theme = $mysql->GetRow('*', 'forum_themes', '`prid`='.$arr['id'].' ORDER BY `time` DESC');
  $arr['themeid'] = $theme['id'] ? $theme['id'] : FALSE;
  if($arr['themeid']){
    $arr['themename'] = $theme['name'];
    $arr['themelastuid'] = $theme['lastuid'];
    $arr['themelastuser'] = $theme['lastuser'];
    $arr['time'] = format_date($theme['time']);
  };

  $tmpl->Vars['FORUMS'][] = $arr;

}

$online->Add('Фopум');

echo $tmpl->Parse('forum/forums.tmpl');

?>