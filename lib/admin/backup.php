<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
if(!extension_loaded('zip'))
  raise_error('Данная функция недоступна т.к. недоступно расширение zip.');

if(isset($_GET['del']))
  unlink($_SERVER['DOCUMENT_ROOT'].'/var/backup/'.$_GET['del'].'.zip');

$d = dir($_SERVER['DOCUMENT_ROOT'].'/var/backup');
$tmpl->Vars['BACKUPS'] = array();
while($str = $d->read()){
  if(substr($str, -3) == 'zip'){
    $size = round(filesize($_SERVER['DOCUMENT_ROOT'].'/var/backup/'.$str) / 1024, 1).'kb';
    $str = substr($str, 0, -4);
    $tmpl->Vars['BACKUPS'][] = array('name'=>$str, 'size'=>$size);
  }
}
$d->close();
sort($tmpl->Vars['BACKUPS']);
echo $tmpl->Parse('admin/backup.tmpl');
?>