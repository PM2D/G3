<?php
// This file is a part of GIII (g3.steelwap.org)

// получение текущего времени в переменную, чтобы больше не получать его потом.
$TIME = $_SERVER['REQUEST_TIME'];

// анти-DoS (загрузка не чаще чем раз в секунду) если не требуется, то лучше убрать
//include_once($_SERVER['DOCUMENT_ROOT'].'/etc/ipaf.php');

// определение модели телефона с оперы-мини и замена http_user_agent на него
if(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']))
  $_SERVER['HTTP_USER_AGENT'] = '(OperaMini)'.$_SERVER['HTTP_X_OPERAMINI_PHONE_UA'];
/*
  Oпределение реального ip. Внимание:
  теоретически может использоваться как для определения реального ip,
  так и злоумышленником для его подмены через отправляемые http залоловки
*/
if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
  $_SERVER['REMOTE_ADDR'] =& $_SERVER['HTTP_X_FORWARDED_FOR'];

// стартуем сессию
//session_save_path($_SERVER['DOCUMENT_ROOT'].'/tmp/sess');
session_start();
// если вдруг константа SID не объявлена, то объявляем её
//if(!defined('SID')) define('SID', session_id());

// получаем глобальные настройки
$CFG = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/etc/globals.conf', TRUE);

// создаем более удобную ссылку на сессию пользователя
$USER =& $_SESSION['U'];

// если сессия пользователя не существует, то подключаем скрипты инициализации пользователя
if(!isset($_SESSION['U'])){
  require($_SERVER['DOCUMENT_ROOT'].'/etc/userinit.php');
}

// проверка новых записок раз в 60сек
if(3!=$USER['id'] && 1>$USER['newl'] && $USER['lchk']<($TIME-60)){
  if(IsModInstalled('letters')) {
    $mysql = new mysql;
    $USER['newl'] = $mysql->GetField('COUNT(*)', 'letters', '`to`='.$USER['id'].' AND `new`>0');
  }
  $USER['lchk'] = $TIME;
}

// создание классов онлайн-счетчика и сжатия
$compress = new compress($CFG['CORE']['gzlevel']);
$online = new online;

?>