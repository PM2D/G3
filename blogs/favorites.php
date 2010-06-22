<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$n =& getvar('n');
$n = intval($n);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Избранное';

$mysql = new mysql;
// получаем список избранных авторов и формируем условие исходя из него
$favs = $mysql->GetField('`favorites`', 'blogs', '`owner`='.$USER['id']);
if(!$favs) raise_error('Cписoк избpaнныx aвтopoв пуcт.', 'index.php?'.SID);
$where = '(`uid`='.implode(' OR `uid`=',explode(' ',$favs[0])).')';
// производим запрос
$que = $mysql->Query('SELECT SQL_CALC_FOUND_ROWS `blogs_posts`.*,`users`.`login`
FROM `blogs_posts` LEFT JOIN `users` ON `blogs_posts`.`uid`=`users`.`id`
WHERE `time`>'.($TIME-172800).' AND '.$where.'
ORDER BY `time` DESC LIMIT '.$n.','.$USER['np']);
// получаем кол-во найденных результатов заранее до других запросов
$rows = $mysql->GetFoundRows();

$tmpl->Vars['FAVS'] = array();
while($arr = $mysql->FetchAssoc($que)) {
  $arr['comms'] = $mysql->GetField('COUNT(*)', 'blogs_comms', '`pid`='.$arr['id']);
  $arr['time'] = format_date($arr['time']);
  $tmpl->Vars['FAVS'][] = $arr;
}
$mysql->Close();

$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = $rows;
$tmpl->Vars['NAV']['limit'] = $USER['np'];

$online->Add('Блoги (избpaннoe)');

echo $tmpl->Parse('blogs/favorites.tmpl');

?>