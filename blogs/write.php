<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/headers.php');
$compress->Enable();

$tmpl = new template;
$tmpl->Vars['TITLE'] = 'Создание нового поста';
$tmpl->Vars['BLOGID'] = intval(getvar('b'));

$online->Add('Блoги (пишeт)');

echo $tmpl->Parse('blogs/write.tmpl');

?>