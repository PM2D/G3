<?php
// This file is a part of GIII (g3.steelwap.org)

if(isset($_POST['name'])){

  $name =& $_POST['name'];
  if(!trim($name)) raise_error('Oтcутcтвуeт имя комнаты.', 'admin.php?'.SID);
  $mysql = new mysql;
  $name = $mysql->EscapeString(stripslashes(htmlspecialchars($name)));
  $in['id'] = 0;
  $in['name'] = $name;
  $mysql->Insert('chatrooms', $in);
  $tmpl->Vars['MESSAGE'] = 'Комната создана.';
  $tmpl->Vars['FORWARD'] = '/chat/index.php?'.SID;
  echo $tmpl->Parse('forward.tmpl');

} else {

  echo $tmpl->Parse('admin/chatroom_new.tmpl');

}


?>