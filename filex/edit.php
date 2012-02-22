<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$fid =& getvar('f');
$fid = intval($fid);

$mysql = new mysql;
$file = $mysql->GetRow('*', 'filex_files', '`id`='.$fid);

if($USER['id']!=$file['uid'] && 1>$USER['state'])
  raise_error('Paзрешено редактировать только свои файлы.');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

$smiles = new smiles;
$tags = new tags;

if(isset($_POST['title'])){

  $tmpl->Vars['TITLE'] = 'Обновление информации о файле';
  $title = stripslashes(htmlspecialchars($_POST['title']));
  $about = stripslashes(htmlspecialchars($_POST['about']));
  $smiles->ToImg($title);
  if(!trim($about)) raise_error('He зaпoлнeнo пoлe.', 'edit.php?i='.$fid.'&amp;'.SID);
  $smiles->ToImg($about);
  $tags->ToHtm($about);
  $a = preg_replace('/(&[a-z]{2,4})</', '$1;<', $about);
  $title = $mysql->EscapeString($title);
  $about = $mysql->EscapeString(nl2br($about));
  if($mysql->Update('filex_files', array('title'=>$title,'about'=>$about), '`id`='.$fid.' LIMIT 1')){
    $tmpl->Vars['MESSAGE'] = 'Информация измeнёна.';
    $tmpl->Vars['BACK'] = 'view.php?c='.$file['cid'].'&amp;'.SID;
    echo $tmpl->Parse('notice.tmpl');
  };

} else {

  $tmpl->Vars['TITLE'] = 'Редактирование информации о файле';
  $smiles->FromImg($file['title']);
  $file['title'] = htmlspecialchars($file['title']);
  $smiles->FromImg($file['about']);
  $tags->FromHtm($file['about']);
  $file['about'] = strip_tags($file['about']);
  $tmpl->Vars['FILE'] = $file;
  echo $tmpl->Parse('filex/edit.tmpl');

}

?>