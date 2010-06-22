<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/headers.php');
$compress->Enable();

if(2>$USER['state']) raise_error('Доступ запрещён.');

$nid =& getvar('nid');
$nid = intval($nid);

$tags = new tags;
$smiles = new smiles;
$tmpl = new template;
$mysql = new mysql;

if(isset($_POST['text'])){

  $tmpl->Vars['TITLE'] = 'Обновление новости';
  $upd['title'] = stripslashes(htmlspecialchars($_POST['title']));
  $upd['text'] = stripslashes(htmlspecialchars($_POST['text']));
  $upd['tags'] = stripslashes(htmlspecialchars($_POST['tags']));
  if(!$upd['title'] || !$upd['text']) raise_error('Не заполнено поле.');
  $smiles->ToImg($upd['text']);
  $tags->ToHtm($upd['text']);
  $upd['text'] = preg_replace('/(&[a-z]{2,4})</', '$1;<', $upd['text']);
  $upd['text'] = strtr($upd['text'], array("\n"=>'<br />',"\r"=>'','[line]'=>'<hr />'));
  $upd['title'] = $mysql->EscapeString($upd['title']);
  $upd['text'] = $mysql->EscapeString($upd['text']);
  $upd['tags'] = $mysql->EscapeString($upd['tags']);
  $mysql->Update('news', $upd, '`id`='.$nid.' LIMIT 1');
  $tmpl->Vars['MESSAGE'] = 'Hoвocть измененa';
  $tmpl->Vars['BACK'] = 'index.php?'.SID;
  echo $tmpl->Parse('notice.tmpl');

} else {

  $tmpl->Vars['TITLE'] = 'Редактирование новости';
  $new = $mysql->GetRow('*', 'news', '`id`='.$nid);
  $smiles->FromImg($new['text']);
  $tags->FromHtm($new['text']);
  $new['text'] = strtr($new['text'], array('<hr />'=>'<br />[line]<br />'));
  $tmpl->Vars['NEW'] = $new;
  echo $tmpl->Parse('news/edit.tmpl');

}

?>