<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');
if(1>$USER['state']) include($_SERVER['DOCUMENT_ROOT'].'/etc/bancheck.php');

if(!IsModInstalled('blogs'))
  raise_error('Данный модуль на данный момент не установлен.');

$bid =& getvar('b');
$bid = intval($bid);

$n =& getvar('n');
$n = intval($n);

$mysql = new mysql;
$blog = $mysql->GetRow('`blogs`.*,`users`.`login`', 'blogs` LEFT JOIN `users` ON `blogs`.`owner`=`users`.`id', '`blogs`.`owner`='.$bid);

if(!$blog['id']) raise_error('Heт тaкoгo блoгa.', 'index.php?'.SID);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['BLOG'] = $blog;

if(3==$USER['id'] && 1==$blog['perm'])
  raise_error('Извините, но доступ к данному блогу незарегистрированных пользователей был закрыт автором.', 'index.php?'.SID);

elseif(2==$blog['perm'] && $bid!=$USER['id'])
  raise_error('Извините, но доступ к чтению данного блога был закрыт автором.', 'index.php?'.SID);

if($tmpl->Vars['POSTVIEW'] = isset($_GET['p'])) {

  $tmpl->Vars['TITLE'] = 'Просмотр поста';
  $pid = intval($_GET['p']);
  $post = $mysql->GetRow('*', 'blogs_posts', '`id`='.$pid);
  if(!$post) raise_error('Heт тaкoгo пocтa.');
  // получаем id "соседних" постов
  $post['back'] = $mysql->GetField('`id`', 'blogs_posts', '`uid`='.$bid.' AND `id`<'.$pid.' ORDER BY `id` DESC');
  $post['next'] = $mysql->GetField('`id`', 'blogs_posts', '`uid`='.$bid.' AND `id`>'.$pid.' ORDER BY `id` ASC');

  if(1>$post['nocomm']) {
    $post['comms'] = $mysql->GetField('COUNT(*)', 'blogs_comms', '`pid`='.$post['id']);
  }
  $tmpl->Vars['POST'] = $post;

} else {

  $tmpl->Vars['TITLE'] = 'Просмотр блога "'.$blog['name'].'"';
  $tmpl->Vars['POSTS'] = array();

  $que = $mysql->Query('SELECT SQL_CALC_FOUND_ROWS * FROM `blogs_posts` WHERE `uid`='.$bid.' ORDER BY `id` DESC LIMIT '.$n.','.$USER['np']);
  $total = $mysql->GetFoundRows();

  while($arr = $mysql->FetchAssoc($que)) {

    $arr['time'] = format_date($arr['time']);

    if(1>$arr['nocomm']) {
      $arr['comms'] = $mysql->GetField('COUNT(*)', 'blogs_comms', '`pid`='.$arr['id']);
    };

    $tmpl->Vars['POSTS'][] = $arr;

  }

  $tmpl->UseNav();
  $tmpl->Vars['NAV']['pos'] = $n;
  $tmpl->Vars['NAV']['total'] = $total;
  $tmpl->Vars['NAV']['limit'] = $USER['np'];
  $tmpl->Vars['NAV']['add'] = 'b='.$blog['owner'];

}

$online->Add('Cмoтpит блoг');

echo $tmpl->Parse('blogs/view.tmpl');

?>