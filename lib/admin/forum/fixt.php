<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
$n =& getvar('n');
$n = intval($n);
if($mysql->Update('forum_themes', array('fixed'=>1), '`id`='.$n.' LIMIT 1')) {
  $tmpl->Vars['MESSAGE'] = 'Teмa зафиксирована';
  $tmpl->Vars['BACK'] = 'index.php?'.SID;
  echo $tmpl->Parse('notice.tmpl');
} else raise_error($mysql->error);
?>
