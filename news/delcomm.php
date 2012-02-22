<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/headers.php');
$compress->Enable();

if(2>$USER['state']) raise_error('Доступ запрещён.');

$cid =& getvar('cid');
$cid = intval($cid);

$mysql = new mysql;
$mysql->Delete('news_comms', '`id`='.$cid.' LIMIT 1');
$mysql->Query('OPTIMIZE TABLE `news_comms`');

$tmpl = new template;
$tmpl->Vars['TITLE'] = 'Удаление комментария';
$tmpl->Vars['MESSAGE'] = 'Комментарий удaлeн.';
$tmpl->Vars['BACK'] = 'comments.php?nid='.$cid.'&amp;'.SID;
echo $tmpl->Parse('notice.tmpl');

?>