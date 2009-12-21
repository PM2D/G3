<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Кто онлайн';
$tmpl->Vars['BACK'] = isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : FALSE;
$tmpl->Vars['USERS'] = array();

for($i=0; $i<online::$count; ++$i){
  $ex = explode('|:|', $online->arr[$i]);
  if(0<$online->user[$ex[0]][0]){
   $arr['id'] = $ex[0];
   $arr['login'] = $ex[1];
   $arr['ua'] = $ex[2];
   $arr['time'] = date('H:i', $ex[3]);
   $arr['where'] = $ex[4];
   $arr['status'] = $ex[6];
   $tmpl->Vars['USERS'][] = $arr;
  };
}

echo $tmpl->Parse('online.tmpl');

?>