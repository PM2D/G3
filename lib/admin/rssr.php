<?php
// This file is a part of AugurCMS (g3.pm2d.ru)

$arr = file($_SERVER['DOCUMENT_ROOT'].'/var/rssr.dat');

if(isset($_POST['add'])){

  $name = trim(stripslashes(htmlspecialchars(postvar('name'))));
  $url = trim(stripslashes(htmlspecialchars(postvar('url'))));
  $ttl = intval(postvar('ttl'));
  if(60>$ttl) $ttl = 60;
  if(substr($url, 0, 7)=='http://') $url = substr($url, 7);
  if(!$name || !$url) raise_error('He зaпoлнeнo пoлe.', 'admin.php?mod=rssr&amp;'.SID);
  $arr[] = $url.'|'.$name.'|'.$ttl."\n";
  $f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/rssr.dat', 'w');
  fwrite($f, implode(NULL, $arr));
  fclose($f);
  $tmpl->Vars['MESSAGE'] = 'Лента добавлена.';
  $tmpl->Vars['BACK'] = false;
  echo $tmpl->Parse('notice.tmpl');

} elseif(isset($_GET['del'])) {

  $id =& getvar('del');
  $id = intval($id);
  if(file_exists($_SERVER['DOCUMENT_ROOT'].'/var/cache/misc/rss'.abs(crc32($arr[$id][0])).'.xml'))
    unlink($_SERVER['DOCUMENT_ROOT'].'/var/cache/misc/rss'.abs(crc32($arr[$id][0])).'.xml');
  unset($arr[$id]);
  $f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/rssr.dat', 'w');
  fwrite($f, implode(NULL, $arr));
  fclose($f);
  $tmpl->Vars['MESSAGE'] = 'Лента удалена.';
  $tmpl->Vars['BACK'] = false;
  echo $tmpl->Parse('notice.tmpl');

} else {

  $tmpl->Vars['FEEDS'] = array();
  $cnt = count($arr);
  for($i=0; $i<$cnt; $i++){
    $a2 = explode('|', $arr[$i]);
    $tmpl->Vars['FEEDS'][] = array('id'=>$i, 'url'=>$a2[0], 'name'=>$a2[1], 'ttl'=>$a2[2]);
  }
  echo $tmpl->Parse('admin/rssr.tmpl');

}
?>