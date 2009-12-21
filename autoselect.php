<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

if(isset($_GET['force'])){

  $USER['tmpl'] = ('wap'==$_GET['force']) ? $CFG['AS']['wap'] : $CFG['AS']['web'];

} else {

  include($_SERVER['DOCUMENT_ROOT'].'/etc/autoselect.php');

}

if(file_exists($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/theme.ini')){
  $theme = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/theme.ini');
}

if(isset($theme['defstyle']) && file_exists($_SERVER['DOCUMENT_ROOT'].'/css/'.$USER['tmpl'].'/'.$theme['defstyle'])){
  $USER['style'] = $theme['defstyle'];
} elseif(file_exists($_SERVER['DOCUMENT_ROOT'].'/css/'.$USER['tmpl'].'/Default')) {
  $USER['style'] = 'Default';
} else {
  $d = dir($_SERVER['DOCUMENT_ROOT'].'/css/'.$USER['tmpl']);
  while($str = $d->read()){
    if('.'!=$str[0] && 'index.php'!=$str && 'set.php'!=$str){ $USER['style'] = $str; break; }
  }
  $d->close();
}

if(3!=$USER['id']){
  $mysql = new mysql;
  $tmpl = $mysql->EscapeString($USER['tmpl']);
  $style = $mysql->EscapeString($USER['style']);
  $mysql->Update('users', array('tmpl'=>$tmpl, 'style'=>$style), '`id`='.$USER['id'].' LIMIT 1');
  $mysql->Close();
};

$location = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/index.php?'.SID;
header('Location: '.$location);

?>