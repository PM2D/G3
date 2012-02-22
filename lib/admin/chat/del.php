<?php
// This file is a part of AugurCMS (g3.pm2d.ru)

$id =& getvar('d');
$id = intval($id);

$mysql = new mysql;

$room = $mysql->GetRow('*', 'chatrooms', '`id`='.$id);

if(!$room) raise_error('Такой комнаты не существует.','room.php?'.SID);

if(isset($_GET['sure']) && $mysql->Delete('chatrooms', '`id`='.$id.' LIMIT 1') && $mysql->Delete('chat', '`roomid`='.$id.'') ){

  $tmpl->Vars['MESSAGE'] = 'Комната удалена.';
  $tmpl->Vars['BACK'] = '/chat/index.php?'.SID;
  echo $tmpl->Parse('notice.tmpl');
  
} else {

  $tmpl->Vars['MESSAGE'] = 'Вы уверены что хотите удалить комнату?.';
  $tmpl->Vars['YES'] = 'del.php?d='.$id.'&amp;sure&amp;'.SID;
  $tmpl->Vars['NO'] = '/chat/index.php?'.SID;
  echo $tmpl->Parse('confirm.tmpl');

}

?>