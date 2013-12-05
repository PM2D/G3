<?php
// This file is a part of GIII (g3.steelwap.org)
$url = rawurldecode($_SERVER['QUERY_STRING']);
if(strpos($url, $_SERVER['HTTP_HOST'])){
  Header('Location: '.strip_tags($url));
  exit;
}
$url = htmlspecialchars($url);
Header('Content-Type: text/html; charset=utf-8');
Header('Cache-Control: no-cache, must-revalidate');
print('<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
<title>Переход по ссылке</title>
<style type="text/css">
a:link,a:visited { color: #0020EE; text-decoration: none }
a:active,a:hover { color: #C00000 }
body { text-align: center; color: #000000; background-color: #FFFFF0 }
</style>
</head><body><div>
Перейти на:<br />
<a href="'.$url.'">'.$url.'</a><hr />');
if(isset($_SERVER['HTTP_REFERER']))
  print('[<a href="'.htmlspecialchars($_SERVER['HTTP_REFERER']).'">Bepнутьcя</a>]');
print('</div></body></html>');
?>