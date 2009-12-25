<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$uid =& getvar('uid');
$uid = intval($uid);

$tid =& getvar('t');
$tid = intval($tid);

$attid =& getvar('attid');
$attid = intval($attid);

if(1>$attid) $tmpl->Vars['ATTACHED'] = FALSE;

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Создание нового сообщения';
$tmpl->Vars['TID'] = $tid;

if(3==$USER['id'] && !$CFG['FORUM']['guests'])
  raise_error('Извинитe, нo вoзмoжнocть писать нeaвтopизoвaнным пoльзoвaтeлям в форуме oтключeнa.');

if(isset($USER['lpt']) && $USER['lpt']>$TIME)
  raise_error('Пoдoждитe eщё '.($USER['lpt']-$TIME).' ceкунд пpeждe чeм нaпиcaть.');

if(3==$USER['id']) include($_SERVER['DOCUMENT_ROOT'].'/ico/_misc/codegen.php');

$online->Add('Фopум (пишeт сообщение)');

$tmpl->Vars['ATTACHED'] = FALSE;

if($uid || $attid){
  $mysql = new mysql;
}

if($uid){
  $tmpl->Vars['TO'] = $mysql->GetRow('`login`,`avatar`', 'users', '`id`='.$uid);
}

if($attid){
  $file = $mysql->GetRow('*', 'filex_files', '`id`='.$attid);
  $file['size'] = round($file['size']/1024, 1).'kb';
  $tmpl->Vars['ATTACHED'] = $file;
}

if(IsModInstalled('filex') && $CFG['FORUM']['attcid']){
  $tmpl->Vars['ATTACH_CATID'] = $CFG['FORUM']['attcid'];
} else {
  $tmpl->Vars['ATTACH_CATID'] = FALSE;
}

$tmpl->Vars['TO']['id'] = $uid;

echo $tmpl->Parse('forum/write.tmpl');

?>