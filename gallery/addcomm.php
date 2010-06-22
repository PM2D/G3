<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$fileid =& getvar('i');
$fileid = intval($fileid);

$mysql = new mysql;
$arr = $mysql->GetRow('`uid`', 'gallery_files', '`id`='.$fileid);
if(!$arr['uid']) raise_error('Heт тaкoгo файла.', 'index.php?'.SID);
$albumuid = $arr['uid'];

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Добавить комментарий.';

if(isset($_POST['comm'])){

  if(1>$USER['state']) include($_SERVER['DOCUMENT_ROOT'].'/etc/bancheck.php');

  $in['id'] = 0;
  $in['auid'] = $albumuid;
  $in['fid'] = $fileid;
  $in['uid'] = $USER['id'];
  $in['time'] = $TIME;
  $in['msg'] = stripslashes(htmlspecialchars($_POST['comm']));
  if(!trim($in['msg'])) raise_error('Вы не заполнили поле.', 'addcomm.php?i='.$fid.'&amp;'.SID);
  $smiles = new smiles;
  $smiles->ToImg($in['msg']);
  $tags = new tags;
  $tags->ToHtm($in['msg']);
  $in['msg'] = preg_replace('/(&[a-z]{2,4})</', '$1;<', $in['msg']);
  $in['msg'] = nl2br($in['msg']);
  $in['msg'] = $mysql->EscapeString($in['msg']);
  if($mysql->Insert('gallery_comms', $in)) {
    $tmpl->Vars['TITLE'] = 'Добавление комментария';
    $tmpl->Vars['MESSAGE'] = $USER['login'].', вaш кoммeнтapий дoбaвлeн';
    $tmpl->Vars['FORWARD'] = 'view.php?a='.$albumuid.'&amp;i='.$fileid.'&amp;'.SID;
    echo $tmpl->Parse('forward.tmpl');
  };
  exit;

};

$tmpl->Vars['FILEID'] = $fileid;
$tmpl->Vars['ALBUMUID'] = $albumuid;
echo $tmpl->Parse('gallery/addcomment.tmpl');

?>