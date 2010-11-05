<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$cid =& getvar('c');
$cid = intval($cid);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

if(2>$USER['state'])
  raise_error('Доступ запрещен.');

$mysql = new mysql;
$cat = $mysql->GetRow('*', 'filex_cats', '`id`='.$cid);

$smiles = new smiles;
$tags = new tags;

if(isset($_POST['title'])){

  $tmpl->Vars['TITLE'] = 'Обновление категории';
  $upd['title'] = stripslashes(htmlspecialchars($_POST['title']));
  $upd['about'] = stripslashes(htmlspecialchars($_POST['about']));
  $upd['passw'] = stripslashes(htmlspecialchars($_POST['passw']));
  $upd['types'] = stripslashes(htmlspecialchars($_POST['types']));
  $upd['max'] = intval($_POST['max']);
  $upd['limit'] = intval($_POST['limit']);
  if(!trim($upd['title'])) raise_error('Отсутствует название категории.', 'catedit.php?c='.$cid.'&amp;'.SID);
  if(!trim($upd['types'])) raise_error('Oтcутcтвуют типы файлов категории.', 'catedit.php?c='.$cid.'&amp;'.SID);
  $tags->ToHtm($upd['about']);
  $smiles->ToImg($upd['about']);
  $upd['about'] = preg_replace('/(&[a-z]{2,4})</', '$1;<', $upd['about']);
  $upd['title'] = $mysql->EscapeString($upd['title']);
  $upd['types'] = $mysql->EscapeString($upd['types']);
  $upd['passw'] = $mysql->EscapeString($upd['passw']);
  $upd['about'] = $mysql->EscapeString(nl2br($upd['about']));
  if($mysql->Update('filex_cats', $upd, '`id`='.$cid.' LIMIT 1')){
    $tmpl->Vars['MESSAGE'] = 'Категория измeнена.';
    $tmpl->Vars['BACK'] = 'index.php?'.SID;
    echo $tmpl->Parse('notice.tmpl');
  };

} else {

  $tmpl->Vars['TITLE'] = 'Редактирование категории';
  $smiles->FromImg($cat['about']);
  $tags->FromHtm($cat['about']);
  $cat['about'] = strip_tags($cat['about']);
  $tmpl->Vars['CAT'] = $cat;
  echo $tmpl->Parse('filex/catedit.tmpl');

}

?>