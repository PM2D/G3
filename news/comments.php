<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$nid =& getvar('nid');
$nid = intval($nid);

$mysql = new mysql;

$new = $mysql->GetRow('*', 'news', '`id`='.$nid);

if(!$new) raise_error('Нет такой новости');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Комментарии';
$tmpl->Vars['NEW'] = $new;
$tmpl->Vars['COMMS'] = array();

$mysql->Query('SELECT `news_comms`.*,`users`.`login`
FROM `news_comms` LEFT JOIN `users` ON `news_comms`.`uid`=`users`.`id`
WHERE `news_comms`.`nid`='.$nid);

while($arr = $mysql->FetchAssoc()) {
  $arr['time'] = format_date($arr['time']);
  $tmpl->Vars['COMMS'][] = $arr;
}

if(3==$USER['id'] && $CFG['NEWS']['guests']) include($_SERVER['DOCUMENT_ROOT'].'/ico/_misc/codegen.php');

$tmpl->Vars['ALLOWGUESTS'] = $CFG['NEWS']['guests'];

echo $tmpl->Parse('news/comments.tmpl');

?>