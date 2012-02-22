<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

if(!IsModInstalled('forum'))
  raise_error('Данный модуль на данный момент не установлен.');

$n =& getvar('n');
$n = intval($n);

if(isset($_GET['fr'])) $USER['last'] = intval($_GET['fr']);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Темы с новыми сообщениями';

$mysql = new mysql;

if(isset($_GET['upd'])) {

  $USER['last'] = $TIME;
  $mysql->Update('users', array('last'=>$TIME), '`id`='.$USER['id'].' LIMIT 1');

} elseif(3!=$USER['id']) {

  $tmpl->Vars['LAST'] = date('d.m.y G:i', $USER['last']);

};


if(3!=$USER['id']){

  $string = 'SELECT SQL_CALC_FOUND_ROWS * FROM `forum_themes` '.
	    'WHERE `time`>'.$USER['last'].' AND `lastuid`!='.$USER['id'].' '.
	    'ORDER BY `time` DESC LIMIT '.$n.','.$USER['np'];

} else {

  $string = 'SELECT * FROM `forum_themes` ORDER BY `time` DESC LIMIT '.$USER['np'];

}

$que = $mysql->Query($string);

if(3!=$USER['id'])
  $total = $mysql->GetFoundRows();

$tmpl->Vars['THEMES'] = array();

while($arr = $mysql->FetchAssoc($que)){

  $arr['forumname'] = $mysql->GetField('`name`', 'forums', '`id`='.$arr['rid']);
  $arr['time'] = format_date($arr['time']);
  $tmpl->Vars['THEMES'][] = $arr;

}

if(3!=$USER['id']){
  $tmpl->UseNav();
  $tmpl->Vars['NAV']['pos'] = $n;
  $tmpl->Vars['NAV']['total'] = $total;
  $tmpl->Vars['NAV']['limit'] = $USER['np'];
};

$mysql->Close();

$online->Add('Фopум (пocлeдниe тeмы)');

echo $tmpl->Parse('forum/last.tmpl');

?>