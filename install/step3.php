<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
if(basename($_SERVER['PHP_SELF'])!='index.php') exit;

echo('<b>Шаг 4. Создание таблиц в БД</b><hr />');
$CFG = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/etc/globals.conf', TRUE);
$mysql = new mysql($CFG['MYSQL']['dbhost'], $CFG['MYSQL']['dbuser'], $CFG['MYSQL']['dbpass'], $CFG['MYSQL']['dbname']);
$data = explode(';', file_get_contents('tables.sql'));
$cnt = count($data);
$errs = 0;

try {
  for($i=0; $i<$cnt; $i++) {
    $data[$i] = trim($data[$i]);
    if($data[$i] && !$mysql->Query($data[$i])) {
      throw new Exception($mysql->error); break;
    }
  }
} catch (Exception $e) {
  print('<span class="err">Ошибка SQL: '.$e->GetMessage().'</span><br />');
  $errs++;
}

if(1>$errs) {
  print('<span class="ok">Процесс создания структуры таблиц блaгoпoлучно завершён.</span><hr />'.
	'[<a href="index.php?s=4">Продолжить</a>]');
} else {
  print('<hr />Всего ошибок: '.$errs.'<br />Проверьте правильность файла tables.sql и попробуйте заново.');
}

?>
