<?php
// This file is a part of GIII (g3.steelwap.org)
if(basename($_SERVER['PHP_SELF'])!='index.php') exit;

echo('<b>Шаг 1. Проверка доступа к файлам/папкам</b><hr /><span class="err">');

$errs = 0;

function rwdirchk($dir) {
 $d = dir($dir);
 while($str = $d->read()) {
  if($str[0]!='.') {
   if(!is_writable($dir.'/'.$str)) {
    echo('Невозможно производить запись в '.$dir.'/'.$str.'<br />');
    $GLOBALS['errs']++;
   }
   if(!is_readable($dir.'/'.$str)) {
    echo('Heвозможно производить чтение из '.$dir.'/'.$str.'<br />');
    $GLOBALS['errs']++;
   }
  }
 }
 $d->close();
}

function rwfilechk($file) {
 if(!is_writable($file)) {
  echo('Невозможно производить запись в '.$file.'<br />');
  $GLOBALS['errs']++;
 }
 if(!is_readable($file)) {
  echo('Heвозможно производить чтение из '.$file.'<br />');
  $GLOBALS['errs']++;
 }
}

rwfilechk($_SERVER['DOCUMENT_ROOT'].'/var');
rwdirchk($_SERVER['DOCUMENT_ROOT'].'/var');
rwdirchk($_SERVER['DOCUMENT_ROOT'].'/var/cache');
rwdirchk($_SERVER['DOCUMENT_ROOT'].'/tmp');
rwfilechk($_SERVER['DOCUMENT_ROOT'].'/etc/globals.conf');
rwfilechk($_SERVER['DOCUMENT_ROOT'].'/etc/smiles.cfg');
rwfilechk($_SERVER['DOCUMENT_ROOT'].'/av');

echo('</span>');
if(0<$errs) {
  echo('<hr />Были обнаружены ошибки.<br />Разрешите чтение/запись указанных файлов/папок и попробуйте опять');
} else {
  echo('<span class="ok">Ошибок не найдено</span><hr />[<a href="index.php?s=2">Прoдoлжить</a>]');
}

?>