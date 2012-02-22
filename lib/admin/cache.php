<?php
// This file is a part of AugurCMS (g3.pm2d.ru)

if(isset($_GET['clear']))
  fstools::clear_dir($_SERVER['DOCUMENT_ROOT'].'/var/cache/'.$_GET['clear'].'/');

$d = opendir($_SERVER['DOCUMENT_ROOT'].'/var/cache');
$tmpl->Vars['DIRS'] = array();
while($str = readdir($d)){
  if($str[0]!='.'){
    switch($str){
     case 'tmpl': $title = 'Кэш шаблонизатора'; break;
     case 'rss': $title = 'Кэш rss-лент'; break;
     case 'imgs': $title = 'Кэш изображений'; break;
     case 'zip': $title = 'Кэш zip-архивов'; break;
     default: $title = $str; break;
    }
    $tmpl->Vars['DIRS'][] = array(
			'title' => $title,
			'name' => $str,
			'size' => round(fstools::get_dir_size($_SERVER['DOCUMENT_ROOT'].'/var/cache/'.$str.'/')/1024, 1).'kB'
				 );
  }
}

echo $tmpl->Parse('admin/cache.tmpl');

?>