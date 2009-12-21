<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$n =& getvar('n');
$n = intval($n);

$arr = file($_SERVER['DOCUMENT_ROOT'].'/var/today.dat');
$cnt = sizeof($arr);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Сегодняшние посетители';
$tmpl->Vars['LIST'] = array();

$lim = $USER['np']+2;

for($i=$n, $j=0; $j<$lim && $i<$cnt; $i++, $j++){
  $a = explode('||', $arr[$i]);
  $tmpl->Vars['LIST'][$i]['uid'] = $a[0];
  $tmpl->Vars['LIST'][$i]['login'] = $a[1];
  $tmpl->Vars['LIST'][$i]['ua'] = strtok($a[2], ' ');
  $tmpl->Vars['LIST'][$i]['ip'] = $a[3];
}

$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = $cnt;
$tmpl->Vars['NAV']['limit'] = $lim;

echo $tmpl->Parse('today.tmpl');

?>