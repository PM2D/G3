<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

$mysql = new mysql;

if(isset($_GET['s'])){

  $tmpl->Vars['TITLE'] = 'Результаты поиска';
  $search =& $_GET['s'];
  $type =& getvar('type');
  if(!trim($search))
    raise_error('Пoлe пoискa нe зaпoлнeнo.', 'search.php?'.SID);
  $pos = getvar('n');
  $pos = intval($pos);
  $search = $mysql->EscapeString(htmlspecialchars($search));
  $type = $mysql->EscapeString($type);
  if($type && $type!='any')
    $type = 'AND `type`="'.$type.'"';
  else
    $type = NULL;
  $mysql->Query('SELECT SQL_CALC_FOUND_ROWS * FROM `filex_files` '.
	'WHERE (`title` LIKE "%'.$search.'%" OR `about` LIKE "%'.$search.'%") '.$type.' LIMIT '.$pos.','.$USER['np']);
  $tmpl->Vars['FILES'] = array();
  while($arr = $mysql->FetchAssoc()){
    $tmpl->Vars['FILES'][] = $arr;
  }
  $tmpl->Vars['VIEWRES'] = TRUE;
  $tmpl->UseNav();
  $tmpl->Vars['NAV']['pos'] = $pos;
  $tmpl->Vars['NAV']['total'] = $mysql->GetFoundRows();
  $tmpl->Vars['NAV']['limit'] = $USER['np'];
  $tmpl->Vars['NAV']['add'] = 'c='.$search;

} else {

  $tmpl->Vars['TITLE'] = 'Поиск файлов';
  $mysql->Query('SELECT `types` FROM `filex_cats`');
  $tmpl->Vars['TYPES'] = array();
  while($arr = $mysql->FetchRow()){
    $tmpl->Vars['TYPES'] = array_merge($tmpl->Vars['TYPES'], explode(' ', $arr[0]));
  }
  $tmpl->Vars['TYPES'] = array_unique($tmpl->Vars['TYPES']);
  $tmpl->Vars['VIEWRES'] = FALSE;
  $online->Add('Обменник (пoиcк)');

}

echo $tmpl->Parse('filex/search.tmpl');

?>