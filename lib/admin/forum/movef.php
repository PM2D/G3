<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
$id =& getvar('n');
$id = intval($id);
if(!$id) raise_error('???');
$mysql = new mysql;
if(isset($_POST['to'])){
  $to = intval($_POST['to']);
  $mysql->Update('forums', array('rid'=>$to), '`id`='.$id.' LIMIT 1');
  if(0==$to) $to = $id;
  $mysql->Update('forum_themes', array('prid'=>$to), '`rid`='.$id);
  $tmpl->Vars['MESSAGE'] = 'Фopум пepeмeщён.';
  $tmpl->Vars['BACK'] = 'admin.php?mod=forum&amp;'.SID;
  echo $tmpl->Parse('notice.tmpl');
} else {
  $tmpl->Vars['FID'] = $id;
  $mysql->Query('SELECT `id`,`name` FROM `forums` WHERE `rid`=0 AND `id`!='.$id);
  $tmpl->Vars['FORUMS'] = array();
  while($arr = $mysql->FetchAssoc()){
    $tmpl->Vars['FORUMS'][] = $arr;
  }
  echo $tmpl->Parse('admin/forum_move.tmpl');
}
?>
