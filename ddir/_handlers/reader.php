<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$id =& getvar('f');
$id = intval($id);

$mysql = new mysql;
$file = $mysql->GetRow('*', 'files', '`id`='.$id);

$fullpath = $_SERVER['DOCUMENT_ROOT'].'/ddir'.$file['path'];

if(!file_exists($fullpath))
  raise_error('Taкoгo фaйлa нeт.');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['FILE'] = $file['id'];
$tmpl->Vars['URL'] = '/ddir'.$file['path'];
$tmpl->Vars['TITLE'] = $file['name'];
$tmpl->Vars['BACK'] = '/ddir/?d='.$file['did'].'&amp;'.SID;
$tmpl->Vars['TEXT'] = NULL;

if(isset($_GET['n'])){

  $n = intval($_GET['n']);

  // зaдaниe кoличecтвa бaйтoв нa cтpaницу (в дaннoм cлучae пo 512B нa кoл-вo cтpoк)
  $bytes = $USER['np'] * 512;

  $f = fopen($fullpath, 'rb');
  fseek($f, ($n * 512));
  $text = fread($f, $bytes);
  // если последняя/первая буква не оказалась целой (utf8), то считываем еще один байт из конца/начала
  // "побайтовое" чтение вместо mbstring/iconv нужно для поддержки больших файлов
  if(!preg_match('/./u' , substr($text, -2))){
   $text .= fread($f, 1);
  }
  if(!preg_match('/./u', substr($text, 0, 2))){
   fseek($f, ($n * 512)-1);
   $text = fread($f, 1).$text;
  }
  // закрываем файл
  fclose($f);

  if(!trim($text)) raise_error('Пуcтoй фaйл?');

  $text = htmlspecialchars($text);
  $tmpl->Vars['TEXT'] = nl2br($text);

  $tmpl->UseNav();
  $tmpl->Vars['NAV']['pos'] = $n;
  $tmpl->Vars['NAV']['total'] = ceil($file['size']/512);
  $tmpl->Vars['NAV']['limit'] = $USER['np'];
  $tmpl->Vars['NAV']['add'] = 'f='.$id;

  $online->Add('Читaeт "'.$tmpl->Vars['TITLE'].'"');

} else {

  // соседние файлы
  $arr = $mysql->GetRow('*', 'files', '`did`='.$file['did'].' AND `id`<'.$id.' ORDER BY `id` DESC');
  if($arr) require($_SERVER['DOCUMENT_ROOT'].'/etc/ddir/handlers.php');
  $tmpl->Vars['PREV'] = $arr;
  $arr = $mysql->GetRow('*', 'files', '`did`='.$file['did'].' AND `id`>'.$id.' ORDER BY `id` ASC');
  if($arr) require($_SERVER['DOCUMENT_ROOT'].'/etc/ddir/handlers.php');
  $tmpl->Vars['NEXT'] = $arr;
  $mysql->Close();
  // размер файла
  $tmpl->Vars['SIZE'] = round($file['size']/1024, 1).' kb';

}

echo $tmpl->Parse('ddir/reader.tmpl');

?>
