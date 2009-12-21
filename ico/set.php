<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$file =& getvar('f');
$file = stripslashes($file);

// поверка на ошибки
if(strpos($file, ':') || $file[0]=='.') raise_error('А может не надо?');
if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/ico/'.$file)) raise_error('Нет такой темы.');

// считываем настройки/информацию темы если есть
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/ico/'.$file.'/theme.ini')){
  $theme = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/ico/'.$file.'/theme.ini');
} else {
  $theme = array('name'=>strtr($file, '_', ' '), 'descr'=>'N/A', 'author'=>'N/A');
}

// если нет своего сообщения установки, то используем обычное
if(!isset($theme['setnotice']) || !$theme['setnotice'])
  $theme['setnotice'] = 'Тема иконок изменена.';

if(3!=$USER['id']){
  $mysql = new mysql;
  $icons = $mysql->EscapeString($file);
  $mysql->Update('users', array('icons'=>$icons), '`id`='.$USER['id'].' LIMIT 1');
  $mysql->Close();
};

$USER['icons'] = $file;

$tmpl = new template;
$tmpl->Vars['TITLE'] = 'Смена иконок';
$tmpl->Vars['MESSAGE'] = $theme['setnotice'];
$tmpl->Vars['BACK'] = FALSE;
$tmpl->SendHeaders();
$compress->Enable();
echo $tmpl->Parse('notice.tmpl');

?>