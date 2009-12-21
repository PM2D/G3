<?php
// This file is a part of GIII (g3.steelwap.org)
define('CMS_VERSION', '3.2.0_rc3');

// функция подсчета времени генерации
function perf() {
  static $start = 0;
  list($usec, $sec) = explode(' ', microtime());
  if($start) return round($usec + $sec - $start, 4);
  else $start = ($usec + $sec);
}

perf();

// автозагрузка вспомогательных классов из lib
function __autoload($classname) {
  include($_SERVER['DOCUMENT_ROOT'].'/lib/'.$classname.'.php');
}

function exception_handler($e) {
  raise_error('Unhandled exception:<br />'.$e->getMessage()."\n");
}
set_exception_handler('exception_handler');

// класс онлайн
final class online {

  static public $count = 0;
  public $arr;
  public $user = array();
  private $path;

  // конструктор класса. считывает текущие онлайн-данные
  public function __construct() {
    $this->path = $_SERVER['DOCUMENT_ROOT'].'/var/online.dat';
    $this->arr = file($this->path);
    $c = online::$count = count($this->arr);
    $t = $GLOBALS['TIME']-300;
    for($i=0; $i<$c; $i++){
      $a = explode('|:|', $this->arr[$i]);
      if($a[3]<$t){
        unset($this->arr[$i]);
        online::$count--;
      } else {
        $this->user[$a[0]] = array($a[6], $a[7]);
      }
    }
  }
  // Добавление пользователя в онлайн
  public function Add($where) {
    global $USER;
    $in[0] = $USER['id'];
    $in[1] = $USER['login'];
    $in[2] = htmlspecialchars(strtok($_SERVER['HTTP_USER_AGENT'],' '));
    for($i=0; $i<online::$count; $i++) {
      $a = explode('|:|', $this->arr[$i]);
      if($a[0]==$in[0] && $a[2]==$in[2]) {
        unset($this->arr[$i]);
        online::$count--;
        break;
      }
    }
    $in[3] = $GLOBALS['TIME'];
    $in[4] = $where;
    $in[5] = htmlspecialchars($_SERVER['REQUEST_URI']);
    $in[6] = $USER['status'];
    $in[7] = $USER['sdescr'];
    $this->user[$in[0]] = array($in[6], $in[7]);
    array_unshift($this->arr, implode('|:|', $in)."\n");
    $f = fopen($this->path, 'w');
    flock($f, LOCK_EX);
    fputs($f, implode(NULL, $this->arr));
    flock($f, LOCK_UN);
    fclose($f);
    online::$count++;
  }
  // подсчет пользователей онлайн по REQUEST_URI
  public function CountIn($place = NULL) {
    if(NULL==$place) return 0;
    $cnt = 0;
    $len = strlen($place);
    for($i=0; $i<online::$count; $i++) {
      $a = explode('|:|', $this->arr[$i]);
      // REQUEST_URI может быть неполным
      if($place==substr($a[5], 0, $len)) $cnt++;
    }
    return $cnt;
  }
  // Получение номера онлайн-статуса пользователя
  public function GetStatus($uid) {
    return isset($this->user[$uid]) ? $this->user[$uid][0] : 0;
  }
  // Получение статусного сообщения пользователя
  public function GetSDescr($uid) {
    return isset($this->user[$uid]) ? $this->user[$uid][1] : NULL;
  }

}

// класс для сжатия выводимого контента
final class compress {

  static public $mode = 'off';
  static public $level = 0;

  public function __construct($level = 3) {
    compress::$level = $level;
    if(isset($_SERVER['HTTP_TE'])) {
      $_SERVER['HTTP_ACCEPT_ENCODING'] =& $_SERVER['HTTP_TE'];
    }
    if(1>$level) $_SERVER['HTTP_ACCEPT_ENCODING'] = 'identity';
  }

  public function Enable() {
    if(FALSE!==strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate')) {
      header('Content-Encoding: deflate');
      compress::$mode = 'deflate';
    } elseif(FALSE!==strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
      header('Content-Encoding: gzip');
      compress::$mode = 'gzip';
    }
    if(ini_get('zlib.output_compression')) {
      ob_start();
    } else {
      ob_start('compress::Out');
    }
    register_shutdown_function('ob_end_flush');
  }

  static public function Out($buff) {
    switch(compress::$mode) {
     case 'deflate':
       return gzdeflate($buff, compress::$level);
     break;
     case 'gzip':
       return gzencode($buff, compress::$level);
     break;
     default:
       return FALSE;
     break;
    }
  }

}

// MySQL class 0.5.2 by DreamDragon (aka PM2D)
class mysql {

  public $res;
  public $error;
  static private $obj;
  static public $connected = FALSE;

