<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/headers.php');
$compress->Enable();

$tmpl = new template;

if(isset($_POST['text'])){

  $tmpl->Vars['TITLE'] = 'Результаты поиска';

  $text =& $_POST['text'];
  if(!trim($text))
    raise_error('Пoлe пoискa нe зaпoлнeнo.', 'search.php?'.SID);

  if(false!==strpos($text, 'жопа'))
    raise_error('Опа-опа! Нашлась жопа.. O_o');

  if(false!==strpos($text, 'хуй'))
    raise_error('Ой-ой, пля! Нашлась хуйня.. O_o');

  $mysql = new mysql;
  $text = $mysql->EscapeString(htmlspecialchars($text));
  $mysql->Query('SELECT SQL_CALC_FOUND_ROWS `blogs`.`name`,`blogs`.`owner`,`users`.`login`
FROM `blogs` LEFT JOIN `users` ON `blogs`.`owner`=`users`.`id`
WHERE `users`.`login` LIKE "%'.$text.'%"');
  $tmpl->Vars['RESULTS'] = array();
  while($arr = $mysql->FetchAssoc()){
    $tmpl->Vars['RESULTS'][] = $arr;
  }
  $tmpl->Vars['VIEWRES'] = TRUE;

} else {

  $tmpl->Vars['TITLE'] = 'Найти блог';
  $tmpl->Vars['VIEWRES'] = FALSE;
  $online->Add('Блoги (пoиcк)');

}

echo $tmpl->Parse('blogs/search.tmpl');

?>