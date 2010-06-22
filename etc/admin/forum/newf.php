<?php
// This file is a part of GIII (g3.steelwap.org)
$mysql = new mysql;
if(isset($_POST['fn'])){
  $to =& postvar('to');
  $in['id'] = 0;
  $in['rid'] = intval($to);
  $in['name'] = $mysql->EscapeString(stripslashes(htmlspecialchars($_POST['fn'])));
  $in['about'] = $mysql->EscapeString(stripslashes(htmlspecialchars($_POST['about'])));
  $in['count'] = 0;
  if(!trim($in['name']))
    raise_error('He зaпoлнeнo пoлe названия.', 'admin.php?mod=newf&amp;'.SID);
  $mysql->Insert('forums', $in);
  $tmpl->Vars['MESSAGE'] = 'Форум coздaн.';
  $tmpl->Vars['BACK'] = 'admin.php?mod=forum&amp;'.SID;
  echo $tmpl->Parse('notice.tmpl');
} else {
  $mysql->Query('SELECT `id`,`name` FROM `forums` WHERE `rid`=0');
  $tmpl->Vars['FORUMS'] = array();
  while($arr = $mysql->FetchAssoc()){
    $tmpl->Vars['FORUMS'][] = $arr;
  }
  echo $tmpl->Parse('admin/forum_new.tmpl');
}
?>
