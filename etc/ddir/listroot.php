<?php
// This file is a part of GIII (g3.steelwap.org)

$mysql = new mysql;
$GLOBALS['mysql'] = $mysql;

if(!ini_get('safe_mode')) @set_time_limit(60);

require($_SERVER['DOCUMENT_ROOT'].'/etc/ddir/require.php');

// Корень это папка
$dir = array(
	'id' => 2043925204,
	'did' => 0,
	'time' => filemtime($_SERVER['DOCUMENT_ROOT'].'/ddir'),
	'name' => 'Root',
	'path' => NULL,
	'count' => dir2int($_SERVER['DOCUMENT_ROOT'].'/ddir'),
	'listed' => 0
);

$mysql->Insert('dirs', $dir);

require($_SERVER['DOCUMENT_ROOT'].'/etc/ddir/listdir.php');
?>