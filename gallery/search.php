<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

if(isset($_POST['text'])){

  $tmpl->Vars['TITLE'] = 'Результаты поиска';
  $text =& $_POST['text'];
  if(!trim($text))
    raise_error('Пoлe пoискa нe зaпoлнeнo.', 'search.php?'.SID);
  $mysql = new mysql;
  $text = $mysql->EscapeString(htmlspecialchars($text));
  $mysql->Query('SELECT SQL_CALC_FOUND_ROWS `gallery_albums`.`title`,`gallery_albums`.`uid`,`users`.`login`
FROM `gallery_albums` LEFT JOIN `users` ON `gallery_albums`.`uid`=`users`.`id`
WHERE `users`.`login` LIKE "%'.$text.'%"');
  $tmpl->Vars['RESULTS'] = array();
  while($arr = $mysql->FetchAssoc()){
    $tmpl->Vars['RESULTS'][] = $arr;
  }
  $tmpl->Vars['VIEWRES'] = TRUE;

} else {

  $tmpl->Vars['TITLE'] = 'Поиск альбома';
  $tmpl->Vars['VIEWRES'] = FALSE;
  $online->Add('Галерея (пoиcк)');

}

echo $tmpl->Parse('gallery/search.tmpl');

?>