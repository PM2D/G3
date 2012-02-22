<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
Header('Content-Type: application/xhtml+xml; charset=UTF-8');
Header('Cache-Control: no-cache, no-store, must-revalidate');
echo('<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru"><head><title>-Установка-</title>
<style type="text/css">
body { background: #404040; color: #A0A0A0 }
a:link,a:visited { color: #0000FF; text-decoration: none }
a:hover,a:active { color: #EE0000; text-decoration: underline }
.ok { color: #00BB00; font-weight: bolder; }
.err { color: #BB0000; font-weight: bolder; }
</style></head><body>
<div style="background: #5080B0 url(bg.gif); margin: 2%; border: 2px solid #000000; padding: 2%; text-align: center; color: #000000">');

if (file_exists($_SERVER['DOCUMENT_ROOT'].'/var/.installed')) {
   exit('<b>Внимание!</b><br />'.
	'Блокировка активна. В целях безопасности при активной блокировке продолжение установки будет невозможно.<br />'.
	'Для снятия блокировки необходимо удалить файл /var/.installed.'.
	'</div></body></html>');
}

if (substr($_SERVER['DOCUMENT_ROOT'], -1) == '/') $_SERVER['DOCUMENT_ROOT'] = substr($_SERVER['DOCUMENT_ROOT'], 0, -1);

if (!extension_loaded('mysqli')) {
  exit('<span class="err">Продолжение установки невозможно т.к. расширение mysqli не обнаружено.'.
	'Используйте no-mysqli патч если доступен.</span></div></body></html>');

}
if (substr(phpversion(), 0, 1) < 5) {
  exit('<span class="err">Установка и работа скрипта невозможна т.к. ваша версия PHP < 5</span></div></body></html>');
}

class mysql {

  public $res;
  public $error;
  static private $obj;
  static public $connected = FALSE;

  public function __construct($DBHOST, $DBUSER, $DBPASS, $DBNAME) {
    if (isset(mysql::$obj->thread_id)) return;
    mysql::$obj = new mysqli;
    global $CFG;
    mysql::$obj->init();
    mysql::$obj->options(MYSQLI_INIT_COMMAND, 'SET NAMES utf8');
    mysql::$obj->real_connect($DBHOST, $DBUSER, $DBPASS, $DBNAME);
    if (mysql::$obj->thread_id) {
      mysql::$connected = TRUE;
    } else {
      $this->error = mysql::$obj->error;
    }
  }

  public function Query($sql) {
    $this->res = mysql::$obj->query($sql);
    if (FALSE===$this->res) $this->error =& mysql::$obj->error;
    return $this->res;
  }

  public function EscapeString($str) {
    return mysql::$obj->real_escape_string($str);
  }

  public function Insert($table, array $data) {
    $fields = '';
    $vals = '';
    foreach ($data as $key=>$val) {
     $fields .= ',`'.$key.'`';
     $vals .= ",'".$val."'";
    }
    $fields = substr($fields, 1);
    $vals = substr($vals, 1);
    $this->res = $this->query('INSERT INTO `'.$table.'` ('.$fields.') VALUES('.$vals.')');
    return (FALSE===$this->res) ? FALSE : TRUE;
  }

  public function Close() {
    mysql::$obj->close();
    unset(mysql::$obj->thread_id, $this->res);
  }

}

// функция для получения GET переменных
function &getvar($idx){
  if (!isset($_GET[$idx])) $_GET[$idx] = NULL;
  return $_GET[$idx];
}

// функция для получения POST переменных
function &postvar($idx){
  if (!isset($_POST[$idx])) $_POST[$idx] = NULL;
  return $_POST[$idx];
}


$step =& getvar('s');

switch ($step) {

 case 1:
  include('step1.php');
 break;

 case 2:
  include('step2.php');
 break;

 case 3:
  include('step3.php');
 break;

 case 4:
  include('step4.php');
 break;

 default:
  echo('<b>Дoбpo пoжaлoвaть в cкpипт уcтaнoвки Gear-3 CMS</b><br />'.
	'Перед тем как приступить к установке, необходимо создать, если её еще нет, базу данных и пользователя для доступа к ней.<br />'.
	'Как правило это можно сделать с помощью контрольной панели предоставляемой хостингом.<br />'.
	'<b>Внимание!</b><br />'.
	'Данное ПО распространяется на условиях <a href="lgpl-3.0.txt">GNU LGPL v3</a> (<a href="lgplrus.txt">перевод</a>) лицензии.<br />'.
	'Продолжая установку и использование данного ПО вы соглашаетесь с пунктами данной лицензии.<hr />'.
	'[<a href="index.php?s=1">Продолжить</a>]');
 break;

}

echo('</div></body></html>');
?>