<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
$n =& getvar('n');
$n = intval($n);
$to =& postvar('to');
$to = intval($to);
if(!$to || !$n) raise_error('???');
$theme = $mysql->GetRow('*', 'forum_themes', '`id`='.$n);
if(!$theme) raise_error('Heт тaкoй тeмы');
$rprid = $mysql->GetField('`rid`', 'forums', '`id`='.$to);
$prid = ($rprid) ? $rprid : $to;
$mysql->Update('forum_themes', array('rid'=>$to, 'prid'=>$prid), '`id`='.$n.' LIMIT 1') or exit;
$mysql->Update('forums', array('count'=>'`count`-1'), '`id`='.$theme['rid'].' LIMIT 1') or exit;
$mysql->Update('forums', array('count'=>'`count`+1'), '`id`='.$to.' LIMIT 1') or exit;
$tmpl->Vars['MESSAGE'] = 'Teмa пepeмeщeнa';
$tmpl->Vars['BACK'] = 'index.php?'.SID;
echo $tmpl->Parse('notice.tmpl');
?>