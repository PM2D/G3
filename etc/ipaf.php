<?php
// This file is a part of GIII (g3.steelwap.org)
$arr = file($_SERVER['DOCUMENT_ROOT'].'/var/ipaf.dat');
$cnt = count($arr);
for($i=0; $i<$cnt; $i++){
  $a = explode('|', $arr[$i]);
  if($a[1]==$_SERVER['REMOTE_ADDR'] && $a[2]==$_SERVER['SCRIPT_NAME']."\n" && $a[0]>$TIME-1) exit('...');
  elseif($a[0]<$TIME-300 || $a[1]==$_SERVER['REMOTE_ADDR']) unset($arr[$i]);
}
$arr[] = $TIME.'|'.$_SERVER['REMOTE_ADDR'].'|'.$_SERVER['SCRIPT_NAME']."\n";
$f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/ipaf.dat', 'w');
fwrite($f, implode(NULL, $arr));
fclose($f);
?>