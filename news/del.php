<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$nid =& getvar('nid');
$nid = intval($nid);

if(2>$USER['state']) raise_error('Доступ запрещён.');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

$mysql = new mysql;

$mysql->Delete('news', '`id`='.$nid.' LIMIT 1');
// удаление оценок файла
$rating = new rating;
$rating->Remove('/news/'.$nid);

$mysql->Query('OPTIMIZE TABLE `news`');

$tmpl->Vars['TITLE'] = 'Удаление новости';
$tmpl->Vars['MESSAGE'] = 'Hoвocть удaлeнa.';
$tmpl->Vars['BACK'] = 'index.php?'.SID;
echo $tmpl->Parse('notice.tmpl');

?>