<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$fid =& getvar('i');
$fid = intval($fid);

$mysql = new mysql;
$image = $mysql->GetRow('*', 'gallery_files', '`id`='.$fid);

if($USER['id']!=$image['uid'] && 2!=$USER['state'])
  raise_error('Paзрешено редактировать только свои изображения.');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

$tags = new tags;
$smiles = new smiles;

if(isset($_POST['title'])){

  $title = stripslashes(htmlspecialchars($_POST['title']));
  $about = stripslashes(htmlspecialchars($_POST['about']));
  $smiles->ToImg($title);
  if(!trim($about)) raise_error('He зaпoлнeнo пoлe.', 'edit.php?i='.$fid.'&amp;'.SID);
  $smiles->ToImg($about);
  $tags->ToHtm($about);
  $a = preg_replace('/(&[a-z]{2,4})</', '$1;<', $about);
  $title = $mysql->EscapeString($title);
  $about = $mysql->EscapeString(nl2br($about));
  if($mysql->Update('gallery_files', array('title'=>$title,'about'=>$about), '`id`='.$fid.' LIMIT 1')){
    $tmpl->Vars['TITLE'] = 'Обновление информации об изображении';
    $tmpl->Vars['MESSAGE'] = 'Информация об изображении обновлена.';
    $tmpl->Vars['BACK'] = 'view.php?a='.$image['uid'].'&amp;'.SID;
    echo $tmpl->Parse('notice.tmpl');
  };

} else {

  $tmpl->Vars['TITLE'] = 'Редактирование информации об изображении';
  $smiles->FromImg($image['title']);
  $image['title'] = htmlspecialchars($image['title']);
  $smiles->FromImg($image['about']);
  $tags->FromHtm($image['about']);
  $image['about'] = strip_tags($image['about']);
  $tmpl->Vars['IMAGE'] = $image;
  echo $tmpl->Parse('gallery/edit.tmpl');

}

?>