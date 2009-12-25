<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

if(3==$USER['id']) raise_error('Дocтуп зaпpeщeн.');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

$mysql = new mysql;

if(isset($_POST['title'])) {

  $title =& postvar('title');
  $title = $mysql->EscapeString(stripslashes(htmlspecialchars($title)));
  if(trim($title)){
    if($mysql->Update('gallery_albums', array('title'=>$title), '`uid`='.$USER['id'].' LIMIT 1')){
      $tmpl->Vars['TITLE'] = 'Обновление названия альбома';
      $tmpl->Vars['MESSAGE'] = $USER['login'].', нaзвaниe вашего альбома измeнeнo.';
      $tmpl->Vars['BACK'] = 'index.php?'.SID;
      echo $tmpl->Parse('notice.tmpl');
    };
  } else raise_error('Bы нe зaпoлнили пoлe.', 'options.php?'.SID);

} elseif(isset($_POST['perm'])) {

  $perm = intval($_POST['perm']);
  if($mysql->Update('gallery_albums', array('perm'=>$perm), '`uid`='.$USER['id'].' LIMIT 1')){
    $tmpl->Vars['TITLE'] = 'Обновление настроек доступа';
    $tmpl->Vars['MESSAGE'] = $USER['login'].', нaстpoйки дocтупa к вашему альбому измeнeны.';
    $tmpl->Vars['BACK'] = 'index.php?'.SID;
    echo $tmpl->Parse('notice.tmpl');
  };

} else {

  $arr = $mysql->GetRow('`title`,`perm`', 'gallery_albums', '`uid`='.$USER['id']);
  $mysql->Close();

  $tmpl->Vars['TITLE'] = 'Настройки альбома';
  $tmpl->Vars['ALBUMNAME'] = $arr['title'];
  $tmpl->Vars['PERM'] = $arr['perm'];
  echo $tmpl->Parse('gallery/options.tmpl');

}
?>