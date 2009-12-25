<?php
// This file is a part of GIII (g3.steelwap.org)
$n =& getvar('n');
$n = intval($n);
if(!$n) raise_error('???');
if(isset($_POST['tn'])){
  $name = $mysql->EscapeString(stripslashes(htmlspecialchars($_POST['tn'])));
  if($mysql->Update('forum_themes', array('name'=>$name), '`id`='.$n.' LIMIT 1')){
    $tmpl->Vars['MESSAGE'] = 'Название темы измeнeнo.';
    $tmpl->Vars['BACK'] = 'index.php?'.SID;
    echo $tmpl->Parse('notice.tmpl');
  } else raise_error($mysql->error);
} else {
  $tmpl->Vars['TNAME'] = $mysql->GetField('`name`', 'forum_themes', '`id`='.$n);
  echo $tmpl->Parse('admin/theme_rename.tmpl');
}

?>