  public function __construct() {
    if(mysql::$connected) return;
    mysql::$obj = new mysqli;
    global $CFG;
    mysql::$obj->init();
    mysql::$obj->options(MYSQLI_INIT_COMMAND, 'SET NAMES utf8');
    mysql::$obj->real_connect($CFG['MYSQL']['dbhost'], $CFG['MYSQL']['dbuser'], $CFG['MYSQL']['dbpass'], $CFG['MYSQL']['dbname']);
    if(mysql::$obj->error) {
      exit('Невозможно подключиться к базе данных: '.mysql::$obj->error);
    } else {
      mysql::$connected = TRUE;
    }
  }

  public function Query($sql) {
    $this->res = mysql::$obj->query($sql);
    if(FALSE===$this->res) $this->error = mysql::$obj->error;
    return $this->res;
  }

  public function GetFoundRows() {
    $arr = $this->query('SELECT FOUND_ROWS()')->fetch_row();
    return $arr[0];
  }

  public function GetLastId() {
    return mysql::$obj->insert_id;
  }

  public function EscapeString($str) {
    return mysql::$obj->real_escape_string($str);
  }

  public function FetchAssoc($res = FALSE) {
    if(FALSE===$res) $res = $this->res;
    return $res->fetch_assoc();
  }

  public function FetchRow($res = FALSE) {
    if(FALSE===$res) $res = $this->res;
    return $res->fetch_row();
  }

  public function GetField($field, $table, $where) {
    $arr = $this->query('SELECT '.$field.' FROM `'.$table.'` WHERE '.$where.' LIMIT 1')->fetch_row();
    return $arr[0];
  }

  public function GetRow($fields, $table, $where) {
    return $this->query('SELECT '.$fields.' FROM `'.$table.'` WHERE '.$where.' LIMIT 1')->fetch_assoc();
  }

  public function Update($table, array $data, $where) {
    $set = '';
    foreach($data as $field=>$value) {
      if(empty($value)) $value = 'NULL';
      elseif($value[0]!='`') $value = "'".$value."'";
      $set .= ',`'.$field.'`='.$value;
    }
    $set = substr($set,1);
    $this->query('UPDATE `'.$table.'` SET '.$set.' WHERE '.$where);
    return (FALSE===$this->res) ? FALSE : TRUE;
  }

  public function Insert($table, array $data) {
    $fields = '';
    $vals = '';
    foreach($data as $key=>$val) {
     $fields .= ',`'.$key.'`';
     $vals .= ",'".$val."'";
    }
    $fields = substr($fields, 1);
    $vals = substr($vals, 1);
    $this->res = $this->query('INSERT INTO `'.$table.'` ('.$fields.') VALUES('.$vals.')');
    return (FALSE===$this->res) ? FALSE : TRUE;
  }

  public function Delete($table, $where = NULL) {
    if(NULL!=$where) $this->res = $this->query('DELETE FROM `'.$table.'` WHERE '.$where);
    else $this->res = $this->query('TRUNCATE TABLE `'.$table.'`');
    return (FALSE===$this->res) ? FALSE : TRUE;
  }

  public function IsExists($table, $where) {
    return (bool)$this->query('SELECT `id` FROM `'.$table.'` WHERE '.$where.' LIMIT 1')->num_rows;
  }

  public function Close() {
    mysql::$obj->close();
    mysql::$connected = FALSE;
    unset(mysql::$obj->thread_id, $this->res, $this->error);
  }

}

// класс транслита
final class translit {

  public $replace = array();

  public function __construct() {
    $this->replace = array(
     'A'=>'А','a'=>'а',
     'B'=>'Б','b'=>'б',
     'V'=>'В','v'=>'в',
     'G'=>'Г','g'=>'г',
     'D'=>'Д','d'=>'д',
     'E'=>'Е','e'=>'е',
     'yo'=>'ё','Yo'=>'Ё',
     'Zh'=>'Ж','zh'=>'ж',
     'Z'=>'З','z'=>'з',
     'I'=>'И','i'=>'и',
     'J'=>'Й','j'=>'й',
     'K'=>'К','k'=>'к',
     'L'=>'Л','l'=>'л',
     'M'=>'М','m'=>'м',
     'N'=>'Н','n'=>'н',
     'O'=>'О','o'=>'о',
     'P'=>'П','p'=>'п',
     'R'=>'Р','r'=>'р',
     'S'=>'С','s'=>'с',
     'T'=>'Т','t'=>'т',
     'U'=>'У','u'=>'у',
     'F'=>'Ф','f'=>'ф',
     'H'=>'Х','h'=>'х',
     'C'=>'Ц','c'=>'ц',
     'Ch'=>'Ч','ch'=>'ч',
     'Sh'=>'Ш','sh'=>'ш',
     'Sch'=>'Щ','sch'=>'щ',
     '"'=>'ъ',"'"=>'ь',
     'Y'=>'Ы','y'=>'ы',
     'Je'=>'Э','je'=>'э',
     'Yu'=>'Ю','yu'=>'ю',
     'Ya'=>'Я','ya'=>'я'
    );
  }

  public function FromTrans(&$text) {
    $text = strtr($text, $this->replace);
  }

  public function ToTrans(&$text) {
    $text = strtr($text, array_flip($this->replace));
  }

}

