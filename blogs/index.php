<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

if(!IsModInstalled('blogs'))
  raise_error('Данный модуль на данный момент не установлен.');

$n =& getvar('n');
$n = intval($n);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Список блогов';

$mysql = new mysql;
// если ли у пользователя блог?
$tmpl->Vars['HAVEBLOG'] = $haveblog = $mysql->IsExists('blogs', '`owner`='.$USER['id']);
// если есть, то получаем новые комментарии
if($haveblog) {
  $que = $mysql->Query('SELECT COUNT(*) as `cnt`,`pid` FROM `blogs_comms`
WHERE `buid`='.$USER['id'].' AND `time`>'.$USER['last'].'
AND `uid`!='.$USER['id'].' GROUP BY `pid`');
  $tmpl->Vars['NEWCOMMS'] = array();
  while($arr = $mysql->FetchAssoc($que)) {
    $title = $mysql->GetField('`title`', 'blogs_posts', '`id`='.$arr['pid']);
    $tmpl->Vars['NEWCOMMS'][] = array('ptitle'=>$title, 'pid'=>$arr['pid'], 'count'=>$arr['cnt']);
  }
};

// список блогов

$mysql->Query('SELECT SQL_CALC_FOUND_ROWS `blogs`.*,`users`.`login`
FROM `blogs` LEFT JOIN `users` ON `blogs`.`owner`=`users`.`id`
ORDER BY `blogs`.`time` DESC LIMIT '.$n.','.$USER['np']);

$tmpl->Vars['BLOGS'] = array();
while($arr = $mysql->FetchAssoc()){
  $arr['time'] = format_date($arr['time']);
  $tmpl->Vars['BLOGS'][] = $arr;
}

$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = $mysql->GetFoundRows();
$tmpl->Vars['NAV']['limit'] = $USER['np'];

$mysql->Close();

$online->Add('Блoги (cписок блoгoв)');

echo $tmpl->Parse('blogs/index.tmpl');
?>