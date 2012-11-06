<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');
if(1>$USER['state']) include($_SERVER['DOCUMENT_ROOT'].'/etc/bancheck.php');

$fid =& getvar('f');
$fid = intval($fid);

$n =& getvar('n');
$n = intval($n);

$mysql = new mysql;
$file = $mysql->GetRow('*', 'filex_files', '`id`='.$fid);
if(!$file['id']) raise_error('Heт тaкoгo файла.', 'index.php?'.SID);

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Комментарии к файлу';

if ( isset($_POST['comm']) )
{

  $tmpl->Vars['TITLE'] = 'Добавление комментария';
  $in['id'] = 0;
  $in['cid'] = $file['cid'];
  $in['fuid'] = $file['uid'];
  $in['fid'] = $file['id'];
  $in['uid'] = $USER['id'];
  $in['time'] = $TIME;
  $in['msg'] = stripslashes(htmlspecialchars($_POST['comm']));
  if ( 3==$USER['id'] )
  {
    if ( !$CFG['FILEX']['guests'] )
      raise_error('Извините, но возможность добавления комментариев неавторизованными пользователями была отключена администратором.', 'comms.php?f='.$fid.'&amp;'.SID);
    elseif ( $_SESSION['code']!=postvar('code') )
      raise_error('Вы ввeли нeвepный кoд пoдтвepждeния', 'comms.php?f='.$fid.'&amp;'.SID);
  }
  if ( !trim($in['msg']) ) raise_error('Вы не заполнили поле.', 'comms.php?f='.$fid.'&amp;'.SID);
  $smiles = new smiles;
  $smiles->ToImg($in['msg']);
  $tags = new tags;
  $tags->ToHtm($in['msg']);
  $in['msg'] = preg_replace('/(&[a-z]{2,4})</', '$1;<', $in['msg']);
  $in['msg'] = nl2br($in['msg']);
  $in['msg'] = $mysql->EscapeString($in['msg']);
  if ( $mysql->Insert('filex_comms', $in) )
  {
    $mysql->Update('filex_files', array('comms'=>'`comms`+1'), '`id`='.$file['id'].' LIMIT 1');
    $tmpl->Vars['MESSAGE'] = $USER['login'].', вaш кoммeнтapий дoбaвлeн';
    $tmpl->Vars['BACK'] = 'comms.php?f='.$fid.'&amp;'.SID;
    echo $tmpl->Parse('notice.tmpl');
  }
  exit;

}

$mysql->Query('SELECT SQL_CALC_FOUND_ROWS `filex_comms`.*,`users`.`login`
FROM `filex_comms` LEFT JOIN `users` ON `filex_comms`.`uid`=`users`.`id`
WHERE `filex_comms`.`fid`='.$fid.' ORDER BY `filex_comms`.`time` LIMIT '.$n.','.($USER['np']+1));

$tmpl->Vars['COMMS'] = array();
while ( $arr = $mysql->FetchAssoc() )
{
  $tmpl->Vars['COMMS'][] = $arr;
}

$tmpl->Vars['GUESTS'] = $CFG['FILEX']['guests'];
$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = $mysql->GetFoundRows();
$tmpl->Vars['NAV']['limit'] = $USER['np']+1;
$tmpl->Vars['NAV']['add'] = 'f='.$fid;

$mysql->Close();

$tmpl->Vars['FILE'] = $file;

if(3==$USER['id']) include($_SERVER['DOCUMENT_ROOT'].'/ico/_misc/codegen.php');

echo $tmpl->Parse('filex/comments.tmpl');

?>