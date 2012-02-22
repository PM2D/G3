<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
$arr = get_loaded_extensions();
$cnt = count($arr);
$tmpl->Vars['EXTENSIONS'] = '';
for($i=0;$i<$cnt;$i++){
  $tmpl->Vars['EXTENSIONS'] .= ' '.$arr[$i].',';
}
$tmpl->Vars['EXTENSIONS'] = substr($tmpl->Vars['EXTENSIONS'], 0, -1);

function inival($var){
  static $arr;
  if(!$arr) $arr = ini_get_all();
  if(!$arr[$var]['local_value']) return 'Heт';
  elseif(2>$arr[$var]['local_value']) return 'Дa';
  else return $arr[$var]['local_value'];
}

echo $tmpl->Parse('admin/phpinfo.tmpl');
?>