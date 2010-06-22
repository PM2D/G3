<?php
// This file is a part of GIII (g3.steelwap.org)
include($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

if(!IsModInstalled('links'))
  raise_error('Данный модуль на данный момент не установлен.');

$arr = file($_SERVER['DOCUMENT_ROOT'].'/var/links.dat');
$cnt = count($arr);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Ссылки';
$tmpl->Vars['LINKS'] = array();

for($i=0; $i<$cnt; $i++) {
  $a = explode('|:|', substr($arr[$i], 0, -1));
  $tmpl->Vars['LINKS'][$i]['id'] = $i;
  $tmpl->Vars['LINKS'][$i]['name'] = $a[0];
  $tmpl->Vars['LINKS'][$i]['url'] = $a[1];
  $tmpl->Vars['LINKS'][$i]['icon'] = ($a[2]) ? $a[2] : '/ico/'.$USER['icons'].'/link.gif';
}

echo $tmpl->Parse('links/index.tmpl');

?>