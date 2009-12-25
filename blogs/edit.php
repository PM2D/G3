<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$pid =& getvar('p');
$pid = intval($pid);

$mysql = new mysql;
$post = $mysql->GetRow('*', 'blogs_posts', '`id`='.$pid);

if($USER['id']!=$post['uid'] && 2!=$USER['state'])
  raise_error('Paзрешено редактировать только свои пocты.');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

$smiles = new smiles;
$tags = new tags;

if(isset($_POST['text'])){

  $upd['title'] = stripslashes(htmlspecialchars($_POST['title']));
  $upd['data'] = stripslashes(htmlspecialchars($_POST['text']));
  $upd['mood'] = stripslashes(htmlspecialchars($_POST['mood']));
  $upd['music'] = stripslashes(htmlspecialchars($_POST['music']));
  $smiles->ToImg($upd['title']);
  if(!trim($upd['data'])) raise_error('He зaпoлнeнo пoлe.', 'edit.php?p='.$pid.'&amp;'.SID);
  $smiles->ToImg($upd['data']);
  $tags->ToHtm($upd['data']);
  $upd['data'] = preg_replace('/(&[a-z]{2,4})</', '$1;<', $upd['data']);
  $upd['title'] = $mysql->EscapeString($upd['title']);
  $upd['data'] = $mysql->EscapeString(nl2br($upd['data']));
  $upd['mood'] = $mysql->EscapeString($upd['mood']);
  $upd['music'] = $mysql->EscapeString($upd['music']);
  // вырезание обрезанных спецсимволов (О_о)
  $upd['title'] = preg_replace('/&([a-z]){2,4}$/', NULL, substr($upd['title'], 0, 255));
  $upd['mood'] = preg_replace('/&([a-z]){2,4}$/', NULL, substr($upd['mood'], 0, 255));
  $upd['music'] = preg_replace('/&([a-z]){2,4}$/', NULL, substr($upd['music'], 0, 255));

  if($mysql->Update('blogs_posts', $upd, '`id`='.$pid.' LIMIT 1')){
    $tmpl->Vars['TITLE'] = 'Обновление записи';
    $tmpl->Vars['MESSAGE'] = 'Запись обновлена.';
    $tmpl->Vars['BACK'] = 'view.php?b='.$post['uid'].'&amp;'.SID;
    echo $tmpl->Parse('notice.tmpl');
  } else raise_error('Ошибка SQL: '.$mysql->error);

} else {

  $smiles->FromImg($post['title']);
  $post['title'] = strip_tags($post['title']);
  $smiles->FromImg($post['data']);
  $tags->FromHtm($post['data']);
  $post['data'] = strip_tags($post['data']);
  $tmpl->Vars['TITLE'] = 'Редактирование записи';
  $tmpl->Vars['POST'] = $post;
  echo $tmpl->Parse('blogs/edit.tmpl');

}

?>