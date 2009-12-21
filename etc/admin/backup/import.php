<?php
// This file is a part of GIII (g3.steelwap.org)
$mysql = new mysql;

$file = $_SERVER['DOCUMENT_ROOT'].'/var/backup/'.getvar('f').'.zip';

if(!file_exists($file))
  raise_error('Файл '.$file.' не существует.', 'admin.php?mod=backup&amp;'.SID);

$zip = new ZipArchive;
$zip->open($file);

for($i=0; $i<$zip->numFiles; $i++){
  $qs = explode(";\n", $zip->getFromIndex($i));
  $qs = array_slice($qs, 0, -1);
  $cnt = sizeof($qs);
  $name = substr($zip->getNameIndex($i), 0, -4);
  for($j=0; $j<$cnt; $j++){
    $mysql->Query($qs[$j]) or raise_error($name.'['.$j.'й зaпpoc]: '.$mysql->error);
  }
}
$zip->close();

$tmpl->Vars['MESSAGE'] = 'Coдepжимoe всех тaблиц импopтиpoвaнo<br />'.perf().'ceк';
$tmpl->Vars['BACK'] = 'admin.php?mod=backup&amp;'.SID;
echo $tmpl->Parse('notice.tmpl');
?>