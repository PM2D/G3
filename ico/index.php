<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$n =& getvar('n');
$n = intval($n);

$icons = array();
$d = opendir($_SERVER['DOCUMENT_ROOT'].'/ico');
while($file = readdir($d)){
  if('.'!=$file[0] && '_'!=$file[0] && 'index.php'!=$file && 'set.php'!=$file){
    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/ico/'.$file.'/theme.ini')){
      $theme = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/ico/'.$file.'/theme.ini');
      $theme['file'] = $file;
    } else {
      $theme = array('file'=>$file, 'name'=>strtr($file, '_', ' '), 'about'=>'N/A', 'author'=>'N/A');
    }
    $icons[] = $theme;
  };
}

$tmpl = new template;
$tmpl->Vars['TITLE'] = 'Выбор темы иконок';
$tmpl->Vars['THEMES'] = array_slice($icons, $n, $USER['np']+2);
$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = sizeof($icons);
$tmpl->Vars['NAV']['limit'] = $USER['np']+2;
$tmpl->SendHeaders();
$compress->Enable();
echo $tmpl->Parse('icons.tmpl');

?>