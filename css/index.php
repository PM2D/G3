<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$n =& getvar('n');
$n = intval($n);

$styles = array();
$d = opendir($_SERVER['DOCUMENT_ROOT'].'/css/'.$USER['tmpl']);
while($file = readdir($d)){
  if('.'!=$file[0] && 'index.php'!=$file && 'set.php'!=$file){
    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/css/'.$USER['tmpl'].'/'.$file.'/theme.ini')){
      $style = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/css/'.$USER['tmpl'].'/'.$file.'/theme.ini');
      $style['file'] = $file;
    } else {
      $style = array('file'=>$file, 'name'=>strtr($file, '_', ' '), 'about'=>'N/A', 'author'=>'N/A');
    }
    $styles[] = $style;
  };
}

$tmpl = new template;
$tmpl->Vars['TITLE'] = 'Выбор стиля';
$tmpl->Vars['THEMES'] = array_slice($styles, $n, $USER['np']+2);
$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = sizeof($styles);
$tmpl->Vars['NAV']['limit'] = $USER['np']+2;
$tmpl->SendHeaders();
$compress->Enable();
echo $tmpl->Parse('css.tmpl');

?>