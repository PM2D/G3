<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$mod =& getvar('mod');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'FAQ';
$tmpl->Vars['MOD'] = $mod;

switch($mod){

 case 'trans':
  $tmpl->Vars['TITLE'] = 'Правила транслита';
  $obj = new translit;
  $tmpl->Vars['TRANS'] = $obj->replace;
 break;

 case 'smiles':
  $tmpl->Vars['TITLE'] = 'Список смайлов';
  $n =& getvar('n');
  $n = intval($n);
  $smiles = new smiles;
  $lim = $USER['np']+1;
  $tmpl->Vars['SMILES'] = array_slice($smiles->replace, $n, $lim);
  $tmpl->UseNav();
  $tmpl->Vars['NAV']['pos'] = $n;
  $tmpl->Vars['NAV']['total'] = count($smiles->replace);
  $tmpl->Vars['NAV']['limit'] = $lim;
  $tmpl->Vars['NAV']['add'] = 'mod='.$mod;
 break;

 default: break;

}

echo $tmpl->Parse('help.tmpl');

?>
