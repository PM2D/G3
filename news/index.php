<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

if(!IsModInstalled('news'))
  raise_error('Данный модуль на данный момент не установлен.');

$n =& getvar('n');
$n = intval($n);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Новости';

$mysql = new mysql;

$rating = new rating;

// просмотр отдельной новости полным текстом
if(isset($_GET['nid'])) {
  $nid = intval($_GET['nid']);
  $new = $mysql->GetRow('`news`.*,`users`.`login`', 'news` LEFT JOIN `users` ON `news`.`uid`=`users`.`id', '`news`.`id`='.$nid);
  if(!$new) raise_error('Возможно нет такой новости');
  $new['time'] = format_date($new['time']);
  $new['comms'] = $mysql->GetField('COUNT(*)', 'news_comms', '`nid`='.$nid);
  $new['tags'] = $new['tags'] ? explode(', ', $new['tags']) : FALSE;
  $rating->SetKey('/news/'.$new['id']);
  $new['rating'] = $rating->Get();
  $new['rating']['avg'] = $new['rating'] ? round($new['rating']['avg']) : 'нет оценок';
  $new['rating']['rateable'] = $rating->IsRateable();
  $tmpl->Vars['NEW'] = $new;
  echo $tmpl->Parse('news/view.tmpl');
  exit;
};

// если на первой странице, то смотрим были ли новые пользователи
if(1>$n) {
  $tmpl->Vars['NEWUSERS'] = array();
  $mysql->Query('SELECT `id`,`login` FROM `users` WHERE `regdat`>'.($TIME-86400));
  while($arr = $mysql->FetchAssoc()) {
    $tmpl->Vars['NEWUSERS'][] = $arr;
  }
} else {
  $tmpl->Vars['NEWUSERS'] = FALSE;
}

$tag =& getvar('t');
if($tag) {
  $tag = $mysql->EscapeString($tag);
  $wheretag = 'WHERE `news`.`tags` LIKE "%'.$tag.'%"';
} else {
  $wheretag = NULL;
}

// список новостей
$que = $mysql->Query('SELECT SQL_CALC_FOUND_ROWS `news`.*,`users`.`login`
FROM `news` LEFT JOIN `users` ON `news`.`uid`=`users`.`id` '.$wheretag.'
ORDER BY `news`.`id` DESC LIMIT '.$n.','.$USER['np']);

$total = $mysql->GetFoundRows();

$tmpl->Vars['NEWS'] = array();
while($new = $mysql->FetchAssoc($que)) {
    $new['time'] = format_date($new['time']);
    $new['comms'] = $mysql->GetField('COUNT(*)', 'news_comms', '`nid`='.$new['id']);
    $new['tags'] = $new['tags'] ? explode(', ', $new['tags']) : FALSE;
    $new['text'] = strip_tags(mb_substr($new['text'], 0, 255, 'UTF-8'), '<br>').'...';
    $new['text'] = preg_replace('/&([a-z]){0,4}$/', NULL, $new['text']);
    $rating->SetKey('/news/'.$new['id']);
    $new['rating'] = round($rating->GetAverage());
    $tmpl->Vars['NEWS'][] = $new;
}

$mysql->Close();

$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = $total;
$tmpl->Vars['NAV']['limit'] = $USER['np'];
$tmpl->Vars['NAV']['add'] = 't='.$tag;

$online->Add('Hoвocти');

echo $tmpl->Parse('news/news.tmpl');

?>