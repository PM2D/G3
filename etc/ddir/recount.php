<?php
// This file is a part of GIII (g3.steelwap.org)

function recursive($dpath=null){
  $cnt = 0;
  // совместимость с корнем т.к. root path = null
  if(!$dpath) $did = 2043925204;
  else $did = abs(crc32($dpath));
  $d = dir($_SERVER['DOCUMENT_ROOT'].'/ddir'.$dpath);
  while($fpath = $d->Read()){
    if($fpath[0]!='.' && $fpath[0]!='_' && $fpath!='index.php'){
      $pos = strrpos($fpath, '.');
      if(!$pos && is_dir($_SERVER['DOCUMENT_ROOT'].'/ddir'.$dpath.'/'.$fpath)){
        $fpath = stripslashes(htmlspecialchars($fpath));
        $upd['count'] = recursive($dpath.'/'.$fpath);
        $cnt += $upd['count'];
	$GLOBALS['mysql']->Update('dirs', $upd, '`id`='.abs(crc32($dpath.'/'.$fpath)));
      } else {
	$cnt++;
      }
    };
  }
  $d->Close();
  return $cnt;
}

$total = recursive();

$mysql->Update('dirs', array('count'=>$total), '`id`=2043925204 LIMIT 1');

?>