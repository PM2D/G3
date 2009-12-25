<?php
// This file is a part of GIII (g3.steelwap.org)
include($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/headers.php');
$compress->Enable();

if(!IsModInstalled('links'))
  raise_error('Данный модуль на данный момент не установлен.');

$arr = file($_SERVER['DOCUMENT_ROOT'].'/var/links.dat');
$cnt = count($arr);

$tmpl = new template;
$tmpl->Vars['TITLE'] = 'Ссылки';
$tmpl->Vars['LINKS'] = array();

for($i=0; $i<$cnt; $i++){
  $a = explode('|:|', $arr[$i]);
  $tmpl->Vars['LINKS'][$i]['id'] = $i;
  $tmpl->Vars['LINKS'][$i]['name'] = $a[0];
  $tmpl->Vars['LINKS'][$i]['url'] = substr($a[1],0,-1);
}

echo $tmpl->Parse('links.tmpl');

?>