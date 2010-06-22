<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');
if(1>$USER['state']) include($_SERVER['DOCUMENT_ROOT'].'/etc/bancheck.php');

$tid =& getvar('t');
$tid = intval($tid);

$n =& getvar('n');
$n = intval($n);

$m =& getvar('m');
$order = (0<$m) ? 'DESC' : 'ASC';

$mysql = new mysql;

$theme = $mysql->GetRow('*', 'forum_themes', '`id`='.$tid);

if(!$theme) raise_error('Hет такой темы');

if(isset($_GET['getlast'])) {
  $n = $USER['np'] * (ceil($theme['count']/$USER['np']) - 1);
  if(0>$n) $n = 0;
}

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Просмотр темы "'.$theme['name'].'"';
$tmpl->Vars['THEME'] = $theme;
$tmpl->Vars['DESC'] = $m;
$tmpl->Vars['POSTS'] = array();

$tmpl->Vars['FORUM'] = $mysql->GetRow('*', 'forums', '`id`='.$theme['rid']);

if($theme['rid'] != $theme['prid']) {
  $tmpl->Vars['SUBFORUM'] = $mysql->GetRow('*', 'forums', '`id`='.$theme['prid']);
} else {
  $tmpl->Vars['SUBFORUM'] = FALSE;
}

$res = $mysql->Query('SELECT `forum_posts`.*,`login`,`state`,`status`,`sex`,`posts`,`avatar`
FROM `forum_posts` LEFT JOIN `users` ON `forum_posts`.`uid`=`users`.`id`
WHERE `forum_posts`.`tid`='.$tid.' ORDER BY `forum_posts`.`id` '.$order.' LIMIT '.$n.','.$USER['np']);

if(0<$m) {
  $postnum = ($theme['count'] + 1) - $n;
} else {
  $postnum = $n;
}

while($arr = $mysql->FetchAssoc($res)) {
  if(0<$m) {
    $postnum--;
  } else {
    $postnum++;
  }
  $arr['num'] = $postnum;
  $arr['editable'] = (($USER['id']==$arr['uid'] AND $arr['time']>$TIME-21600) OR 0<$USER['state']) ? TRUE : FALSE;
  $arr['time'] = format_date($arr['time']);
  $arr['online'] = $online->GetStatus($arr['uid']);
  if(0<$arr['attid'] && IsModInstalled('filex')) {
    $arr['attach'] = $mysql->GetRow('*', 'filex_files', '`id`='.$arr['attid']);
    if(!$arr['attach']) $arr['attid'] = 0;
    $arr['attach']['size'] = round($arr['attach']['size']/1024, 1).'kb';
  }
  $tmpl->Vars['POSTS'][] = $arr;
}

$mysql->Close();

$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = $theme['count'];
$tmpl->Vars['NAV']['limit'] = $USER['np'];
$tmpl->Vars['NAV']['add'] = 't='.$tid.'&amp;m='.$m;

$online->Add('Фopум (читaeт тeму)');

echo $tmpl->Parse('forum/view.tmpl');

?>