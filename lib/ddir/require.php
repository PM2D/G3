<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
// функция автотранслитерации имени файла/папки
function fname($str) {
 $str = strtr($str, array('_'=>' ', 'Dt'=>'.'));
 if('-'==$str[0]) {
   static $obj = FALSE;
   if(!$obj) $obj = new translit;
   $str = substr($str, 1);
   $obj->FromTrans($str);
 }
 return $str;
}

// функция считающая кол-во файлов в папке
function dir2int($dir) {
  $c = 0;
  $d = dir($dir);
  while($str = $d->Read()) {
    if($str[0]!='.' && $str[0]!='_' && $str!='index.php') {
      if(FALSE===strpos($str, '.')) $c += dir2int($dir.'/'.$str);
      else $c++;
    }
  }
  $d->Close();
  return $c;
}

?>
