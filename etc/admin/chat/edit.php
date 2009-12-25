<?php
// This file is a part of GIII (g3.steelwap.org)

$roomid =& getvar('r');
$roomid = intval($roomid);

$mysql = new mysql;
$room = $mysql->GetRow('*', 'chatrooms', '`id`='.$roomid);

if(empty($room))
  raise_error('Такой комнаты не существует.', '/chat/index.php?'.SID);

if(isset($_POST['nr'])){

  $nr =& $_POST['nr'];
  if(!trim($nr)) raise_error('Oтcутcтвуeт имя комнаты.', 'admin.php?mod=editroom&amp;r='.$roomid.'&amp;'.SID);
  $nr = $mysql->EscapeString(stripslashes(htmlspecialchars($nr)));
  
  if($mysql->Update('chatrooms', array('name'=>$nr), '`id`='.$roomid)){
    $tmpl->Vars['MESSAGE'] = 'Комната переименована.';
    $tmpl->Vars['FORWARD'] = '/chat/index.php?'.SID;
    echo $tmpl->Parse('forward.tmpl');
  } else raise_error($mysql->error);
  
} else {

  $tmpl->Vars['ROOM'] = $room;
  echo $tmpl->Parse('admin/chatroom_edit.tmpl');

}

?>