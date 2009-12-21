<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$n =& getvar('n');
$n = intval($n);

$tmpls = array();
$d = opendir($_SERVER['DOCUMENT_ROOT'].'/tmpl');
while($file = readdir($d)){
  if('.'!=$file[0] && 'index.php'!=$file && 'set.php'!=$file){
    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$file.'/theme.ini')){
      $theme = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$file.'/theme.ini');
      $theme['file'] = $file;
    } else {
      $theme = array('file'=>$file, 'name'=>strtr($file, '_', ' '), 'about'=>'N/A', 'author'=>'N/A');
    }
    $tmpls[] = $theme;
  };
}

$tmpl = new template;
$tmpl->Vars['TITLE'] = 'Выбор внешнего вида';
$tmpl->Vars['THEMES'] = array_slice($tmpls, $n, $USER['np']);
$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = sizeof($tmpls);
$tmpl->Vars['NAV']['limit'] = $USER['np'];
$tmpl->SendHeaders();
$compress->Enable();
echo $tmpl->Parse('tmpls.tmpl');

?>