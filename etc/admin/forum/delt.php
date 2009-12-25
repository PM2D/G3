<?php
// This file is a part of GIII (g3.steelwap.org)
$n =& getvar('n');
$n = intval($n);
$rid = $mysql->GetField('`rid`', 'forum_themes', '`id`='.$n);
$mysql->Delete('forum_themes', '`id`='.$n.' LIMIT 1') or raise_error($mysql->error);
$mysql->Delete('forum_posts', '`tid`='.$n) or raise_error($mysql->error);
$mysql->Update('forums', array('count'=>'`count`-1'), '`id`='.$rid.' LIMIT 1') or raise_error($mysql->error);
$mysql->Query('OPTIMIZE TABLE `forum_themes`,`forum_posts`');
$tmpl->Vars['MESSAGE'] = 'Teмa удaлeна';
$tmpl->Vars['BACK'] = FALSE;
echo $tmpl->Parse('notice.tmpl');
?>
