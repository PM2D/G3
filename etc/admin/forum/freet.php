<?php
// This file is a part of GIII (g3.steelwap.org)
$n =& getvar('n');
$n = intval($n);
if($mysql->Update('forum_themes', array('closed'=>0, 'fixed'=>0), '`id`='.$n.' LIMIT 1')) {
  $tmpl->Vars['MESSAGE'] = 'Hopмaльный cтaтуc тeмы вoccтaнoвлeн';
  $tmpl->Vars['BACK'] = 'index.php?'.SID;
  echo $tmpl->Parse('notice.tmpl');
} else raise_error($mysql->error);
?>
