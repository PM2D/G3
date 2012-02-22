<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
if (basename($_SERVER['PHP_SELF'])!='index.php') exit;

echo('<b>Шаг 2. Настройка подключения к БД</b><hr />');

if (isset($_POST['dbname']) && isset($_POST['dbuser']) && isset($_POST['dbhost']) && isset($_POST['dbpass'])) {

  $CFG['MYSQL']['dbname'] = postvar('dbname');
  $CFG['MYSQL']['dbuser'] = postvar('dbuser');
  $CFG['MYSQL']['dbhost'] = postvar('dbhost');
  $CFG['MYSQL']['dbpass'] = postvar('dbpass');
  $CFG['AVATAR'] = array('max'=>10240, 'allowed'=>'jpg,jpeg,gif');
  $CFG['AS']['active'] = FALSE;
  $CFG['AS']['wap'] = 'Default';
  $CFG['AS']['web'] = 'Default';
  $CFG['CORE']['gzlevel'] = 0;
  $CFG['MODS']['installed'] = NULL;
  $CFG['MODS']['active'] = NULL;

  $f = fopen($_SERVER['DOCUMENT_ROOT'].'/etc/globals.conf', 'w'); 
  foreach ($CFG as $section=>$array) {
    fwrite($f, '['.$section."]\n");
    foreach ($array as $key => $value) {
      if (!$value) {
        $value = 'false';
      } elseif (is_bool($value)) {
        $value = 'true';
      } elseif (is_string($value)) {
        $value = '"'.$value.'"';
      }
      fwrite($f, $key.' = '.$value."\n");
    }
    fwrite($f, "\n");
  }
  fclose($f);

  $mysql = new mysql($CFG['MYSQL']['dbhost'], $CFG['MYSQL']['dbuser'], $CFG['MYSQL']['dbpass'], $CFG['MYSQL']['dbname']);
  if (!mysql::$connected) {
    print('Ошибка: <span class="err">Невозможно подключиться к БД.</span><br />'.
	'Вероятно неверные имя пользователя, пароль, адрес хоста БД или имя БД<br />'.
	'<small>('.$mysql->error.')</small><br />'.
	'[<a href="index.php?s=2">Вернуться</a>]');
  } else {
    print('Статус: <span class="ok">Есть работающее подключение к БД</span><br />'.
	'[<a href="index.php?s=3">Продолжить</a>]');
  }
  $mysql->Close();

} else {

  echo('Введите данные для доступа к БД:'.
	'<form action="index.php?s=2" method="post">'.
	'Хост БД:<br />'.
	'<input type="text" name="dbhost" value="localhost" /><br />'.
	'Имя БД:<br />'.
	'<input type="text" name="dbname" /><br />'.
	'Имя пользователя:<br />'.
	'<input type="text" name="dbuser" /><br />'.
	'Пароль пользователя:<br />'.
	'<input type="text" name="dbpass" /><hr />'.
	'<input type="submit" value="Продолжить" />'.
	'</form>');

}
?>