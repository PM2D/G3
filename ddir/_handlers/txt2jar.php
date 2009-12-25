<?php
// This file is a part of G3 (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

if(!extension_loaded('zip'))
  raise_error('Расширение zip недоступно.');

$file =& getvar('f');
$file .= '.txt';

if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/ddir'.$file))
  raise_error('Нет такого файла.');

// сборщик "мусора" (более 10 минут == мусор)
$d = opendir($_SERVER['DOCUMENT_ROOT'].'/tmp/jar');
while($str = readdir($d)){
 if($str[0]!='.' && filemtime($_SERVER['DOCUMENT_ROOT'].'/tmp/jar/'.$str)<($TIME-600)){
   unlink($_SERVER['DOCUMENT_ROOT'].'/tmp/jar/'.$str);
 }
}

$name = basename($file, '.txt');
if($name[0]=='-') $name = substr($name, 1);

$tmpfile = '/tmp/jar/'.$name.'.jar';

$text = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/ddir'.$file);

// кодировка, конечно, позорная, но выбора нет :)
mb_language('ru');
mb_internal_encoding('CP1251');
$text = mb_convert_encoding($text, 'CP1251', 'UTF-8');

copy('txt2jar.zip', $_SERVER['DOCUMENT_ROOT'].$tmpfile);

$zip = new ZipArchive;
$zip->open($_SERVER['DOCUMENT_ROOT'].$tmpfile) or raise_error('Ошибка открытия zip файла.');

$arr = str_split($text, 25600);
unset($text);
$cnt = sizeof($arr);

function encode($str1){
  $len = strlen($str1);
  $str2 = '';
  for($i=0; $i<$len; $i++){
    $str2 .= $str1[$i].chr(0);
  }
  return $str2;
}

$props = $zip->getFromName('props.ini');
$props .= chr(0).chr(10).chr(0).encode('J/textfile.txt.label=1');
for($i=1; $i<$cnt; $i++){
  $props .= chr(10).chr(0).encode('J/textfile'.$i.'.txt.label='.($i+1));
}
$props .= chr(10);

$zip->addFromString('props.ini', $props);

$manifest =
'Manifest-Version: 1.0'."\r\n".
'MicroEdition-Configuration: CLDC-1.0'."\r\n".
'MicroEdition-Profile: MIDP-1.0'."\r\n".
'MIDlet-Name: '.$name."\r\n".
'MIDlet-Vendor: G3'."\r\n".
'MIDlet-1: '.$name.', /icon.png, br.BookReader'."\r\n".
'MIDlet-Version: 1.6'."\r\n".
'MIDlet-Info-URL: '.$_SERVER['HTTP_HOST']."\r\n".
'MIDlet-Delete-Confirm: GoodBye =)';
$zip->addFromString('META-INF/MANIFEST.MF', $manifest);

$zip->addFromString('textfile.txt', $arr[0]);
for($i=1; $i<$cnt; $i++){
  $zip->addFromString('textfile'.$i.'.txt', $arr[$i]);
}
$zip->close();

header('Location: '.$tmpfile, TRUE, 301);

?>