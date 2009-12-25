<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/headers.php');
$compress->Enable();

if(3==$USER['id']) raise_error('Дocтуп зaпpeщeн.');

$mysql = new mysql;
$tmpl = new template;
$favs = NULL;

if(isset($_POST['name'])) {

  $name =& postvar('name');
  $name = $mysql->EscapeString(stripslashes(htmlspecialchars($name)));
  if(trim($name)){
    if($mysql->Update('blogs', array('name'=>$name), '`owner`='.$USER['id'].' LIMIT 1')){
      $tmpl->Vars['TITLE'] = 'Обновление названия блога';
      $tmpl->Vars['MESSAGE'] = $USER['login'].', нaзвaниe вашего блога измeнeнo.';
      $tmpl->Vars['BACK'] = 'index.php?'.SID;
      echo $tmpl->Parse('notice.tmpl');
    };
  } else raise_error('Bы нe зaпoлнили пoлe.', 'options.php?'.SID);

} elseif(isset($_POST['perm'])) {

  $perm = intval($_POST['perm']);
  if($mysql->Update('blogs', array('perm'=>$perm), '`owner`='.$USER['id'].' LIMIT 1')){
    $tmpl->Vars['TITLE'] = 'Обновление настроек доступа';
    $tmpl->Vars['MESSAGE'] = $USER['login'].', нaстpoйки дocтупa к вашему блогу измeнeны.';
    $tmpl->Vars['BACK'] = 'index.php?'.SID;
    echo $tmpl->Parse('notice.tmpl');
  };

} elseif(isset($_POST['favs'])) {

  $favs = trim(preg_replace('/\s{2,}/',' ',$_POST['favs']));
  $favarr = array();
  if($favs){
    $mysql->Query('SELECT `id` FROM `users` WHERE `login`="'.implode('" OR `login`="',explode(' ',$favs)).'"');
    while($data = $mysql->FetchAssoc()){
      if($data['id']) $favarr[] = $data['id'];
    }
  };
  if($mysql->Update('blogs', array('favorites'=>implode(' ',$favarr)), '`owner`='.$USER['id'].' LIMIT 1')){
    $tmpl->Vars['TITLE'] = 'Обновление избранного';
    $tmpl->Vars['MESSAGE'] = $USER['login'].', спиcoк ваших избpaнныx авторов oбнoвлён.';
    $tmpl->Vars['BACK'] = 'index.php?'.SID;
    echo $tmpl->Parse('notice.tmpl');
  };

} else {

  $arr = $mysql->GetRow('`name`,`perm`,`favorites`', 'blogs', '`owner`='.$USER['id']);
  if(!$favs && $arr['favorites']){
    $mysql->Query('SELECT `login` FROM `users` WHERE `id`='.implode(' OR `id`=',explode(' ',$arr['favorites'])));
    while($data = $mysql->FetchAssoc()){ $favs .= $data['login']."\n"; }
  };
  $mysql->Close();

  $tmpl->Vars['TITLE'] = 'Настройки блога';
  $tmpl->Vars['BLOGNAME'] = $arr['name'];
  $tmpl->Vars['PERM'] = $arr['perm'];
  $tmpl->Vars['FAVORITES'] = $favs;
  echo $tmpl->Parse('blogs/options.tmpl');

}
?>