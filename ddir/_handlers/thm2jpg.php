<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

// SE Theme Reader 0.2 (c) by PM-DD aka PM2D aka DreamDragon (265386035)

// если в URL всякая фигня, то ничего не делаем
if(!isset($_GET['f']) || substr($_GET['f'],-3)!='thm' || strpos($_GET['f'],':')) exit;
// открываем тему как tar-архив
$tar = new tar;
$tar->OpenTar($_SERVER['DOCUMENT_ROOT'].'/ddir'.$_GET['f']);
// только если файл Theme.xml имеется, иначе нет смысла вообще что-либо делать
if($tar->containsFile('Theme.xml')) {
  $arr = $tar->getFile('Theme.xml');
  // парсим xml файл
  $xml_parser = xml_parser_create();
  xml_parse_into_struct($xml_parser, $arr['file'], $vals, $names);
  xml_parser_free($xml_parser);
  // проверка на существование фонового изображения
  // иначе нельзя будет определить размер экрана и продолжать нет смысла
  if(isset($names['BACKGROUND_IMAGE'])) {
   // вытаскиваем файл и создаем из него изображение
   $arr = $tar->getFile($vals[$names['BACKGROUND_IMAGE'][0]]['attributes']['SOURCE']);
   $img_main = imagecreatefromstring($arr['file']);
   // получаем размер изображения
   $x_main = imagesx($img_main);
   $y_main = imagesy($img_main);
   // если есть картинка статус-бара, то получаем ее и накладываем на общую картинку
   if(isset($names['STANDBY_STATUSBAR_IMAGE'])) {
     $arr = $tar->getFile($vals[$names['STANDBY_STATUSBAR_IMAGE'][0]]['attributes']['SOURCE']);
     $img = imagecreatefromstring($arr['file']);
     $x = imagesx($img);
     $y = imagesy($img);
     imagecopy($img_main, $img, 0, 0, 0, 0, $x, $y);
     imagedestroy($img);
   };
   // если существует изображение нажатой левой софт-кнопки,
   // то получаем его и накладываем на общую картинку
   if(isset($names['SOFTKEY_LEFT_PRESSED_IMAGE'])) {
     $arr = $tar->getFile($vals[$names['SOFTKEY_LEFT_PRESSED_IMAGE'][0]]['attributes']['SOURCE']);
     $img = imagecreatefromstring($arr['file']);
     $x = imagesx($img);
     $y = imagesy($img);
     imagecopy($img_main, $img, 0, $y_main-$y, 0, 0, $x, $y);
     imagedestroy($img);
   // иначе пробуем работать с изображением не нежатой софт-кнопки
   } elseif(isset($names['STANDBY_SOFTKEY_IMAGE'])) {
     $arr = $tar->getFile($vals[$names['STANDBY_SOFTKEY_IMAGE'][0]]['attributes']['SOURCE']);
     $img = imagecreatefromstring($arr['file']);
     $x = imagesx($img);
     $y = imagesy($img);
     imagecopy($img_main, $img, 0, $y_main-$y, 0, 0, $x, $y);
     imagedestroy($img);
   };
   // если существует цвет текста нажатой левой софт-кнопки, то получаем его
   if(isset($names['SOFTKEY_TEXT_LEFT_PRESSED'])){
     $val = $vals[$names['SOFTKEY_TEXT_LEFT_PRESSED'][0]]['attributes']['COLOR'];
     $red = hexdec(substr($val,-6,2));
     $green = hexdec(substr($val,-4,2));
     $blue = hexdec(substr($val,-2));
   // иначе задаем белый цвет
   } else {
     $red = 250;
     $green = 250;
     $blue = 250;
   }
   // расчитываем кое-какое смещение
   (176<$y_main) ? $align=22 : $align=16;
   // рисуем текст левой софт-кнопки
   imagestring($img_main, 3, $x_main-round($x_main/10)*9, $y_main-$align, 'OK', imagecolorallocate($img_main, $red, $green, $blue) );
   // если существует цвет текста правой софт-кнопки, то получаем его
   if(isset($names['SOFTKEY_RIGHT'])){
     $val = $vals[$names['SOFTKEY_RIGHT'][0]]['attributes']['COLOR'];
     $red = hexdec(substr($val,-6,2));
     $green = hexdec(substr($val,-4,2));
     $blue = hexdec(substr($val,-2));
   } else {
    $ref = 250;
     $green = 250;
     $blue = 250;
   }
   // рисуем текст правой софт-кнопки
   imagestring($img_main, 3, round($x_main/10)*7, $y_main-$align, 'Menu', imagecolorallocate($img_main, $red, $green, $blue) );
   // рисуем адрес сайта
   imagestring($img_main, 3, 10, round($y_main/2)-4, $_SERVER['HTTP_HOST'], 0);
   // выводим готовое изображение
   header('Content-Type: image/jpeg');
   imagejpeg($img_main, NULL, 70);
  };
};

?>