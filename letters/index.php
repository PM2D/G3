<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

$mysql = new mysql;
// обнуляем количество новых сообщений в сессии
$USER['newl'] = 0;
// вывод отдельного сообщения
if(isset($_GET['l'])) {

  $tmpl->Vars['TITLE'] = 'Просмотр сообщения';
  $id = intval($_GET['l']);
  // получаем сообщение
  $mysql->Query('SELECT `letters`.*,`users`.`login` '.
	'FROM `letters` LEFT JOIN `users` ON `letters`.`uid`=`users`.`id` '.
	'WHERE `letters`.`id`='.$id.' AND `letters`.`to`='.$USER['id'].' LIMIT 1');
  $letter = $mysql->FetchAssoc();
  // форматируем время
  $letter['time'] = format_date($letter['time']);
  // делаем сообщение уже не новым
  if($letter['new']) {
    $mysql->Update('letters', array('new'=>0), '`id`='.$id);
  }

  $tmpl->Vars['LETTER'] = $letter;

  echo $tmpl->Parse('letters/view.tmpl');
// вывод списка сообщений
} else {

  $tmpl->Vars['TITLE'] = 'Личные сообщения';

  $n =& getvar('n');
  $n = intval($n);

  $mysql->Query('SELECT SQL_CALC_FOUND_ROWS `letters`.*,`users`.`login` '.
	'FROM `letters` LEFT JOIN `users` ON `letters`.`uid`=`users`.`id` '.
	'WHERE `to`='.$USER['id'].' ORDER BY `letters`.`new` DESC,`letters`.`id` DESC LIMIT '.$n.','.$USER['np']);
  $tmpl->Vars['LETTERS'] = array();
  while($letter = $mysql->FetchAssoc()) {
    $letter['time'] = format_date($letter['time']);
    $tmpl->Vars['LETTERS'][] = $letter;
  }

  $tmpl->UseNav();
  $tmpl->Vars['NAV']['pos'] = $n;
  $tmpl->Vars['NAV']['total'] = $mysql->GetFoundRows();
  $tmpl->Vars['NAV']['limit'] = $USER['np'];

  echo $tmpl->Parse('letters/inbox.tmpl');

  $online->Add('Личные сообщения');

}

$mysql->Close();

?>