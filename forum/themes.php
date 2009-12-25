<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$rid =& getvar('r');
$rid = intval($rid);

$n = getvar('n');
$n = intval($n);

$mysql = new mysql;
// получаем данные текущего форума
$forum = $mysql->GetRow('*', 'forums', '`id`='.$rid);
if(!$forum) raise_error('Возможно нет такого форума.');
// получаем имя "родительского" форума, если он есть
if(0<$forum['rid']) $forum['pname'] = $mysql->GetField('name', 'forums', '`id`='.$forum['rid']);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Список тем/форумов ('.$forum['name'].')';
$tmpl->Vars['FORUM'] = $forum;
$tmpl->Vars['SUBFORUMS'] = array();

$que = $mysql->Query('SELECT * FROM `forums` WHERE `rid`='.$rid);
while($arr = $mysql->FetchAssoc($que)){

  $theme = $mysql->GetRow('*', 'forum_themes', '`rid`='.$arr['id'].' ORDER BY `time` DESC');

  if($theme) {
    $arr['themeid'] = $theme['id'];
    $arr['themename'] = $theme['name'];
    $arr['themelastuid'] = $theme['lastuid'];
    $arr['themelastuser'] = $theme['lastuser'];
    $arr['themetime'] = format_date($theme['time']);
  } else $arr['themeid'] = FALSE;

  $tmpl->Vars['SUBFORUMS'][] = $arr;

}

$tmpl->Vars['THEMES'] = array();

$mysql->Query('SELECT * FROM `forum_themes` WHERE `rid`='.$rid.'
ORDER BY `fixed` DESC, `time` DESC LIMIT '.$n.','.$USER['np']);

while($arr = $mysql->FetchAssoc()){

  $arr['time'] = format_date($arr['time']);
  $tmpl->Vars['THEMES'][] = $arr;

}

$mysql->Close();

$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = $forum['count'];
$tmpl->Vars['NAV']['limit'] = $USER['np'];
$tmpl->Vars['NAV']['add'] = 'r='.$rid;

$online->Add('Фopум ('.$forum['name'].')');

echo $tmpl->Parse('forum/themes.tmpl');

?>