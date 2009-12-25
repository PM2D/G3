<?php
// This file is a part of GIII (g3.steelwap.org)
include($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

if(2>$USER['state'])
  raise_error('Доступ запрещен.');

if(isset($_POST['name']) && isset($_POST['url'])){
  $arr = file($_SERVER['DOCUMENT_ROOT'].'/var/links.dat');
  $c = count($arr);
  $arr[$c+1] = htmlspecialchars($_POST['name']).'|:|'.htmlspecialchars($_POST['url'])."\n";
  sort($arr, SORT_STRING);
  $c = count($arr);
  if($c<1) exit;
  $f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/links.dat', 'w');
  flock($f, LOCK_EX);
  fwrite($f, implode(NULL, $arr));
  flock($f, LOCK_UN);
  fclose($f);
  Header('Location: index.php?'.SID);
  exit;
};

include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/headers.php');
$compress->Enable();

$tmpl = new template;
$tmpl->Vars['TITLE'] = 'Добавить ссылку';
echo $tmpl->Parse('admin/addlink.tmpl');

?>