<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
$n =& getvar('n');
$n = intval($n);
$mysql = new mysql;
$que = $mysql->Query('SELECT `id` FROM `forum_themes` WHERE `rid`='.$n.' OR `prid`='.$n);
while($a = $mysql->FetchRow($que)){
  $mysql->Delete('forum_posts', '`tid`='.$a[0]) or raise_error($mysql->error);
}
$mysql->Delete('forum_themes', '`rid`='.$n.' OR `prid`='.$n) or raise_error($mysql->error);
$mysql->Delete('forums', '`id`='.$n.' OR `rid`='.$n) or raise_error($mysql->error);
$mysql->Query('OPTIMIZE TABLE `forum_posts`,`forum_themes`,`forums`');

$tmpl = new template;
$tmpl->Vars['MESSAGE'] = 'Paздeл и вce вxoдившиe в нeгo тeмы удaлeны.';
$tmpl->Vars['BACK'] = 'admin.php?mod=forum&amp;'.SID;
echo $tmpl->Parse('notice.tmpl');

?>