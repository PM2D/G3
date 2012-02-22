<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

if(3==$USER['id'] && !$CFG['CHAT']['guests'])
  raise_error('Извинитe, нo вoзмoжнocть пиcaть нeaвтopизoвaнным пoльзoвaтeлям в чaтe oтключeнa.');

$uid =& getvar('uid');
$uid = intval($uid);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Написать';

if($uid){
  $mysql = new mysql;
  $tmpl->Vars['TO'] = $mysql->GetRow('`login`,`avatar`', 'users', '`id`='.$uid);
  $tmpl->Vars['STATUS'] = $online->GetStatus($uid);
  $tmpl->Vars['SDESCR'] = $online->GetSDescr($uid);
  $mysql->Close();
};

$tmpl->Vars['TO']['id'] = $uid;

$tmpl->Vars['ROOMID'] = getvar('r');

echo $tmpl->Parse('chat/write.tmpl');

?>