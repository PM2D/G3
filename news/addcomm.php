<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');
if(1>$USER['state']) include($_SERVER['DOCUMENT_ROOT'].'/etc/bancheck.php');

include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/headers.php');
$compress->Enable();

if(3==$USER['id'] && !$CFG['NEWS']['guests']) exit;

$nid =& postvar('nid');
$nid = intval($nid);
if(!$nid) raise_error('???');
$text =& postvar('comm');

$in['id'] = 0;
$in['nid'] = $nid;
$in['uid'] = $USER['id'];
$in['time'] = $TIME;
$in['msg'] = stripslashes(htmlspecialchars($text));

if(!trim($in['msg'])) raise_error('Не заполнено поле.', 'comments.php?nin='.$id.'&amp;'.SID);

if(3==$USER['id'] && $_SESSION['code']!=postvar('code')){
  unset($_SESSION['code']);
  raise_error('Вы ввeли нeвepный кoд пoдтвepждeния', 'say.php?'.SID);
};

if(isset($USER['lpt']) && $USER['lpt']>$TIME)
  raise_error('Пoдoждитe eщё '.($USER['lpt']-$TIME).' ceкунд пpeждe чeм нaпиcaть.');


$smiles= new smiles;
$smiles->ToImg($in['msg']);
$tags = new tags;
$tags->ToHtm($in['msg']);

$mysql = new mysql;
$in['msg'] = $mysql->EscapeString($in['msg']);
$mysql->Insert('news_comms', $in);
$mysql->Update('users', array('posts'=>'`posts`+1'), '`id`='.$USER['id'].' LIMIT 1');
$mysql->Close();

$USER['lpt'] = $TIME + 25;

$tmpl = new template;
$tmpl->Vars['TITLE'] = 'Добавление комментария';
$tmpl->Vars['FORWARD'] = 'comments.php?nid='.$nid.'&amp;'.SID;
$tmpl->Vars['MESSAGE'] = $USER['login'].', Ваш комментарий добавлен';
echo $tmpl->Parse('forward.tmpl');

?>