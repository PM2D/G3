<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

if(!IsModInstalled('gallery'))
  raise_error('Данный модуль на данный момент не установлен.');

$n =& getvar('n');
$n = intval($n);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Фотогалерея';
$mysql = new mysql;
$tmpl->Vars['HAVEALBUM'] = $havealbum = $mysql->IsExists('gallery_albums', '`uid`='.$USER['id']);
if($havealbum){
  $que = $mysql->Query('SELECT COUNT(*) as `cnt`,`fid` FROM `gallery_comms`
WHERE `time`>'.$USER['last'].' AND `auid`='.$USER['id'].'
AND `uid`!='.$USER['id'].' GROUP BY `fid`');
  $tmpl->Vars['NEWCOMMS'] = array();
  while($arr = $mysql->FetchAssoc($que)){
    $title = $mysql->GetField('`title`', 'gallery_files', '`id`='.$arr['fid']);
    $tmpl->Vars['NEWCOMMS'][] = array('title'=>$title, 'fid'=>$arr['fid'], 'count'=>$arr['cnt']);
  }
};

$cur = floor($GLOBALS['TIME']/86400);
$mysql->Query('SELECT SQL_CALC_FOUND_ROWS `gallery_albums`.*,`users`.`login`
FROM `gallery_albums` LEFT JOIN `users` ON `gallery_albums`.`uid`=`users`.`id`
ORDER BY `gallery_albums`.`time` DESC LIMIT '.$n.','.$USER['np']);

$tmpl->Vars['ALBUMS'] = array();
while($arr = $mysql->FetchAssoc()){
  $day = floor($arr['time']/86400);
  if($cur==$day) $arr['time'] = 'ceгoдня в '.date('G:i', $arr['time']);
  elseif(($cur-1)==$day) $arr['time'] = 'вчepa в '.date('G:i', $arr['time']);
  elseif(($cur-2)==$day) $arr['time'] = 'пoзaвчepa в '.date('G:i', $arr['time']);
  else $arr['time'] = date('d.m.y в G:i', $arr['time']);
  $tmpl->Vars['ALBUMS'][] = $arr;
}

$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = $mysql->GetFoundRows();
$tmpl->Vars['NAV']['limit'] = $USER['np'];

$mysql->Close();

$online->Add('Галерея (cписок альбомов)');

echo $tmpl->Parse('gallery/index.tmpl');
?>