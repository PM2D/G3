<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$mysql = new mysql;

// Log Out:
if(isset($_GET['logout'])) {
  // получаем данные гостевого акааунта и записываем их в сессию
  $USER = array_merge($USER, $mysql->GetRow('`id`,`login`,`state`,`np`,`style`,`icons`,`tmpl`', 'users', '`id`=3'));
  // онлайн статус
  $USER['status'] = 1;
  $USER['sdescr'] = NULL;
  // удаляем cookies
  setcookie('log', NULL, $TIME-3600, '/auth.php', $_SERVER['HTTP_HOST']);
  setcookie('pas', NULL, $TIME-3600, '/auth.php', $_SERVER['HTTP_HOST']);
  Header('Location: /?'.SID);
  include($_SERVER['DOCUMENT_ROOT'].'/etc/include/logout.php');
  exit;
};

if((isset($_GET['log']) || isset($_COOKIE['log'])) && 3==$USER['id']) {

  $login = (isset($_COOKIE['log']) && $_COOKIE['log']) ? $_COOKIE['log'] : substr(getvar('log'), 0, 127);
  $login = stripslashes(htmlspecialchars($login));
  $login = $mysql->EscapeString($login);
  // получаем необходимые данные аккаунта пользователя с логином $login
  $arr = $mysql->GetRow('`id`,`login`,`pass`,`state`,`np`,`style`,`icons`,`tmpl`,`last`',
			'users',
			'`login`="'.$login.'"');
  if(!isset($_COOKIE['pas'])) $_COOKIE['pas'] = NULL;
  // если пароль пустой или не подходит выводим ошибку
  if(empty($arr['pass']) || (getvar('pas')!=$arr['pass'] && $_COOKIE['pas']!=md5($arr['pass']))) {
    // удаляем cookies, чтобы не мешались
    setcookie('log', NULL, $TIME-3600, '/auth.php', $_SERVER['HTTP_HOST']);
    setcookie('pas', NULL, $TIME-3600, '/auth.php', $_SERVER['HTTP_HOST']);
    raise_error('Возможно Bы ввeли нeвepный лoгин или пapoль', 'auth.php?'.SID);
  }
  // если надо, "запоминаем" данные в cookies
  if(isset($_GET['rem'])) {
    setcookie('log', $arr['login'], $TIME+2592000, '/auth.php', $_SERVER['HTTP_HOST']);
    setcookie('pas', md5($arr['pass']), $TIME+2592000, '/auth.php', $_SERVER['HTTP_HOST']);
  }
  // для костылей (/etc/include/auth.php)
  $login = $arr['login'];
  $pass = $arr['pass'];
  // убираем пароль из массива и записываем данные аккаунта в сессию пользователя
  unset($arr['pass']);
  $USER = array_merge($USER, $arr);
  // онлайн статус
  $USER['status'] = isset($_COOKIE['status']) ? intval($_COOKIE['status']) : 1;
  $USER['sdescr'] = isset($_COOKIE['sdescr']) ? substr(stripslashes(htmlspecialchars($_COOKIE['sdescr'])), 0, 128) : NULL;
  // автопроверка новых писем
  $USER['lchk'] = 0;
  $USER['newl'] = 0;

  // проверяем есть ли уже пользователь с таким ua и ip в файле today.dat
  $arr = file($_SERVER['DOCUMENT_ROOT'].'/var/today.dat');
  $hosts = count($arr);
  $exists = FALSE;
  $ua = htmlspecialchars($_SERVER['HTTP_USER_AGENT']);
  $ip = htmlspecialchars($_SERVER['REMOTE_ADDR']);
  for($i=0; $i<$hosts; $i++) {
    if($USER['id'].'||'.$USER['login'].'||'.$ua.'||'.$ip."\n"==$arr[$i]) {
      $exists = TRUE;
      break;
    };
  }

  // если нет, то добавляем его
  if(!$exists) {
    $f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/today.dat', 'a');
    fwrite($f, $USER['id'].'||'.$USER['login'].'||'.$ua.'||'.$ip."\n");
    fclose($f);
    // а также обновляем число хостов
    $f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/hosts.dat', 'w');
    fwrite($f, $hosts+1);
    fclose($f);
  };

};

if(1>$USER['state']) include($_SERVER['DOCUMENT_ROOT'].'/etc/bancheck.php');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

// если пользователь уже не гость:
if(3!=$USER['id']) {
  $tmpl->Vars['TITLE'] = 'Добро пожаловать, '.$USER['login'];
  // дата последнего захода:
  $tmpl->Vars['LAST'] = format_date($USER['last']);
  // есть ли свежие новости:
  if($tmpl->Vars['NEWS'] = IsModInstalled('news')) {
    $tmpl->Vars['NEW']['news'] = $mysql->GetField('COUNT(*)', 'news', '`time`>'.$USER['last']);
  }
  // былы ли новые посты на форуме:
  if($tmpl->Vars['FORUM'] = IsModInstalled('forum')) {
    $tmpl->Vars['NEW']['themes'] = $mysql->GetField('COUNT(*)', 'forum_themes', '`time`>'.$USER['last'].' AND `lastuid`!='.$USER['id']);
  }
  // были ли новые посты в блогах:
  if($tmpl->Vars['BLOGS'] = IsModInstalled('blogs')) {
    $tmpl->Vars['NEW']['blogs'] = $mysql->GetField('COUNT(*)', 'blogs', '`time`>'.$USER['last']);
  }
  // были ли новые файлы в фотогалерее:
  if($tmpl->Vars['GALLERY'] = IsModInstalled('gallery')) {
    $tmpl->Vars['NEW']['gallery'] = $mysql->GetField('COUNT(*)', 'gallery_albums', '`time`>'.$USER['last']);
  }
  // были ли новые файлы в обменнике:
  if($tmpl->Vars['FILEX'] = IsModInstalled('filex')) {
    $tmpl->Vars['NEW']['filex'] = $mysql->GetField('COUNT(*)', 'filex_cats', '`time`>'.$USER['last']);
  }
  $tmpl->Vars['NEW']['bdays'] = $mysql->GetField('COUNT(*)', 'users', '`bmonth`='.date('m', $TIME).' AND `bday`='.date('d', $TIME));
  $tmpl->Vars['NEW']['letters'] = $mysql->GetField('COUNT(*)', 'letters', '`to`='.$USER['id'].' AND `new`=1');
  // получаем ip и ua с которых пользователь был в последний раз
  $arr = $mysql->GetRow('`br`,`ip`', 'users', '`id`='.$USER['id']);
  $tmpl->Vars['UA'] = $arr['br'];
  $tmpl->Vars['IP'] = $arr['ip'];
  // обновляем ip и ua на текущие
  $mysql->Update(
		'users',
		array(
			'br' => $mysql->EscapeString($_SERVER['HTTP_USER_AGENT']),
			'ip' => $mysql->EscapeString($_SERVER['REMOTE_ADDR']),
			'last' => $TIME
		),
		'`id`='.$USER['id'].' LIMIT 1'
		);

  include($_SERVER['DOCUMENT_ROOT'].'/etc/include/auth.php');

} else {
  $tmpl->Vars['TITLE'] = 'Авторизация';
}

echo $tmpl->Parse('auth.tmpl');

?>
