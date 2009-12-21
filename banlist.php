<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

if(1>$USER['state']) raise_error('Доступ запрещён.');

$n = isset($_GET['n']) ? intval($_GET['n']) : FALSE;
$file = file($_SERVER['DOCUMENT_ROOT'].'/var/ban.dat');

function get_ban_mode($i){
  switch($i){
   case 0:
    return 'IP+UA';
   break;
   case 1:
    return 'UA';
   break;
   case 2:
    return 'IP';
   break;
   case 3:
    return 'Cookie[ID]';
   break;
   case 4:
    return 'ID';
   break;
   case 5:
    return 'Total Ban';
   break;
   default:
    return FALSE;
   break;
  }
}

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

if(FALSE!==$n && 0<$USER['state']){
  $a = explode('|:|', $file[$n]);
  unset($file[$n]);
  $f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/ban.dat', 'w');
  fwrite($f, implode(NULL, $file));
  to_log($USER['login'].' удaлил из бaн лиcтa '.get_ban_mode($a[0]).': '.$a[1]);
  $tmpl->Vars['TITLE'] = 'Удаление из бан-листа.';
  $tmpl->Vars['MESSAGE'] = 'Запись удалена.';
  $tmpl->Vars['FORWARD'] = 'banlist.php?'.SID;
  echo $tmpl->Parse('forward.tmpl');
  exit;
};

$tmpl->Vars['TITLE'] = 'Бан-лист';

$cnt = count($file);

$tmpl->Vars['LIST'] = array();
for($i=0; $i<$cnt; $i++){
  $a = explode('|:|', $file[$i]);
  $tmpl->Vars['LIST'][$i]['method'] = get_ban_mode($a[0]);
  $tmpl->Vars['LIST'][$i]['data'] = $a[1];
}

echo $tmpl->Parse('admin/banlist.tmpl');

?>