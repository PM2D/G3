<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

if(1<$USER['state']){
  $arr = file($_SERVER['DOCUMENT_ROOT'].'/var/links.dat');
  $n =& getvar('n');
  $n = intval($n);
  unset($arr[$n]);
  $f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/links.dat', 'w');
  flock($f, LOCK_EX);
  fwrite($f, implode(NULL, $arr));
  flock($f, LOCK_UN);
  fclose($f);
  Header('Location: index.php?'.SID);
};

?>