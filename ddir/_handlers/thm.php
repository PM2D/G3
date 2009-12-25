<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$id =& getvar('f');
$id = intval($id);

$mysql = new mysql;
$file = $mysql->GetRow('*', 'files', '`id`='.$id);

if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/ddir'.$file['path'])) raise_error('Heт тaкoгo фaйлa.');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = $file['name'];
$tmpl->Vars['PATH'] = $file['path'];
$tmpl->Vars['URL'] = '/ddir'.$file['path'];
$tmpl->Vars['SIZE'] = round($file['size']/1024,1).'kb';
$tmpl->Vars['BACK'] = '/ddir/?d='.$file['did'].'&amp;'.SID;
// соседние файлы
$arr = $mysql->GetRow('*', 'files', '`did`='.$file['did'].' AND `id`<'.$id.' ORDER BY `id` DESC');
if($arr) require($_SERVER['DOCUMENT_ROOT'].'/etc/ddir/handlers.php');
$tmpl->Vars['PREV'] = $arr;
$arr = $mysql->GetRow('*', 'files', '`did`='.$file['did'].' AND `id`>'.$id.' ORDER BY `id` ASC');
if($arr) require($_SERVER['DOCUMENT_ROOT'].'/etc/ddir/handlers.php');
$tmpl->Vars['NEXT'] = $arr;
$mysql->Close();

echo $tmpl->Parse('ddir/thm.tmpl');

?>