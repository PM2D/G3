<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$online->Add('нa глaвнoй');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Главная';

$mysql = new mysql;

if($tmpl->Vars['NEWS'] = IsModInstalled('news')) {
  // получаем последнюю новость
  $lnew = $mysql->GetRow('*', 'news', '1 ORDER BY `id` DESC');
  // если новостей нет, то заполняем пустотой
  if(!$lnew){
    $lnew['title'] = 'N/A';
    $lnew['time'] = 'N/A';
    $lnew['text'] = 'Пусто.';
  } else {
    $lnew['time'] = format_date($lnew['time']);
  }
  $tmpl->Vars['LASTNEW'] = $lnew;
}

if($tmpl->Vars['GBOOK'] = IsModInstalled('gbook')) {
  $tmpl->Vars['GBOOKCNT'] = $mysql->GetField('COUNT(*)', 'gbook', '1');
}

if($tmpl->Vars['FORUM'] = IsModInstalled('forum')) {
  $tmpl->Vars['THEMES'] = $mysql->GetField('COUNT(*)', 'forum_themes', '1');
  $tmpl->Vars['POSTS'] = $mysql->GetField('COUNT(*)', 'forum_posts', '1');
}

if($tmpl->Vars['CHAT'] = IsModInstalled('chat')) {
  $tmpl->Vars['INCHAT'] = $online->CountIn('/chat');
}

$tmpl->Vars['BLOGS'] = IsModInstalled('blogs');

$tmpl->Vars['GALLERY'] = IsModInstalled('gallery');

if($tmpl->Vars['FILEX'] = IsModINstalled('filex')) {
  $tmpl->Vars['FILEXCNT'] = $mysql->GetField('COUNT(*)', 'filex_files', '1');
  if(0<$CFG['FILEX']['view']) {
    $mysql->Query('SELECT * FROM `filex_cats` ORDER BY `time` DESC');
    $tmpl->Vars['CATS'] = array();
    while($arr = $mysql->FetchAssoc()) {
      $arr['time'] = format_date($arr['time']);
      $tmpl->Vars['CATS'][] = $arr;
    }
  } else {
    $tmpl->Vars['CATS'] = FALSE;
  }
}

if($tmpl->Vars['DDIR'] = IsModInstalled('ddir')) {
  // 2043925204 - abs(crc32()) от "/" (Root)
  // общее количество файлов
  $tmpl->Vars['FILESCNT'] = $mysql->GetField('`count`', 'dirs', '`id`=2043925204');
  // вариант показа 1 - новые файлы
  if(1 == $CFG['DDIR']['view']) {
    // 259200 - трое суток в секундах
    $que = $mysql->Query('SELECT * FROM `files` WHERE `time`>'.($TIME-259200).' ORDER BY `time` DESC LIMIT '.$USER['np']);
    $tmpl->Vars['LASTFILES'] = array();
    while($arr = $mysql->FetchAssoc($que)) {
      require($_SERVER['DOCUMENT_ROOT'].'/etc/ddir/handlers.php');
      $arr['size'] = round($arr['size']/1024, 1).'kb';
      $arr['time'] = format_date($arr['time']);
      $tmpl->Vars['LASTFILES'][] = $arr;
    }
    $tmpl->Vars['DIRS'] = FALSE;
  // вариант показа 2 - папки корня
  } elseif(2 == $CFG['DDIR']['view']) {
    $que = $mysql->Query('SELECT * FROM `dirs` WHERE `did`=2043925204');
    $tmpl->Vars['DIRS'] = array();
    while($arr = $mysql->FetchAssoc($que)) {
      $arr['time'] = format_date($arr['time']);
      $tmpl->Vars['DIRS'][] = $arr;
    }
    $tmpl->Vars['LASTFILES'] = FALSE;
  // вариант 0 - ничего
  } else {
    $tmpl->Vars['LASTFILES'] = $tmpl->Vars['DIRS'] = FALSE;
  }
}

$tmpl->Vars['RSSR'] = IsModInstalled('rssr');

$tmpl->Vars['VOTES'] = IsModInstalled('votes');

$tmpl->Vars['LINKS'] = IsModInstalled('links');

$tmpl->Vars['LETTERS'] = IsModInstalled('letters');

$tmpl->Vars['ASACTIVE'] = $CFG['AS']['active'];

$mysql->Close();

echo $tmpl->Parse('index.tmpl');

?>
