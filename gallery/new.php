<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');
if(1>$USER['state']) include($_SERVER['DOCUMENT_ROOT'].'/etc/bancheck.php');

if(3==$USER['id'])
  raise_error('Создавать альбомы разрешено только зарегистрированным пользователям.');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

if(isset($_POST['title'])){

  $title =& $_POST['title'];
  $trans = postvar('trans');
  if(!trim($title)) raise_error('Oтcутcтвуeт нaзвaниe альбома.', 'new.php?'.SID);
  $mysql = new mysql;
  $title = $mysql->EscapeString(stripslashes(htmlspecialchars($title)));
  if('on'==$trans){
    $obj = new translit;
    $obj->FromTrans($title);
  };
  if(!$mysql->IsExists('gallery_albums', '`uid`='.$USER['id'])){
    $in['id'] = 0;
    $in['uid'] = $USER['id'];
    $in['title'] = $title;
    $in['time'] = $GLOBALS['TIME'];
    $in['perm'] = 0;
    $mysql->Insert('gallery_albums', $in);
  };
  $tmpl->Vars['TITLE'] = 'Создание нового альбома';
  $tmpl->Vars['MESSAGE'] = $USER['login'].', вaш альбом coздан.';
  $tmpl->Vars['FORWARD'] = 'view.php?a='.$USER['id'].'&amp;'.SID;
  echo $tmpl->Parse('forward.tmpl');

} else {

  $tmpl->Vars['TITLE'] = 'Создать новый альбом';
  echo $tmpl->Parse('gallery/new.tmpl');

}
?>