<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');
if(1>$USER['state']) include($_SERVER['DOCUMENT_ROOT'].'/etc/bancheck.php');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Обменник - Загрузка';

$path = $_SERVER['DOCUMENT_ROOT'].'/filex/files/';

if(!isset($USER['id']) || 3==$USER['id']) raise_error('Дocтуп зaпpeщён.');
if(!is_writable($path))
  raise_error('Heвoзмoжнo пpoизвoдить зaпиcь т.к. дocтуп к пaпкe зaпpeщён, oбpaтитecь к aдминиcтpaтopу');

$cid = intval(getvar('c'));

$mysql = new mysql;
$category = $mysql->GetRow('*', 'filex_cats', '`id`='.$cid);
if(!$category)
  raise_error('Нет такой категории.', 'index.php?'.SID);
$category['types'] = explode(' ', $category['types']);

if($category['limit']<$mysql->GetField('COUNT(*)', 'filex_files', '`uid`='.$USER['id']))
  raise_error('Превышен лимит файлов для категории ('.$category['limit'].').<br />
'.$USER['login'].', удалите не нужные файлы и попробуйте опять.', 'index.php?'.SID);

if(isset($_GET['from'])) {
  $from =& $_GET['from'];
} elseif(isset($_POST['from'])) {
  $from =& $_POST['from'];
} else {
  $from = FALSE;
}

if(isset($_FILES['userfile']) && is_uploaded_file($_FILES['userfile']['tmp_name'])) {

  $file =& $_FILES['userfile'];
  $ext = substr($file['name'], strrpos($file['name'], '.')+1);
  $ext = strtolower($ext);
  if(!in_array($ext, $category['types']))
    raise_error('Фaйл нeдoпуcтимoгo фopмaтa.', 'upload.php?'.SID);
  if($file['error'])
    raise_error($file['error'], 'upload.php?'.SID);
  if($file['size']>$category['max'])
    raise_error('Фaйл cлишкoм бoльшoй ('.round($file['size']/1024,1).'kb > '.round($category['max']/1024,1).'kb)', 'upload.php?'.SID);
  // подготовка данных для вставки в таблицу
  $in['id'] = 0;
  $in['cid'] = $cid;
  $in['uid'] = $USER['id'];
  $in['time'] = $GLOBALS['TIME'];
  $in['type'] = $ext;
  $in['size'] = $file['size'];
  $in['width'] = 0;
  $in['height'] = 0;
  $in['title'] = stripslashes(htmlspecialchars($file['name']));
  if(!$in['title']) $in['title'] = 'unnamed '.date('d.m.y G:i');
  $in['title'] = $mysql->EscapeString($in['title']);
  $in['about'] =& postvar('about');
  if(!$in['about']) raise_error('Нет описания к файлу.', 'upload.php?c='.$cid.'&amp;'.SID);
  $in['about'] = stripslashes(htmlspecialchars($in['about']));
  if('on'==postvar('trans')){
    $trans = new translit;
    $trans->FromTrans($in['about']);
  };
  $smiles = new smiles;
  $smiles->ToImg($in['about']);
  $tags = new tags;
  $tags->ToHtm($in['about']);
  $in['about'] = nl2br($in['about']);
  $in['about'] = preg_replace('/(&[a-z]{2,4})</', '$1;<', $in['about']);
  $in['about'] = $mysql->EscapeString($in['about']);
  $in['dloads'] = 0;
  $in['comms'] = 0;
  // вставка данных в таблицу
  $mysql->Insert('filex_files', $in);
  // получение id вставленной строки, и перемещение загруженного файла в соответствующее место
  $fname = $mysql->GetLastId();
  move_uploaded_file($file['tmp_name'], $path.$cid.'/'.$fname.'.'.$ext);
  chmod($path.$cid.'/'.$fname.'.'.$ext, 0666);
  // уведомление
  $tmpl->Vars['MESSAGE'] = $USER['login'].', Ваш файл был благополучно загружен.';
  if($from){
    $tmpl->Vars['BACK'] = $from.'&amp;attid='.$fname.'&amp;'.SID;
  } else {
    $tmpl->Vars['BACK'] = 'view.php?c='.$cid.'&amp;'.SID;
  }
  echo $tmpl->Parse('notice.tmpl');
  // обновление времени обновления категории :)
  $mysql->Update('filex_cats', array('time'=>$GLOBALS['TIME']), '`id`='.$cid.' LIMIT 1');

} else {

  $tmpl->Vars['CATID'] = $category['id'];
  $tmpl->Vars['ABOUT'] = $category['about'];
  $tmpl->Vars['MAXBYTES'] = $category['max'];
  $tmpl->Vars['MAXKB'] = round($category['max']/1024, 1).'kb';
  $tmpl->Vars['ALLOWED'] = implode(', ', $category['types']);
  $tmpl->Vars['FROM'] = $from;
  echo $tmpl->Parse('filex/upload.tmpl');

}

?>