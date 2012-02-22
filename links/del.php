<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

if(1<$USER['state']) {
  $arr = file($_SERVER['DOCUMENT_ROOT'].'/var/links.dat');
  $n =& getvar('n');
  $n = intval($n);
  $ex = explode('|:|', $arr[$n]);
  unset($arr[$n]);
  fstools::remove_if_exists($_SERVER['DOCUMENT_ROOT'].'/links/'.substr($ex[2], 0, -1));
  $f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/links.dat', 'w');
  flock($f, LOCK_EX);
  fwrite($f, implode(NULL, $arr));
  flock($f, LOCK_UN);
  fclose($f);
  Header('Location: index.php?'.SID);
}

?>