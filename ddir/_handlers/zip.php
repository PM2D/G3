<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

if(!extension_loaded('zip'))
  raise_error('Расширение php zip не доступно.');

$id =& getvar('f');
$id = intval($id);

$mysql = new mysql;
$file = $mysql->GetRow('*', 'files', '`id`='.$id);

if(!$file || !file_exists($_SERVER['DOCUMENT_ROOT'].'/ddir'.$file['path'])) raise_error('Heт тaкoгo фaйлa.');

if(isset($_GET['i'])) {
  $_GET['i'] = intval($_GET['i']);
  $i =& $_GET['i'];
  $zip = new ZipArchive;
  $zip->open($_SERVER['DOCUMENT_ROOT'].'/ddir'.$file['path']);
  $filename = $zip->getNameIndex($i);
  $ext = substr($filename, strrpos($filename, '.')+1);
  switch($ext) {
   case 'mid': case 'midi': $mime = 'audio/midi'; break;
   case 'jpg': case 'jpeg': $mime = 'image/jpeg'; break;
   case 'bmp': $mime = 'image/bmp'; break;
   case 'gif': $mime = 'image/gif'; break;
   case 'png': $mime = 'image/png'; break;
   case 'htm': case 'html': $mime = 'text/html'; break;
   case 'txt': $mime = 'text/plain'; break;
   default: $mime = 'application/octet-stream'; break;
  }
  header('Content-Type: '.$mime);
  header('Content-Disposition: filename="'.$filename.'"');
  echo($zip->GetFromIndex($i));
  $zip->close();
  exit;
}

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

$tmpl->Vars['FILE'] = $id;

isset($_GET['d']) ? $dir =& $_GET['d'] : $dir='.';

if($dir!='.') {
  $tmpl->Vars['TITLE'] = $file['name'].':'.htmlspecialchars($dir);
} else $tmpl->Vars['TITLE'] = $file['name'];

$tmpl->Vars['PATH'] = '/ddir'.$file['path'];

if(!isset($_GET['d']) || empty($_GET['d'])){

  $tmpl->Vars['SIZE'] = round($file['size']/1024,1).' kb';
  $tmpl->Vars['DIR'] = FALSE;
  // в папку
  $tmpl->Vars['BACK'] = '/ddir/?d='.$file['did'].'&amp;'.SID;
  // соседние файлы
  $arr = $mysql->GetRow('*', 'files', '`did`='.$file['did'].' AND `id`<'.$id.' ORDER BY `id` DESC');
  if($arr) require($_SERVER['DOCUMENT_ROOT'].'/etc/ddir/handlers.php');
  $tmpl->Vars['PREV'] = $arr;
  $arr = $mysql->GetRow('*', 'files', '`did`='.$file['did'].' AND `id`>'.$id.' ORDER BY `id` ASC');
  if($arr) require($_SERVER['DOCUMENT_ROOT'].'/etc/ddir/handlers.php');
  $tmpl->Vars['NEXT'] = $arr;
  $mysql->Close();

} else {

  // да просто так на всякий случай :)
  $mysql->Close();

  $n =& getvar('n');
  $n = intval($n);

  $tmpl->Vars['DIR'] = TRUE;
  if('.'!=$dir) {
    $tmpl->Vars['UP'] = dirname($dir);
  } else {
    $tmpl->Vars['UP'] = NULL;
  }

  $cached = $_SERVER['DOCUMENT_ROOT'].'/var/cache/zip/'.abs(crc32($file['path'].':'.$dir)).'.dat';

  if(!file_exists($cached)) {
    $zip = new ZipArchive;
    $zip->open($_SERVER['DOCUMENT_ROOT'].'/ddir'.$file['path']);
    $data = array();
    for($i=0; $i<$zip->numFiles; $i++) {
      $arr = $zip->statIndex($i);
      if(dirname($arr['name'])==$dir) {
        if(substr($arr['name'], -1)=='/') {
          $ext = 'dir';
          $path = 'zip.php?f='.$file['id'].'&amp;d='.substr($arr['name'], 0, -1).'&amp;';
          $about = FALSE;
        } else {
          $ext = substr($arr['name'], strrpos($arr['name'],'.')+1);
          if(!in_array($ext, explode(',', $CFG['DDIR']['ico']))) $ext = 'file';
          $path = 'zip.php?f='.$file['id'].'&amp;i='.$i;
          $about = round($arr['size']/1024, 1).'kb';
        }
        $name = htmlspecialchars(basename($arr['name']));
        $data[] = array('name'=>$name, 'path'=>$path, 'type'=>$ext, 'about'=>$about);
      }
    }
    $zip->close();
    $res = fopen($cached, 'wb');
    fwrite($res, serialize($data));
    fclose($res);
    unset($data);
  }

  $arr = unserialize(file_get_contents($cached));
  $cnt = count($arr);
  $lim = $USER['np']+2;
  $tmpl->Vars['FILES'] = array();
  $tmpl->Vars['FILES'] = array_slice($arr, $n, $lim);
  $tmpl->UseNav();
  $tmpl->Vars['NAV']['pos'] = $n;
  $tmpl->Vars['NAV']['total'] = $cnt;
  $tmpl->Vars['NAV']['limit'] = $lim;
  $tmpl->Vars['NAV']['add'] = 'f='.$file['id'].'&amp;d='.htmlspecialchars($dir);

}

echo $tmpl->Parse('ddir/zip.tmpl');

?>