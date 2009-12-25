<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$id =& getvar('f');
$id = intval($id);

$mysql = new mysql;
$file = $mysql->GetRow('*', 'files', '`id`='.$id);

if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/ddir'.$file['path'])) raise_error('Heт тaкoгo фaйлa.');

if(isset($_GET['jad'])){
  if(!extension_loaded('zip')) raise_error('Расширение php zip не доступно.');
  $zip = new ZipArchive;
  $zip->open($_SERVER['DOCUMENT_ROOT'].'/ddir'.$file['path']);
  $data = $zip->getFromName('META-INF/MANIFEST.MF');
  $zip->close();
  if(!$data)
   raise_error('Вероятно файл не является правильным j2me архивом.');
  header('Content-Type: text/vnd.sun.j2me.app-descriptor');
  header('Content-Disposition: filename='.basename($file['path'], '.jar').'.jad');
  $data = preg_replace('/MIDlet-Jar-URL: [\S\s]+[\n\r]/i', NULL, $data);
  $data = preg_replace('/[\n\r]{2,}/', "\n", $data);
  print $data.'MIDlet-Jar-Size: '.$file['size']."\n".'MIDlet-Jar-URL: http://'.$_SERVER['HTTP_HOST'].'/ddir'.$file['path'];
  exit;
}

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = $file['name'];

$about = $_SERVER['DOCUMENT_ROOT'].'/ddir'.dirname($file['path']).'/_'.basename($file['path'], '.jar').'.txt';
$tmpl->Vars['ABOUT'] = file_exists($about) ? file_get_contents($about) : FALSE;

$tmpl->Vars['SIZE'] = round($file['size']/1024,1).' kb';
$tmpl->Vars['UNZIP'] = $CFG['DDIR']['unzip'];
$tmpl->Vars['URL'] = '/ddir'.$file['path'];
$tmpl->Vars['ID'] = $id;
$tmpl->Vars['BACK'] = '/ddir/?d='.$file['did'].'&amp;'.SID;
// соседние файлы
$arr = $mysql->GetRow('*', 'files', '`did`='.$file['did'].' AND `id`<'.$id.' ORDER BY `id` DESC');
if($arr) require($_SERVER['DOCUMENT_ROOT'].'/etc/ddir/handlers.php');
$tmpl->Vars['PREV'] = $arr;
$arr = $mysql->GetRow('*', 'files', '`did`='.$file['did'].' AND `id`>'.$id.' ORDER BY `id` ASC');
if($arr) require($_SERVER['DOCUMENT_ROOT'].'/etc/ddir/handlers.php');
$tmpl->Vars['NEXT'] = $arr;
$mysql->Close();

echo $tmpl->Parse('ddir/java.tmpl');

?>