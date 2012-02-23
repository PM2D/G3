<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$file =& getvar('f');
$file = stripslashes($file);

// проверка на ошибки
if (strpos($file, ':') || $file[0]=='.') raise_error('А может не надо?');
if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$file)) raise_error('Нет такой темы шаблонов.');
if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/css/'.$file)) raise_error('Нет стилей для данной темы шаблонов.');

// считываем настройки/информацию темы если есть
if (file_exists($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$file.'/theme.ini')) {
  $theme = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$file.'/theme.ini');
} else {
  $theme = array('name'=>strtr($file, '_', ' '), 'descr'=>'N/A', 'author'=>'N/A');
}

// если нет своего сообщения установки, то используем обычное
if (!isset($theme['setnotice']) || !$theme['setnotice'])
  $theme['setnotice'] = 'Тема шаблонов изменена.';
// если для темы задан стиль по умолчанию, и он существует, то используем его
if (isset($theme['defstyle']) && file_exists($_SERVER['DOCUMENT_ROOT'].'/css/'.$file.'/'.$theme['defstyle'])) {
  $style = $theme['defstyle'];
// иначе если стиль по умолчанию не задан, пробуем стиль из папки Default
} elseif (file_exists($_SERVER['DOCUMENT_ROOT'].'/css/'.$file.'/Default')) {
  $style = 'Default';
// если же и его нет, то смотрим папку и берем первый попавшийся стиль
} else {
  $d = dir($_SERVER['DOCUMENT_ROOT'].'/css/'.$file);
  while ($str = $d->read()){
    if ('.'!=$str[0] && 'index.php'!=$str && 'set.php'!=$str) { $style = $str; break; }
  }
  $d->close();
}

// если не гость, то обновляем данные в базе
if (3!=$USER['id']) {
  $mysql = new mysql;
  $tmpl = $mysql->EscapeString($file);
  $style = $mysql->EscapeString($style);
  $mysql->Update('users', array('tmpl'=>$tmpl, 'style'=>$style), '`id`='.$USER['id'].' LIMIT 1');
  $mysql->Close();
}

// обновляем данные в сессии
$USER['tmpl'] = $file;
$USER['style'] = $style;

// выводим уведомление
$tmpl = new template;
$tmpl->Vars['TITLE'] = 'Смена внешнего вида';
$tmpl->Vars['MESSAGE'] = $theme['setnotice'];
$tmpl->Vars['BACK'] = FALSE;
$tmpl->SendHeaders();
$compress->Enable();
echo $tmpl->Parse('notice.tmpl');

?>