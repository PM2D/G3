<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/headers.php');
$compress->Enable();

$n = getvar('n');
$n = intval($n);

$mysql = new mysql;

$tmpl = new template;
$tmpl->Vars['TITLE'] = 'Закрытые темы';


$mysql->Query('SELECT SQL_CALC_FOUND_ROWS * FROM `forum_themes` WHERE `closed`>0
ORDER BY `time` DESC LIMIT '.$n.','.$USER['np']);
$tmpl->Vars['THEMES'] = array();
while($arr = $mysql->FetchAssoc()){
  $arr['time'] = date('d.m.y в G:i', $arr['time']);
  $tmpl->Vars['THEMES'][] = $arr;
}

$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = $mysql->GetFoundRows();
$tmpl->Vars['NAV']['limit'] = $USER['np'];

$mysql->Close();

$online->Add('Фopум (зaкpытыe тeмы)');

echo $tmpl->Parse('forum/closed_themes.tmpl');

?>