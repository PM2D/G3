<?php
// This file is a part of GIII (g3.steelwap.org)
$n =& getvar('n');
$n = intval($n);
if(1>$n) raise_error('Koгo удaлять тo?');
if(isset($_GET['sure'])) {

  function deldir($dir) {
    $d = opendir($dir);
    while($str = readdir($d)) {
      if($str[0]!='.'){
        is_dir($dir.'/'.$str) ? deldir($dir.'/'.$str) : @unlink($dir.'/'.$str);
      }
    }
    @rmdir($dir);
    closedir($d);
  }

  $mysql = new mysql;
  // информация о пользователе для костылей
  $user = $mysql->GetRow('`id`,`login`,`pass`', 'users', '`id`='.$n);
  // удаление из таблицы пользователей, записок и отзывов
  $mysql->Query('DELETE LOW_PRIORITY FROM `users` WHERE `id`='.$n.' LIMIT 1');
  $mysql->Query('DELETE LOW_PRIORITY FROM `letters` WHERE `to`='.$n.' OR `uid`='.$n);
  $mysql->Query('DELETE LOW_PRIORITY FROM `references` WHERE `uid`='.$n.' OR `from`='.$n);
  $mysql->Query('OPTIMIZE TABLE `users`,`letters`,`references`');
  // посты на форуме теперь принадлежат гостю
  if(IsModInstalled('forum')) {
    $mysql->Query('UPDATE LOW_PRIORITY `posts` SET `uid`=3 WHERE `uid`='.$n);
  }
  // удаление комментариев пользователя из галереи
  if(IsModInstalled('gallery')) {
    $mysql->Query('DELETE LOW_PRIORITY FROM `gallery_comms` WHERE `uid`='.$n);
    $mysql->Query('OPTIMIZE TABLE `gallery_comms`');
  }
  // удаление комментариев пользователя из блогов
  if(IsModInstalled('blogs')) {
    $mysql->Query('DELETE LOW_PRIORITY FROM `blogs_comms` WHERE `uid`='.$n);
    $mysql->Query('OPTIMIZE TABLE `blogs_comms`');
  }
  // удаление комментариев пользователя из обменника
  if(IsModInstalled('filex')) {
    $mysql->Query('DELETE LOW_PRIORITY FROM `filex_comms` WHERE `uid`='.$n);
    $mysql->Query('OPTIMIZE TABLE `filex_comms`');
  }
  // удаление аватарок пользователя
  $allowed = explode(',', $CFG['AVATAR']['allowed']);
  for($i=0, $cnt=count($allowed); $i<$cnt; $i++) {
    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/av/'.$n.'.'.$allowed[$i])) {
      unlink($_SERVER['DOCUMENT_ROOT'].'/av/'.$n.'.'.$allowed[$i]);
    }
  }

  $tmpl->Vars['MESSAGE'] = 'Пользователь удалён.';
  $tmpl->Vars['BACK'] = FALSE;
  echo $tmpl->Parse('notice.tmpl');

  include($_SERVER['DOCUMENT_ROOT'].'/etc/include/delete.php');

} else {

  $tmpl->Vars['MESSAGE'] = 'Вы уверены что желаете удалить данного пользователя?<br />'.
				'Будет также удалена большая часть его постов.';
  $tmpl->Vars['YES'] = 'admin.php?mod=deluser&amp;n='.$n.'&amp;sure&amp;'.SID;
  $tmpl->Vars['NO'] = 'index.php?'.SID;
  echo $tmpl->Parse('confirm.tmpl');

}
?>