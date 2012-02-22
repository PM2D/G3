<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Новостные ленты';
$tmpl->Vars['FEEDS'] = array();

$arr = file($_SERVER['DOCUMENT_ROOT'].'/var/rssr.dat');
$cnt = count($arr);
for($i=0; $i<$cnt; $i++){
  $ex = explode('|', $arr[$i]);
  $tmpl->Vars['FEEDS'][] = array('id'=>$i+1, 'url'=>$ex[0], 'name'=>$ex[1], 'ttl'=>$ex[2]);
}

$online->Add('Спиcoк лeнт');

echo $tmpl->Parse('rssr/index.tmpl');

?>