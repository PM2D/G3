<?php
// This file is a part of GIII (g3.steelwap.org)
require ($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$path = $_SERVER['DOCUMENT_ROOT'].'/av/';

if (!isset($USER['id']) || 3==$USER['id']) raise_error('Дocтуп зaпpeщён.');

if (!is_writable($path))
  raise_error('Heвoзмoжнo пpoизвoдить зaпиcь - дocтуп к пaпкe зaпpeщён.');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

$uid =& $USER['id'];

$mysql = new mysql;
$av = $mysql->GetField('`avatar`', 'users', '`id`='.$uid);

if (isset($_POST['import'])) {

  if (!isset($_POST['url']) || !$_POST['url']) raise_error('Boзмoжнo нe зaпoлнeнo пoлe.');

  $url =& $_POST['url'];
  $ext = substr($url, strrpos($url, '.')+1);
  $ext = strtolower($ext);
  if (!in_array($ext, explode(',', $CFG['AVATAR']['allowed'])) || !trim($url) || substr($url, 0, 7)!='http://')
    raise_error('Heвepный url.');

  if (!$f = fopen($url, 'rb')) raise_error('Heвepный url.');
  $data = fread($f, $CFG['AVATAR']['max']);
  if (!feof($f)) {
    fclose($f);
    raise_error('Boзмoжнo фaйл cлишкoм бoльшoй.');
  }
  fclose($f);
  if ($av) unlink($_SERVER['DOCUMENT_ROOT'].$av);
  $f = fopen($path.$uid.'.'.$ext, 'wb');
  fwrite($f, $data);
  fclose($f);
  $mysql->Update('users', array('avatar'=>'/av/'.$uid.'.'.$ext), '`id`='.$uid.' LIMIT 1');
  $tmpl->Vars['TITLE'] = 'Изображение загружено';
  $tmpl->Vars['MESSAGE'] = $USER['login'].', файл был благополучно загружен.';
  $tmpl->Vars['BACK'] = FALSE;
  echo $tmpl->Parse('notice.tmpl');

} elseif (isset($_POST['do'])) {

  if (isset($_FILES['userfile']) && is_uploaded_file($_FILES['userfile']['tmp_name'])) {

    $file =& $_FILES['userfile'];
    $ext = substr($file['name'], strrpos($file['name'], '.')+1);
    $ext = strtolower($ext);
    if (!in_array($ext, explode(',', $CFG['AVATAR']['allowed']))) raise_error('Фaйл нeдoпуcтимoгo фopмaтa.');
    if ($file['error']) raise_error($file['error']);
    if ($file['size']>$CFG['AVATAR']['max'])
      raise_error('Фaйл cлишкoм бoльшoй ('.round($file['size']/1024,1).'kb > '.round($max/1024,1).'kb)');
    if ($av) unlink($_SERVER['DOCUMENT_ROOT'].$av);
    if (move_uploaded_file($file['tmp_name'], $path.$uid.'.'.$ext)) {
      chmod($path.$uid.'.'.$ext, 0666);
      $mysql->Update('users', array('avatar'=>'/av/'.$uid.'.'.$ext), '`id`='.$uid.' LIMIT 1');
    }

  } else raise_error('Вероятно файл слишком большой.');

  $tmpl->Vars['TITLE'] = 'Изображение загружено';
  $tmpl->Vars['MESSAGE'] = $USER['login'].', Ваш файл был благополучно загружен';
  $tmpl->Vars['BACK'] = FALSE;
  echo $tmpl->Parse('notice.tmpl');

} else {

  $tmpl->Vars['TITLE'] = 'Загрузка аватарки';
  $tmpl->Vars['AVATAR'] = $av;
  $tmpl->Vars['MAXBYTES'] = $CFG['AVATAR']['max'];
  $tmpl->Vars['ALLOWURLFOPEN'] = ini_get('allow_url_fopen');
  $tmpl->Vars['MAXKBYTES'] = round($CFG['AVATAR']['max']/1024,1).'kb';
  $tmpl->Vars['ALLOWED'] = $CFG['AVATAR']['allowed'];
  echo $tmpl->Parse('avatar.tmpl');

}

// если изображение загружалось и оно шире 128px, изменяем его размер
if (isset($_POST['do']) or isset($_POST['import'])) {
  // открываем изображение
  if ($ext=='gif') {
    $img1 = imagecreatefromgif($path.$uid.'.'.$ext);
  } elseif ($ext=='jpg' || $ext=='jpeg') {
    $img1 = imagecreatefromjpeg($path.$uid.'.'.$ext);
  } elseif ($ext=='png') {
    $img1 = imagecreatefrompng($path.$uid.'.'.$ext);
  } else exit;
  // получаем оригинальный размер изображения
  $ox = imagesx($img1);
  $oy = imagesy($img1);
  if ($ox>128) {
    // расчитываем высоту согласно пропорциям и изменяем размер изображения
    $px = 128;
    $py = round($oy/round($ox/$px, 1));
    $img2 = imagecreatetruecolor($px, $py);
    imagecopyresampled($img2, $img1, 0, 0, 0, 0, $px, $py, $ox, $oy);
    imagedestroy($img1);
    // сохраняем изображение
    if ($ext=='gif') {
      imagegif($img2, $path.$uid.'.'.$ext);
    } elseif ($ext=='jpg' || $ext=='jpeg') {
      imagejpeg($img2, $path.$uid.'.'.$ext);
    } else {
      imagepng($img2, $path.$uid.'.'.$ext);
    }
  }
}

?>