<?php
// This file is a part of GIII (g3.steelwap.org)
$arr = file($_SERVER['DOCUMENT_ROOT'].'/var/refs.dat');
$c = count($arr);
$tmpl->Vars['REFS'] = array(); 
for($i=0; $i<$c; $i++){
  $tmpl->Vars['REFS'][] = htmlspecialchars(substr($arr[$i], 0, -1));
}
echo $tmpl->Parse('admin/referals.tmpl');
?>