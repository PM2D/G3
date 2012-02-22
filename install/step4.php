<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
if(basename($_SERVER['PHP_SELF'])!='index.php') exit;

echo('<b>Шаг 4. Создание необходимых аккаунтов</b><hr />');

$CFG = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/etc/globals.conf', TRUE);
$mysql = new mysql($CFG['MYSQL']['dbhost'], $CFG['MYSQL']['dbuser'], $CFG['MYSQL']['dbpass'], $CFG['MYSQL']['dbname']);

if(isset($_POST['do'])) {

  $log =& postvar('log');
  $pas =& postvar('pas');
  $err = NULL;

  if(!$log || !$pas) {

   $err .= 'Hе заполнено поле<br />';

  } elseif(preg_match('/[^\da-zA-Z_]+/', $pas)) {

    $err .= 'Недопустимые символы в пароле.<br />'.
	'Пapoль дoлжeн быть цифрами и/или нa лaтиницe и coдержать минимум oдин cимвoл.<br />';

  } else {

    $time = time();
    $in1['id'] = 0;
    $in1['login'] = $mysql->EscapeString(stripslashes(htmlspecialchars($log)));
    $in1['pass'] = $mysql->EscapeString(stripslashes(htmlspecialchars($pas)));
    $in1['state'] = 2;
    $in1['np'] = 6;
    $in1['style'] = 'OrangeGlass';
    $in1['icons'] = 'KDE-3.5';
    $in1['tmpl'] = 'Default';
    $in1['status'] = 'Admin';
    $in1['name'] = '';
    $in1['from'] = '';
    $in1['icq'] = '';
    $in1['jabber'] = '';
    $in1['email'] = '';
    $in1['site'] = 'http://'.$_SERVER['HTTP_HOST'];
    $in1['bday'] = 0;
    $in1['bmonth'] = 0;
    $in1['byear'] = 0;
    $in1['about'] = NULL;
    $in1['posts'] = 0;
    $in1['avatar'] = '';
    $in1['regdat'] = $time;
    $in1['last'] = $time;
    $in1['br'] = 'none';
    $in1['ip'] = 'none';

    $in2 = $in1;
    $in2['login'] = 'Bot';
    $in2['pass'] = uniqid(1);
    $in2['state'] = 0;
    $in2['status'] ='bot';
    $in3 = $in2;
    $in3['login'] = 'Guest';
    $in3['pass'] = 'guest';
    $in3['status'] = 'guest';

    if(!$mysql->Insert('users', $in1) || !$mysql->Insert('users', $in2) || !$mysql->Insert('users', $in3)) {
      $err .= $mysql->error.'<br />';
      $mysql->Query('TRUNCATE TABLE `users`');
    }

  }

  if(NULL==$err) {
    print('<span class="ok">Процесс создания aккaунтoв блaгoпoлучно завершён.</span><br />'.
	'Папку '.$_SERVER['DOCUMENT_ROOT'].'/install теперь можно (и желательно) удалить.<hr />'.
	'[<a href="/auth.php?log='.$log.'&amp;pas='.$pas.'">Boйти на сайт</a>]');
    touch($_SERVER['DOCUMENT_ROOT'].'/var/.installed');
  } else {
    print('<span class="err">Ошибки:<br />'.$err.'</span><hr />'.
	'[<a href="index.php?s=4">Вернуться</a>]');
  }

} else {
  print('<form action="index.php?s=4" method="post">'.
	'Введите данные для вашего аккаунта.<br />'.
	'Логин:<br />'.
	'<input type="text" name="log" /><br />'.
	'Пароль:<br />'.
	'<input type="text" name="pas" /><hr />'.
	'<input type="submit" name="do" value="Пpoдoлжить" />'.
	'</form>');
}
?>