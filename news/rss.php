<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

header('Content-Type: text/xml; charset=UTF-8');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
//header('Expires: Sun, 03-May-1998 16:00:00 GMT');
$compress->Enable();

$tmpl = new template;

$mysql = new mysql;
$mysql->Query('SELECT `time`,`title`,`text` FROM `news` ORDER BY `id` DESC LIMIT 32');
$tmpl->Vars['NEWS'] = array();
while($arr = $mysql->FetchAssoc()) {
  $arr['time'] = date('d.m.y в G:i', $arr['time']);
  $arr['text'] = htmlspecialchars(strip_tags($arr['text']));
  $tmpl->Vars['NEWS'][] = $arr;
}

$mysql->Close();

echo $tmpl->Parse('news/rss.tmpl');

?>