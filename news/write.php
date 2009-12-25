<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/headers.php');
$compress->Enable();

$tmpl = new template;
$tmpl->Vars['TITLE'] = 'Создание новости';
echo $tmpl->Parse('news/write.tmpl');

?>