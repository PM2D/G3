<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');


$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

$mysql = new mysql;

if(isset($_GET['del']) && 0<$USER['state']){

  if($mysql->Delete('references', '`id`='.intval($_GET['del']).' LIMIT 1')){
    $mysql->Query('OPTIMIZE TABLE `references`');
    $tmpl = new template;
    $tmpl->Vars['MESSAGE'] = 'Отзыв удален.';
    $tmpl->Vars['BACK'] = FALSE;
    echo $tmpl->Parse('notice.tmpl');
    exit;
  };

};

$uid =& getvar('uid');
$uid = intval($uid);

$n =& getvar('n');
$n = intval($n);

if(!$uid) raise_error('???');

$tmpl->Vars['TITLE'] = 'Отзывы о пользователе';

if(isset($_POST['reason']) && 3!=$USER['id'] && $uid!=$USER['id']){

  if(1>$USER['state']) include($_SERVER['DOCUMENT_ROOT'].'/etc/bancheck.php');


  if(isset($USER['lrt']) && $USER['lrt']>$TIME)
    raise_error('Вы уже оставляли отзыв. '.
		'Следующий Вы сможете оставить только через '.($USER['lrt']-$TIME)).' ceк';

  if(!trim($_POST['reason']))
    raise_error('Не заполнено поле.', 'references.php?uid='.$uid.'&amp;'.SID);

  $in['id'] = 0;
  $in['uid'] = $uid;
  $in['who'] = $USER['id'];
  $in['reason'] = stripslashes(htmlspecialchars($_POST['reason']));
  $smiles = new smiles;
  $smiles->ToImg($in['reason']);
  $tags = new tags;
  $tags->ToHtm($in['reason']);
  $in['reason'] = preg_replace('/(&[a-z]{2,4})</', '$1;<', $in['reason']);
  $in['reason'] = stripslashes($in['reason']);
  $in['reason'] = nl2br($in['reason']);
  $in['reason'] = $mysql->EscapeString($in['reason']);

  if($mysql->Insert('references', $in)){
    $USER['lrt'] = $TIME + 600;
    $tmpl->Vars['TITLE'] = 'Добавление отзыва';
    $tmpl->Vars['MESSAGE'] = 'Ваш отзыв добавлен.';
    $tmpl->Vars['BACK'] = false;
    echo $tmpl->Parse('notice.tmpl');
  };

  exit;

};

$tmpl->Vars['LOGIN'] = $mysql->GetField('`login`','users','`id`='.$uid);

$mysql->Query('SELECT SQL_CALC_FOUND_ROWS `references`.*,`users`.`login` '.
	'FROM `references` LEFT JOIN `users` ON `references`.`who`=`users`.`id` '.
	'WHERE `references`.`uid`='.$uid);

$tmpl->Vars['REFERENCES'] = array();
while($arr = $mysql->FetchAssoc()){
  $tmpl->Vars['REFERENCES'][] = $arr;
}

$tmpl->Vars['UID'] = $uid;
$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = $mysql->GetFoundRows();
$tmpl->Vars['NAV']['limit'] = $USER['np'];
$tmpl->Vars['NAV']['add'] = 'uid='.$uid;

echo $tmpl->Parse('references.tmpl');

?>