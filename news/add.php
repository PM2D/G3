<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/headers.php');
$compress->Enable();

if(2>$USER['state']) raise_error('Доступ запрещён.');

$mysql = new mysql;

$in['id'] = 0;
$in['uid'] = $USER['id'];
$in['time'] = $TIME;
$in['title'] = stripslashes(htmlspecialchars(postvar('title')));
$in['text'] = stripslashes(htmlspecialchars(postvar('text')));
$in['tags'] = stripslashes(htmlspecialchars(postvar('tags')));
if(!$in['title'] || !$in['text']) raise_error('He зaпoлнeнo пoлe.', 'write.php?'.SID);
$in['text'] = nl2br($in['text']);
$in['text'] = strtr($in['text'], array('[line]'=>'<hr />'));

$smiles = new smiles;
$smiles->ToImg($in['text']);

$tags = new tags;
$tags->ToHtm($in['text']);

$in['text'] = preg_replace('/(&[a-z]{2,4})</', '$1;<', $in['text']);
$in['title'] = $mysql->EscapeString($in['title']);
$in['text'] = $mysql->EscapeString($in['text']);
$in['tags'] = $mysql->EscapeString($in['tags']);
$mysql->Insert('news', $in);

$tmpl = new template;
$tmpl->Vars['TITLE'] = 'Добавление новости';
$tmpl->Vars['FORWARD'] = 'index.php?'.SID;
$tmpl->Vars['MESSAGE'] = 'Новость добавлена';
echo $tmpl->Parse('forward.tmpl');

?>