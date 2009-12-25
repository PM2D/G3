<?php
// This file is a part of GIII (g3.steelwap.org)
if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/ddir'.$dir['path'])) exit;

$infiles = array();
$indirs = array();
$d = dir($_SERVER['DOCUMENT_ROOT'].'/ddir'.$dir['path']);
while($fpath = $d->Read()) {
  if($fpath[0]!='.' && $fpath[0]!='_' && $fpath!='index.php') {
    $fpath = stripslashes(htmlspecialchars($fpath));
    // получаем расширение (тип) файла и отделяем его от имени, а также проверяем файл ли это
    $pos = strrpos($fpath, '.');
    if($pos) {
      $type = substr($fpath, $pos+1);
      $name = fname(substr($fpath, 0, $pos));
      if(!in_array($type, explode(',', $CFG['DDIR']['ico']))) $type = 'file';
      $infiles[] = array(
	'id' => 0,
	'did' => $dir['id'],
	'size' => filesize($_SERVER['DOCUMENT_ROOT'].'/ddir'.$dir['path'].'/'.$fpath),
	'time' => filemtime($_SERVER['DOCUMENT_ROOT'].'/ddir'.$dir['path'].'/'.$fpath),
	'type' => $type,
	'name' => $mysql->EscapeString($name),
	'path' => $dir['path'].'/'.$mysql->EscapeString($fpath)
      );
    } else {
      $name = fname($fpath);
      $fpath = $dir['path'].'/'.$fpath;
      $indirs[] = array(
	'id' => abs(crc32($fpath)),
	'did' => $dir['id'],
	'time' => filemtime($_SERVER['DOCUMENT_ROOT'].'/ddir'.$fpath),
	'name' => $mysql->EscapeString($name),
	'path' => $mysql->EscapeString($fpath),
	'count' => dir2int($_SERVER['DOCUMENT_ROOT'].'/ddir'.$fpath),
	'listed' => 0
      );
    }
  }
}
$d->Close();

function MultiInsert($table, array $data){
  if(empty($data)) return;
  $query = 'INSERT INTO `'.$table.'` VALUES';
  $cnt = sizeof($data);
  for($i=0; $i<$cnt; $i++){
    $vals = '';
    foreach($data[$i] as $key=>$val){
      $vals .= ",'".$val."'";
    }
    $query .= ' ('.substr($vals, 1).'),';
  }
  GLOBAL $mysql;
  $mysql->query(substr($query, 0, -1));
}

MultiInsert('dirs', $indirs);
MultiInsert('files', $infiles);
$mysql->Update('dirs', array('time'=>filemtime($_SERVER['DOCUMENT_ROOT'].'/ddir'.$dir['path']), 'listed'=>1),'`id`='.$dir['id'].' LIMIT 1');

?>