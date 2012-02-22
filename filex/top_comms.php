<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$cid =& getvar('c');
$cid = intval($cid);

$n =& getvar('n');
$n = intval($n);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = $USER['np'].' самых комментируемых';

$cur = floor($TIME/86400);

$mysql = new mysql;
$mysql->Query('SELECT SQL_CALC_FOUND_ROWS * FROM `filex_files` FORCE INDEX (`comms`) ORDER BY `comms` DESC LIMIT '.$n.','.$USER['np']);

$tmpl->Vars['FILES'] = array();

while($arr = $mysql->FetchAssoc()){

  $day = floor($arr['time']/86400);
  if($cur==$day) $arr['time'] = 'ceгoдня в '.date('G:i', $arr['time']);
  elseif(($cur-1)==$day) $arr['time'] = 'вчepa в '.date('G:i', $arr['time']);
  elseif(($cur-2)==$day) $arr['time'] = 'пoзaвчepa в '.date('G:i', $arr['time']);
  else $arr['time'] = date('d.m.y в G:i', $arr['time']);
  $tmpl->Vars['FILES'][] = $arr;

}

$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = $mysql->GetFoundRows();
$tmpl->Vars['NAV']['limit'] = $USER['np'];

$online->Add('Обменник (топ)');

echo $tmpl->Parse('filex/top_comms.tmpl');

?>