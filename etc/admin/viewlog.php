<?php
// This file is a part of GIII (g3.steelwap.org)
$n =& getvar('n');
$n = intval($n);
$arr = file($_SERVER['DOCUMENT_ROOT'].'/var/log.dat');
$arr = array_reverse($arr);
$cnt = count($arr);
$tmpl->Vars['LOG'] = array();
for($i=$n, $c=0; $c<$USER['np'] && $i<$cnt; $i++, $c++){
  $a = explode('||', $arr[$i]);
  $tmpl->Vars['LOG'][$i]['date'] = $a[0];
  $tmpl->Vars['LOG'][$i]['dscr'] = $a[1];
}
include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/nav.php');
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = $cnt;
$tmpl->Vars['NAV']['limit'] = $USER['np'];
$tmpl->Vars['NAV']['add'] = 'mod='.$mod;
echo $tmpl->Parse('admin/viewlog.tmpl');
?>