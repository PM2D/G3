<?php
// This file is a part of GIII (g3.steelwap.org)

if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/ddir'.$dir['path'])) exit;

function recursive($dpath){
  $did = abs(crc32($dpath));
  // совместимость с корнем т.к. root path = null
  if(!$dpath) $did = 2043925204;
  $d = dir($_SERVER['DOCUMENT_ROOT'].'/ddir'.$dpath);
  while($fpath = $d->Read()){
    if($fpath[0]!='.' && $fpath[0]!='_' && $fpath!='index.php'){
      $pos = strrpos($fpath, '.');
      if(!$pos && is_dir($_SERVER['DOCUMENT_ROOT'].'/ddir'.$dpath.'/'.$fpath)){
        $fpath = stripslashes(htmlspecialchars($fpath));
        recursive($dpath.'/'.$fpath);
	$GLOBALS['mysql']->Delete('dirs', '`id`='.abs(crc32($dpath.'/'.$fpath)));
      };
    };
  }
  $d->Close();
  $GLOBALS['mysql']->Delete('files', '`did`='.$did);
}

recursive($dir['path']);

$dir['listed'] = 0;

?>