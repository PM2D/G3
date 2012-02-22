<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
$id =& getvar('n');
$id = intval($id);
if(!$id) raise_error('???');
$mysql = new mysql;
if(isset($_POST['fn'])){
  $name = $mysql->EscapeString(stripslashes(htmlspecialchars($_POST['fn'])));
  $about = $mysql->EscapeString(stripslashes(htmlspecialchars($_POST['about'])));
  if($mysql->Update('forums', array('name'=>$name, 'about'=>$about), '`id`='.$id)){
    $tmpl->Vars['MESSAGE'] = 'Имя и описание форума обновлено.';
    $tmpl->Vars['BACK'] = 'admin.php?mod=forum&amp;'.SID;
    echo $tmpl->Parse('notice.tmpl');
  } else raise_error($mysql->error);
} else {
  $forum = $mysql->GetRow('*', 'forums', '`id`='.$id);
  $tmpl->Vars['FID'] = $id;
  $tmpl->Vars['FNAME'] = $forum['name'];
  $tmpl->Vars['FABOUT'] = $forum['about'];
  echo $tmpl->Parse('admin/forum_rename.tmpl');
}
?>
