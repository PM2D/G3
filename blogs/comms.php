<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');
if(1>$USER['state']) include($_SERVER['DOCUMENT_ROOT'].'/etc/bancheck.php');

$pid =& getvar('p');
$pid = intval($pid);

$n =& getvar('n');
$n = intval($n);

$mysql = new mysql;
$arr = $mysql->GetRow('`uid`', 'blogs_posts', '`id`='.$pid);
if(!$arr['uid']) raise_error('Heт тaкoгo пocтa.');
$buid = $arr['uid'];

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Комментарии';

if(isset($_POST['comm'])){

  $in['id'] = 0;
  $in['pid'] = $pid;
  $in['buid'] = $buid;
  $in['uid'] = $USER['id'];
  $in['time'] = $TIME;
  $in['msg'] = stripslashes(htmlspecialchars($_POST['comm']));
  if(3==$USER['id'] && $_SESSION['code']!=postvar('code')){
    raise_error('Вы ввeли нeвepный кoд пoдтвepждeния', 'comms.php?p='.$pid.'&amp;'.SID);
  };
  if(!trim($in['msg'])){
    raise_error('Вы не заполнили поле.', 'comm.php?p='.$pid.'&amp;'.SID);
  };
  $smiles = new smiles;
  $smiles->ToImg($in['msg']);
  $tags = new tags;
  $tags->ToHtm($in['msg']);
  $in['msg'] = preg_replace('/(&[a-z]{2,4})</', '$1;<', $in['msg']);
  $in['msg'] = nl2br($in['msg']);
  $in['msg'] = $mysql->EscapeString($in['msg']);
  if($mysql->Insert('blogs_comms', $in)){
    $tmpl->Vars['TITLE'] = 'Добавление комментария';
    $tmpl->Vars['MESSAGE'] = $USER['login'].', вaш кoммeнтapий дoбaвлeн';
    $tmpl->Vars['BACK'] = 'comms.php?p='.$pid.'&amp;'.SID;
    echo $tmpl->Parse('notice.tmpl');
  };
  exit;

};

$mysql->Query('SELECT SQL_CALC_FOUND_ROWS `blogs_comms`.*,`users`.`login` FROM `blogs_comms` LEFT JOIN `users` ON `blogs_comms`.`uid`=`users`.`id`
WHERE `blogs_comms`.`pid`='.$pid.' ORDER BY `blogs_comms`.`time` LIMIT '.$n.','.($USER['np']+1));

$tmpl->Vars['COMMS'] = array();
while($arr = $mysql->FetchAssoc()){
  $tmpl->Vars['COMMS'][] = $arr;
}

$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = $mysql->GetFoundRows();
$tmpl->Vars['NAV']['limit'] = $USER['np']+1;
$tmpl->Vars['NAV']['add'] = 'p='.$pid;

$mysql->Close();

if(3==$USER['id']) include($_SERVER['DOCUMENT_ROOT'].'/ico/_misc/codegen.php');

$tmpl->Vars['POSTID'] = $pid;
$tmpl->Vars['BLOGUID'] = $buid;
echo $tmpl->Parse('blogs/comments.tmpl');

?>