<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$aid =& getvar('a');
$aid = intval($aid);

$n =& getvar('n');
$n = intval($n);

$mysql = new mysql;
$album = $mysql->GetRow('`gallery_albums`.*,`users`.`login`',
			'gallery_albums` LEFT JOIN `users` ON `gallery_albums`.`uid`=`users`.`id',
			'`gallery_albums`.`uid`='.$aid);

if(!$album['id']) raise_error('Heт тaкoгo альбома.', 'index.php?'.SID);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['ALBUM'] = $album;

if(3==$USER['id'] && 1==$album['perm']) {
  raise_error('Извините, но доступ к данному альбому незарегистрированных пользователей был закрыт автором.', 'index.php?'.SID);
} elseif(2==$album['perm'] && $aid!=$USER['id']) {
  raise_error('Извините, но доступ к просмотру данного альбома был закрыт автором.', 'index.php?'.SID);
}

$rating = new rating;

if($tmpl->Vars['IMGVIEW'] = isset($_GET['i'])) {

  if(1>$USER['state']) include($_SERVER['DOCUMENT_ROOT'].'/etc/bancheck.php');

  $tmpl->Vars['TITLE'] = 'Просмотр изображения';
  // получаем данные из бд
  $fid = intval($_GET['i']);
  $image = $mysql->GetRow('*', 'gallery_files', '`id`='.$fid);
  if(!$image) raise_error('Heт тaкoгo изображения.');
  // получаем id "соседних" изображений
  $image['back'] = $mysql->GetField('`id`', 'gallery_files', '`uid`='.$aid.' AND `id`<'.$fid.' ORDER BY `id` DESC');
  $image['next'] = $mysql->GetField('`id`', 'gallery_files', '`uid`='.$aid.' AND `id`>'.$fid.' ORDER BY `id` ASC');
  // формируем путь к файлу
  $image['filename'] = $image['uid'].'/'.$image['id'].'.'.$image['type'];
  // оценки
  $rating->SetKey('/gallery/'.$image['id']);
  $image['rating'] = $rating->Get();
  // округление средней оценки
  $image['rating']['avg'] = $image['rating'] ? round($image['rating']['avg']) : 'нет оценок';
  $image['rating']['rateable'] = $rating->IsRateable();
  // получаем комментарии
  $mysql->Query('SELECT SQL_CALC_FOUND_ROWS `gallery_comms`.*,`users`.`login`
FROM `gallery_comms` LEFT JOIN `users` ON `gallery_comms`.`uid`=`users`.`id`
WHERE `gallery_comms`.`fid`='.$image['id'].' LIMIT '.$n.','.$USER['np']);
  $tmpl->Vars['COMMS'] = array();
  while($comm = $mysql->FetchAssoc()) {
    $comm['time'] = format_date($comm['time']);
    $tmpl->Vars['COMMS'][] = $comm;
  }
  // получаем количество комментариев
  $image['comms'] = $mysql->GetFoundRows();
  // навигация
  $tmpl->UseNav();
  $tmpl->Vars['NAV']['pos'] = $n;
  $tmpl->Vars['NAV']['total'] = $image['comms'];
  $tmpl->Vars['NAV']['limit'] = $USER['np'];
  $tmpl->Vars['NAV']['add'] = 'a='.$album['uid'].'&amp;i='.$image['id'];
  // переводим размер в килобайты
  $image['filesize'] = round($image['filesize']/1024, 1).'kb';
  // если разрешение изображения еще не было определено, то определяем
  if(!$image['width']) {
    $ext = substr($image['filename'], -4);
    if($ext=='.gif') {
      $img = @imagecreatefromgif($_SERVER['DOCUMENT_ROOT'].'/gallery/files/'.$image['filename']);
    } elseif($ext=='.jpg' || $ext=='jpeg') {
      $img = @imagecreatefromjpeg($_SERVER['DOCUMENT_ROOT'].'/gallery/files/'.$image['filename']);
    } elseif($ext=='.png') {
      $img = @imagecreatefrompng($_SERVER['DOCUMENT_ROOT'].'/gallery/files/'.$image['filename']);
    }
    if($img) {
      $image['width'] = $upd['width'] = imagesx($img);
      $image['height'] = $upd['height'] = imagesy($img);
    }
  }
  // если не гость и не автор альбома, то инкрементируем количество просмотров
  if($aid != $USER['id'] && 3 != $USER['id'] && 1 > $n) {
    $image['views']++;
    $upd['views'] = '`views`+1';
    $mysql->Update('gallery_files', $upd, '`id`='.$image['id'].' LIMIT 1');
  }

  $tmpl->Vars['IMAGE'] = $image;

} else {

  $tmpl->Vars['TITLE'] = 'Просмотр альбома';
  $tmpl->Vars['FILES'] = array();

  $que = $mysql->Query('SELECT * FROM `gallery_files` WHERE `gallery_files`.`uid`='.$aid.' ORDER BY `gallery_files`.`id` DESC LIMIT '.$n.','.$USER['np']);

  while($arr = $mysql->FetchAssoc($que)) {

    $rating->SetKey('/gallery/'.$arr['id']);
    $arr['rating'] = round($rating->GetAverage());
    $arr['time'] = format_date($arr['time']);
    $arr['comms'] = $mysql->GetField('COUNT(*)', 'gallery_comms', '`fid`='.$arr['id']);
    $tmpl->Vars['FILES'][] = $arr;

  }

  $tmpl->UseNav();
  $tmpl->Vars['NAV']['pos'] = $n;
  $tmpl->Vars['NAV']['total'] = $mysql->GetField('COUNT(*)', 'gallery_files', '`uid`='.$aid);
  $tmpl->Vars['NAV']['limit'] = $USER['np'];
  $tmpl->Vars['NAV']['add'] = 'a='.$album['uid'];

}

$online->Add('Cмoтpит альбом');

echo $tmpl->Parse('gallery/view.tmpl');

?>