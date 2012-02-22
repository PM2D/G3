<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$id =& getvar('f');
$id = intval($id);

$mysql = new mysql;
$file = $mysql->GetRow('*', 'files', '`id`='.$id);

if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/ddir'.$file['path'])) raise_error('Heт тaкoгo фaйлa.');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = $file['name'];
$tmpl->Vars['FILE'] = '/ddir'.$file['path'];
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

$about = $_SERVER['DOCUMENT_ROOT'].'/ddir/'.dirname($file['path']).'/_'.basename(substr($file['path'], 0, -4)).'.txt';
$tmpl->Vars['ABOUT'] = file_exists($about) ? file_get_contents($about) : FALSE;

echo $tmpl->Parse('ddir/img.tmpl');

?>