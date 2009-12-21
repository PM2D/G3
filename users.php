<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Список пользователей';
$tmpl->Vars['OPTIONS'] = isset($_GET['options']);

$mysql = new mysql;

$n =& getvar('n');
$n = intval($n);

$string = 'SELECT SQL_CALC_FOUND_ROWS `id`,`login`,`posts`,`status`,`last` FROM `users` ';

// получение строки для поиска (если используется)
if(isset($_GET['search'][3]) && $_GET['search'] = trim($_GET['search'])){
  $search =& $_GET['search'];
  $search = stripslashes(htmlspecialchars($search));
  $tmpl->Vars['SEARCH'] = $search;
  $search = $mysql->EscapeString($search);
  $search = ' LIKE "%'.$search.'%"';
} else {
  $tmpl->Vars['SEARCH'] = FALSE;
  $search = NULL;
}

// выбор условия запроса
$type =& getvar('type');
switch($type){
 // по полю "откуда"
 case 'from':
  $string .= 'WHERE `from`'.$search;
 break;
 // по полу
 case 'male':
  $string .= 'WHERE `sex`=1';
 break;
 case 'female':
  $string .= 'WHERE `sex`=2';
 break;
 // только с icq
 case 'icq':
  $string .= 'WHERE `icq`>0';
 break;
 // только с JabberID
 case 'jabber':
  $string .= 'WHERE `jabber`!=""';
 break;
 // только с e-mail
 case 'mail':
  $string .= 'WHERE `email`!=""';
 break;
 // только с сайтом
 case 'site':
  $string .= 'WHERE `site`!=""';
 break;
 // только админов
 case 'adms':
  $string .= 'WHERE `state`=2';
 break;
 // только модераторов
 case 'mdrs':
  $string .= 'WHERE `state`=1';
 break;
 // поиск по логину
 case 'login':
  $string .= 'WHERE `login`'.$search;
 break;
 // поиск по имени
 case 'name':
  $string .= 'WHERE `name`'.$search;
 break;
 // по умолчанию всех
 default:
  $string .= 'WHERE 1';
 break;
}
// кроме гостей
$string .= ' AND `id`!=3 ';

// выбор типа сортировки
$order =& getvar('order');
$order = intval($order);
switch($order){
 case 1: $order = 'posts'; break;
 case 2: $order = 'login'; break;
 case 3: $order = 'regdat'; break;
 default: $order = 'last'; break;
}
// направление сортировки
$rever =& getvar('rev');
$rever = ('on'==$rever) ? 'ASC' : 'DESC';
// добавление сортировки к запросу
$string .= 'ORDER BY `'.$order.'` '.$rever;

// производим запрос
// /*debug*/ echo $string;
$mysql->Query($string.' LIMIT '.$n.','.($USER['np']+4));
// загоняем данные из sql запроса в массив шаблона
$tmpl->Vars['LIST'] = array();
while($arr = $mysql->FetchAssoc()){
  $arr['last'] = date('d.m.y в g:i', $arr['last']);
  $tmpl->Vars['LIST'][] = $arr;
}

$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = $mysql->GetFoundRows();
$tmpl->Vars['NAV']['limit'] = $USER['np'] + 4;
$tmpl->Vars['NAV']['add'] = 'type='.$type.'&amp;order='.$order.'&amp;rev='.$rever.'&amp;search='.getvar('search');

$mysql->Close();

$online->Add('Cпиcoк пoльзoвaтeлeй');

echo $tmpl->Parse('users.tmpl');

?>