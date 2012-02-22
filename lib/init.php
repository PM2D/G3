<?php
// This file is a part of AugurCMS (g3.pm2d.ru)

// получение текущего времени в переменную, чтобы больше не получать его потом.
$TIME = $_SERVER['REQUEST_TIME'];

// анти-DoS (загрузка не чаще чем раз в секунду) если не требуется, то лучше убрать
//include_once($_SERVER['DOCUMENT_ROOT'].'/lib/ipaf.php');

// модификация глобального User-Agent на User-Agent устройства, если используется Opera-mini
if (isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']))
  $_SERVER['HTTP_USER_AGENT'] = '(OperaMini)'.$_SERVER['HTTP_X_OPERAMINI_PHONE_UA'];
/*
  Oпределение реального ip. Внимание:
  теоретически может использоваться как для определения реального ip,
  так и злоумышленником для его подмены через отправляемые http залоловки
*/
if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
  $_SERVER['REMOTE_ADDR'] =& $_SERVER['HTTP_X_FORWARDED_FOR'];
elseif (isset($_SERVER['HTTP_X_REAL_IP']))
  $_SERVER['REMOTE_ADDR'] =& $_SERVER['HTTP_X_REAL_IP'];

// стартуем сессию
session_start();

// получаем глобальные настройки из конфига
$CFG = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/etc/globals.conf', TRUE);

// создаем более удобную ссылку на сессию пользователя
$USER =& $_SESSION['U'];

// если сессия пользователя не существует, то подключаем скрипты инициализации пользователя
if (!isset($_SESSION['U'])) {
  require($_SERVER['DOCUMENT_ROOT'].'/etc/userinit.php');
}

// проверка новых записок раз в 60сек
if (3!=$USER['id'] && 1>$USER['newl'] && $USER['lchk']<($TIME-60)) {
  if (IsModInstalled('letters')) {
    $mysql = new mysql;
    $USER['newl'] = $mysql->GetField('COUNT(*)', 'letters', '`to`='.$USER['id'].' AND `new`>0');
  }
  $USER['lchk'] = $TIME;
}

// создание экземпляров объектов онлайн-счетчика и сжатия страниц
$compress = new compress($CFG['CORE']['gzlevel']);
$online = new online;

?>