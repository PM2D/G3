<?php
// This file is a part of GIII (g3.steelwap.org)
$smiles = new smiles;

if(isset($_POST['url'])){
  if(trim($_POST['url']) && trim($_POST['code'])){
    $smiles->replace[$_POST['code']] = '<img src="'.$_POST['url'].'" alt="'.$_POST['alt'].'" />';
    $f = fopen($_SERVER['DOCUMENT_ROOT'].'/etc/smiles.cfg', 'wb');
    fwrite($f, serialize($smiles->replace));
    fclose($f);
    $tmpl->Vars['MESSAGE'] = 'Смайл добавлен.';
    $tmpl->Vars['BACK'] = FALSE;
  } else raise_error('Не заполнено поле', 'admin.php?mod=smiles&amp;'.SID);
};

if(isset($_GET['d'])){

  $tmpl->Vars['MESSAGE'] = 'Смайл '.$smiles->replace[$_GET['d']].' удален.';
  $tmpl->Vars['BACK'] = 'admin.php?mod=smiles&amp;'.SID;
  echo $tmpl->Parse('notice.tmpl');
  unset($smiles->replace[$_GET['d']]);
  $f = fopen($_SERVER['DOCUMENT_ROOT'].'/etc/smiles.cfg', 'wb');
  fwrite($f, serialize($smiles->replace));
  fclose($f);

} else {

  $tmpl->Vars['SMILES'] = array();
  foreach($smiles->replace as $k=>$v){
    $tmpl->Vars['SMILES'][] = array('code'=>$k, 'img'=>$v, 'delurl'=>urlencode($k));
  }

  echo $tmpl->Parse('admin/smiles.tmpl');

}

?>