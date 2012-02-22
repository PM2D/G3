<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/headers.php');
$compress->Enable();

$n =& getvar('n');
$mod =& getvar('mod');

if(1>$USER['state']) raise_error('Доступ запрещён.');

$mysql = new mysql;
$tmpl = new template;
$tmpl->Vars['TID'] = $n;

switch($mod){

  case 'del':
   $tmpl->Vars['TITLE'] = 'Удаление темы';
   include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/forum/delt.php');
  break;

  case 'edit':
   $tmpl->Vars['TITLE'] = 'Редактирование темы';
   include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/forum/editt.php');
  break;

  case 'close':
   $tmpl->Vars['TITLE'] = 'Закрытие темы';
   include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/forum/closet.php');
  break;

  case 'fix':
   $tmpl->Vars['TITLE'] = 'Фиксирование темы';
   include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/forum/fixt.php');
  break;

  case 'free':
   $tmpl->Vars['TITLE'] = 'Восстановление темы';
   include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/forum/freet.php');
  break;

  case 'move':
   $tmpl->Vars['TITLE'] = 'Перемещение темы';
   include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/forum/movet.php');
  break;

  default:
   $tmpl->Vars['TITLE'] = 'Действия над темой';
   $mysql->Query('SELECT * FROM `forums` ORDER BY `rid`');
   while($arr = $mysql->FetchAssoc()){
     if($arr['rid']) $arr['name']='-'.$arr['name'];
     $tmpl->Vars['FORUMS'][] = $arr;
   }
   echo $tmpl->Parse('admin/theme.tmpl');
  break;

}

?>