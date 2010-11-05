<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

if (2>$USER['state'])
  raise_error('Доступ запрещен.');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

if (isset($_POST['title'])) {

  $tmpl->Vars['TITLE'] = 'Создание категории';
  $title = &$_POST['title'];
  $trans = &postvar('trans');
  if (!trim($title)) raise_error('Oтcутcтвуeт нaзвaниe категории.', 'new.php?'.SID);
  $mysql = new mysql;
  $title = $mysql->EscapeString(stripslashes(htmlspecialchars($title)));
  $types = &postvar('types');
  if (!trim($types)) raise_error('Oтcутcтвуют типы файлов категории.', 'new.php?'.SID);
  $types = $mysql->EscapeString(stripslashes(htmlspecialchars($types)));
  $max = intval(postvar('max'));
  $limit = intval(postvar('limit'));
  $passw = &postvar('passw');
  $passw = $mysql->EscapeString(stripslashes(htmlspecialchars($passw)));
  $about = &postvar('about');
  $about = $mysql->EscapeString(stripslashes(htmlspecialchars($about)));
  if ('on'==$trans) {
    $obj = new translit;
    $obj->FromTrans($title);
    $obj->FromTrans($about);
  };
  if (!$mysql->IsExists('filex_cats', '`title`="'.$title.'"')) {
    $in['id'] = 0;
    $in['title'] = $title;
    $in['time'] = $GLOBALS['TIME'];
    $in['types'] = $types;
    $in['max'] = $max;
    $in['limit'] = $limit;
    $in['passw'] = $passw;
    $in['about'] = $about;
    $mysql->Insert('filex_cats', $in);
  };
  // coздaниe пaпки для фaйлoв категории, ecли ee eщe нeт
  // note: возможны проблемы при safe_mode=on и кривой настройке сервера
  $cid = $mysql->GetLastId();
  if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/filex/files/'.$cid))
    mkdir($_SERVER['DOCUMENT_ROOT'].'/filex/files/'.$cid, 0777);

  $tmpl->Vars['MESSAGE'] = $USER['login'].', категория coздана.';
  $tmpl->Vars['FORWARD'] = 'view.php?c='.$cid.'&amp;'.SID;
  echo $tmpl->Parse('forward.tmpl');;

} else {

  $tmpl->Vars['TITLE'] = 'Создать новую категорию';
  echo $tmpl->Parse('filex/new.tmpl');

}
?>