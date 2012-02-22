<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
$r =& getvar('r');
$mysql = new mysql;
$mysql->Query('SELECT * FROM `forums` WHERE `rid`='.intval($r));
$tmpl->Vars['FORUMS'] = array();
while($arr = $mysql->FetchAssoc()){
  $tmpl->Vars['FORUMS'][] = $arr;
}
echo $tmpl->Parse('admin/forums.tmpl');
?>