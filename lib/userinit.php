<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
// эти скрипты выполняются при первом заходе пользователя на сайт

// получаем номер текущего дня для некоторых дальнейших операций
$day = date('d', $TIME);

// если есть реферал и он не содержит адрес текущего сайта
if(isset($_SERVER['HTTP_REFERER']) && FALSE===strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) && trim($_SERVER['HTTP_REFERER'])) {
  // если день последнего изменения файла не равен текущему, то очищаем файл
  if(date('d', filemtime($_SERVER['DOCUMENT_ROOT'].'/var/refs.dat')) != $day) {
    fclose(fopen($_SERVER['DOCUMENT_ROOT'].'/var/refs.dat', 'w'));
  };
  // добавляем реферал в файл
  $f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/refs.dat', 'a');
  fwrite($f, $_SERVER['HTTP_REFERER']."\n");
  fclose($f);
}

// получаем данные гостевого аккаунта и вписываем их в сессию
$mysql = new mysql;
$USER = $mysql->GetRow('`id`,`login`,`state`,`np`,`icons`,`tmpl`', 'users', '`id`=3');

// для онлайн-статуса
$USER['status'] = 1;
$USER['sdescr'] = NULL;

// для автопроверки (записок)
$USER['lchk'] = 0;
$USER['newl'] = 0;

// информация о том, что пользователь только что вошел
$USER['jentered'] = TRUE;
// но в конце выполнения скрипта это уже не должно быть так
function unjentered() {
  $GLOBALS['USER']['jentered'] = FALSE;
}
register_shutdown_function('unjentered');

// автовыбор темы шаблонов
if($CFG['AS']['active']) include($_SERVER['DOCUMENT_ROOT'].'/etc/autoselect.php');

// считываем настройки/информацию темы шаблонов, если есть
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/theme.ini')) {
  $theme = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/theme.ini');
}

// если для темы задан стиль по умолчанию, и он существует, то используем его
if(isset($theme['defstyle']) && file_exists($_SERVER['DOCUMENT_ROOT'].'/css/'.$USER['tmpl'].'/'.$theme['defstyle'])) {
  $USER['style'] = $theme['defstyle'];
// иначе если стиль по умолчанию не задан, пробуем стиль из папки Default
} elseif(file_exists($_SERVER['DOCUMENT_ROOT'].'/css/'.$USER['tmpl'].'/Default')) {
  $USER['style'] = 'Default';
// если же и его нет, то смотрим папку и берем первый попавшийся стиль
} else {
  $d = dir($_SERVER['DOCUMENT_ROOT'].'/css/'.$USER['tmpl']);
  while($str = $d->read()) {
    if('.'!=$str[0] && 'index.php'!=$str && 'set.php'!=$str) { $USER['style'] = $str; break; }
  }
  $d->close();
}


// подсчет хостов
// только если пользователь не авторизуется, чтобы не было двух записей за раз
if(basename($_SERVER['SCRIPT_NAME'])!='auth.php'){

  // если день последнего изменения файла не равен текущему, то очищаем файл
  if(date('d', filemtime($_SERVER['DOCUMENT_ROOT'].'/var/today.dat')) != $day){
    fclose(fopen($_SERVER['DOCUMENT_ROOT'].'/var/today.dat', 'w'));
  };

  // проверяем есть ли уже пользователь с таким ua и ip
  $arr = file($_SERVER['DOCUMENT_ROOT'].'/var/today.dat');
  $hosts = sizeof($arr);
  $exists = FALSE;
  $ua = htmlspecialchars($_SERVER['HTTP_USER_AGENT']);
  $ip = htmlspecialchars($_SERVER['REMOTE_ADDR']);
  for($i=0; $i<$hosts; $i++){
    if($USER['id'].'||'.$USER['login'].'||'.$ua.'||'.$ip."\n"==$arr[$i]){
      $exists = TRUE;
      break;
    };
  }

  // если нет, то добавляем его логин, ua и ip
  if(!$exists){
    $f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/today.dat', 'a');
    fwrite($f, $USER['id'].'||'.$USER['login'].'||'.$ua.'||'.$ip."\n");
    fclose($f);
    // а также обновляем число хостов
    $f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/hosts.dat', 'w');
    fwrite($f, $hosts+1);
    fclose($f);
  }

}

?>