<?php
// This file is a part of GIII (g3.steelwap.org)
$n = isset($_GET['n']) ? intval($_GET['n']) : 0;
$mysql = new mysql;
$mysql->Query('SELECT SQL_CALC_FOUND_ROWS `id`,`login`,`posts`,`regdat`,`last` FROM `users`
WHERE `last`<'.($TIME-2592000).' AND `posts`<50 AND `id`!=3 ORDER BY `posts`,`last` LIMIT '.$n.','.($USER['np']+2));
$tmpl->Vars['USERS'] = array();
while($arr = $mysql->FetchAssoc()){
  $arr['regdat'] = date('j.m.y', $arr['regdat']);
  $arr['last'] = date('j.m.y', $arr['last']);
  $tmpl->Vars['USERS'][] = $arr;
}
$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = $mysql->GetFoundRows();
$tmpl->Vars['NAV']['limit'] = $USER['np']+2;
$tmpl->Vars['NAV']['add'] = 'mod='.$mod;
echo $tmpl->Parse('admin/users.tmpl');
?>
