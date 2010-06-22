<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

if (isset($_GET['edit']) && 1<$USER['state']) {

  $theme = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$_GET['edit'].'/theme.ini');
  if (isset($_POST['name'])) {
    $theme['name'] = $_POST['name'] ? stripslashes(htmlspecialchars($_POST['name'])) : $_GET['edit'];
    if (!empty($_POST['about'])) $theme['about'] = stripslashes(htmlspecialchars($_POST['about']));
    $theme['defstyle'] = stripslashes(htmlspecialchars($_POST['defstyle']));
    if (!empty($_POST['setnotice'])) $theme['setnotice'] = stripslashes(htmlspecialchars($_POST['setnotice']));
    fstools::save_ini($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$_GET['edit'].'/theme.ini', $theme);
    $tmpl->Vars['MESSAGE'] = 'Данные темы шаблонов "'.$theme['name'].'" обновлены.';
    $tmpl->Vars['BACK'] = '/tmpl/?'.SID;
    echo $tmpl->Parse('notice.tmpl');
  } else {
    if (!is_writable($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$_GET['edit'].'/theme.ini')) {
      raise_error('Редактирование невозможно т.к. нет прав для записи в /tmpl/'.$_GET['edit'].'/theme.ini<br />'.
		  'Редактируйте файл вручную либо установите права для возможности записи.',
		  '/tmpl/?'.SID);
    }
    if (!isset($theme['setnotice'])) $theme['setnotice'] = NULL;
    $tmpl->Vars['FILE'] = $_GET['edit'];
    $tmpl->Vars['THEME'] = $theme;
    $dir = dir($_SERVER['DOCUMENT_ROOT'].'/css/'.$_GET['edit'].'/');
    while ($str = $dir->Read()) {
      if ($str{0}!='.' && $str{0}!='_') $tmpl->Vars['STYLES'][] = $str;
    }
    $dir->Close();
    echo $tmpl->Parse('admin/tmplini_edit.tmpl');
  }

} else {

  $n =& getvar('n');
  $n = intval($n);

  $tmpls = array();
  $d = opendir($_SERVER['DOCUMENT_ROOT'].'/tmpl');
  while ($file = readdir($d)) {
    if ('.'!=$file[0] && 'index.php'!=$file && 'set.php'!=$file) {
      if (file_exists($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$file.'/theme.ini')) {
        $theme = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$file.'/theme.ini');
        $theme['file'] = $file;
      } else {
        $theme = array('file'=>$file, 'name'=>strtr($file, '_', ' '), 'about'=>'N/A', 'author'=>'N/A');
      }
      $tmpls[] = $theme;
    }
  }

  $tmpl->Vars['TITLE'] = 'Выбор внешнего вида';
  $tmpl->Vars['THEMES'] = array_slice($tmpls, $n, $USER['np']);
  $tmpl->UseNav();
  $tmpl->Vars['NAV']['pos'] = $n;
  $tmpl->Vars['NAV']['total'] = sizeof($tmpls);
  $tmpl->Vars['NAV']['limit'] = $USER['np'];
  echo $tmpl->Parse('tmpls.tmpl');

}

?>