// класс для смайлов
final class smiles {

  public $replace = array();

  public function __construct() {
    $this->replace = unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/etc/smiles.cfg'));
  }

  public function ToImg(&$text) {
    $text = strtr($text, $this->replace);
  }

  public function FromImg(&$text) {
    $text = strtr($text, array_flip($this->replace));
  }

}

// класс конвертации тегов
final class tags {

  static public function ToHtm(&$str) {
    $str = preg_replace('!\[img\](http://'.$_SERVER['HTTP_HOST'].'/[^\[<]+)\[/img\]!is',
			'<img src="$1" alt="image" />', $str);
    $str = preg_replace('!(^|[\s])\[b\]([^\[<]+)\[\/b\]([\s]|$)!is', '$1<b>$2</b>$3', $str);
    $str = preg_replace('!(^|[\s])\[i\]([^\[<]+)\[\/i\]([\s]|$)!is', '$1<i>$2</i>$3', $str);
    $str = preg_replace('!(^|[\s])\[u\]([^\[<]+)\[\/u\]([\s]|$)!is', '$1<u>$2</u>$3', $str);
    $str = preg_replace('!(^|[\s])\[s\]([^\[<]+)\[\/s\]([\s]|$)!is', '$1<s>$2</s>$3', $str);
    $str = preg_replace('!\[color=([\S]+)\]([^\[<]+)\[\/color\]!is', '<span style="color:$1">$2</span>', $str);
    $str = preg_replace('!\[url=((f|ht)tp[s]?://[^<> \n]+?)\](.+?)\[/url\]!i', '<a href="/go.php?$1">$3</a>', $str);
    $str = preg_replace('!(^|[\s])(http://[\S]+)([^\s]|$)!i', '$1<small><a href="/go.php?$2">$2</a></small>$3', $str);
    $str = preg_replace('!(^|[\s])([a-z0-9\.]{2,}\.(ru|tv|com|net|org|info|mobi)[\S]{0,})([\s]|$)!is', '$1<small><a href="/go.php?http://$2">$2</a></small>$4', $str);
  }

  static public function FromHtm(&$str) {
    $str = strtr($str, array('<img src="'=>'[img]', '" alt="image" />'=>'[/img]', '</span>'=>'[/color]'));
    $str = preg_replace('/<(\/{0,1}[ubis])>/', '[$1]', $str);
    $str = preg_replace('/<span\ style="color:([\S]+)">/', '[color=$1]', $str);
    $str = preg_replace('!<a href="/go\.php\?([\S]+?)">([\S]+?)</a>!', '[url=$1]$2[/url]', $str);
  }

}

// функция записи в лог
function to_log($str) {
  if(date('d', filemtime($_SERVER['DOCUMENT_ROOT'].'/var/log.dat')) != date('d', $GLOBALS['TIME'])){
    fclose(fopen($_SERVER['DOCUMENT_ROOT'].'/var/log.dat', 'w'));
  };
  $str = strtr($str, "\n", ' ');
  $f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/log.dat', 'a');
  fwrite($f, date('d.m H:i', $GLOBALS['TIME']).'||'.$str."\n");
  fclose($f);
}

// функция для лёгкого получения GET переменных
function &getvar($idx) {
  if(!isset($_GET[$idx])) $_GET[$idx] = NULL;
  return $_GET[$idx];
}

// функция для лёгкого получения POST переменных
function &postvar($idx) {
  if(!isset($_POST[$idx])) $_POST[$idx] = NULL;
  return $_POST[$idx];
}

// функция вывода ошибок
function raise_error($desc, $back = FALSE) {
  global $USER;
  if(!isset($USER['tmpl'])) $USER['tmpl'] = 'Default';
  $tmpl = new template;
  if(!headers_sent()) $tmpl->SendHeaders();
  $tmpl->Vars['MESSAGE'] = $desc;
  $tmpl->Vars['BACK'] = $back;
  echo $tmpl->Parse('error.tmpl');
  exit;
}

// функции проверки доступности модуля
function IsModInstalled($name) {
  static $modules = NULL;
  if(NULL == $modules) $modules = explode(',', $GLOBALS['CFG']['MODS']['active']); // с 3.1 проверка в active, а не installed
  return in_array($name, $modules);
}

// функция форматирования даты (сегодня/вчера/позавчера)
function format_date($time) {
  static $now = NULL;
  if(NULL == $now) $now = floor($GLOBALS['TIME']/86400);
  $day = floor($time/86400);
  if($now==$day) return 'ceгoдня в '.date('G:i', $time);
  elseif(($now-1)==$day) return 'вчepa в '.date('G:i', $time);
  elseif(($now-2)==$day) return 'пoзaвчepa в '.date('G:i', $time);
  else return date('d.m.y в G:i', $time);
}

// подключаем скрипты инициализации
require($_SERVER['DOCUMENT_ROOT'].'/etc/init.php');

// подключаем шаблонизатор
require($_SERVER['DOCUMENT_ROOT'].'/etc/tmpleng.php');

?>
