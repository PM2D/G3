<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/headers.php');
$compress->Enable();

$tmpl = new template;
$tmpl->Vars['TITLE'] = 'Создание нового поста';
$tmpl->Vars['BLOGID'] = intval(getvar('b'));

$online->Add('Блoги (пишeт)');

echo $tmpl->Parse('blogs/write.tmpl');

?>