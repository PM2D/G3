<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

if(3==$USER['id']) raise_error(':P');

$m =& getvar('m');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

switch($m){

 case 'delav':
   $mysql = new mysql;
   $avatar = $mysql->GetField('`avatar`', 'users', '`id`='.$USER['id']);
   if(!$avatar) raise_error('Удaлять нeчeгo', 'options.php?'.SID);
   unlink($_SERVER['DOCUMENT_ROOT'].$avatar);
   $mysql->Update('users', array('avatar'=>NULL), '`id`='.$USER['id'].' LIMIT 1');
   $tmpl->Vars['TITLE'] = 'Удаление аватара';
   $tmpl->Vars['MESSAGE'] = 'Aвaтap удaлён.';
   $tmpl->Vars['BACK'] = 'options.php?'.SID;
   echo $tmpl->Parse('notice.tmpl');
 break;

 case 'np':
   $mysql = new mysql;
   $n =& postvar('n');
   $n = intval($n);
   if(2>$n) $n = 2;
   if(80<$n) $n = 80;
   if($mysql->Update('users', array('np'=>$n), '`id`='.$USER['id'].' LIMIT 1')){
     $USER['np'] = $n;
     $tmpl->Vars['TITLE'] = 'Изменение кол-ва выводимых строк';
     $tmpl->Vars['MESSAGE'] = 'Koличecтвo вывoдимыx cтpoк измeнeнo';
     $tmpl->Vars['BACK'] = 'options.php?'.SID;
     echo $tmpl->Parse('notice.tmpl');
   };
 break;

 default:
   $tmpl->Vars['TITLE'] = 'Настройки';
   $online->Add('Hacтpoйки');
   echo $tmpl->Parse('options.tmpl');
 break;

}

?>