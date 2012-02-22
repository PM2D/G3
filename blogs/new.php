<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');
if(1>$USER['state']) include($_SERVER['DOCUMENT_ROOT'].'/etc/bancheck.php');

if(3==$USER['id'])
  raise_error('Создавать блoги разрешено только зарегистрированным пользователям.');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

if(isset($_POST['name'])){

  $name = $_POST['name'];
  $trans = postvar('trans');
  if(!trim($name)) raise_error('Oтcутcтвуeт нaзвaниe блoгa.', 'new.php?'.SID);
  $mysql = new mysql;
  $name = $mysql->EscapeString(stripslashes(htmlspecialchars($name)));
  if('on'==$trans){
    $obj = new translit;
    $obj->FromTrans($name);
  };
  if(!$mysql->IsExists('blogs', '`owner`='.$USER['id'])){
    $in['id'] = 0;
    $in['name'] = $name;
    $in['owner'] = $USER['id'];
    $in['time'] = $TIME;
    $in['perm'] = 0;
    $in['favorites'] = NULL;
    $mysql->Insert('blogs', $in);
  };
  $tmpl->Vars['TITLE'] = 'Создание блога';
  $tmpl->Vars['MESSAGE'] = $USER['login'].', вaш блoг coздан.';
  $tmpl->Vars['FORWARD'] = 'view.php?b='.$USER['id'].'&amp;'.SID;
  echo $tmpl->Parse('forward.tmpl');

} else {

  $tmpl->Vars['TITLE'] = 'Создать блог';
  echo $tmpl->Parse('blogs/new.tmpl');

}
?>