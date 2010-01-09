<?php
// This file is a part of GIII (g3.steelwap.org)
include($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

if(2>$USER['state'])
  raise_error('Доступ запрещен.');


$n = intval($_GET['n']);

$file = file($_SERVER['DOCUMENT_ROOT'].'/var/links.dat');

if(!isset($file[$n]))
  raise_error('Нет такой ссылки.', 'indedx.php?'.SID);

$arr = explode('|:|', $file[$n]);
$url = $arr[1];

if(substr($url, 0, 7) == 'http://') $url = substr($url, 7);
if(!strpos($url, '/')) $url .= '/';
$slpos = strpos($url, '/');
$host = substr($url, 0, $slpos);
$uri = substr($url, $slpos);

if(ini_get('allow_url_fopen')) {

  $http = new httpquery($host, $uri);
  $http->sendHeaders['User-Agent'] = 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2b5) Gecko/20091222 Gentoo Firefox/3.6b5';
  $http->SendQuery();
  $http->GetResponse();
  $http->Close();
  $data = $http->responseData;
  $match = array();
  preg_match('/rel="SHORTCUT\ ICON"\ href="([\S]+)"/i', $data, $match);

  if(isset($match[1])) {
    $ext = substr($match[1], strrpos($match[1], '.')+1);
    $ext = strtolower($ext);
    if(substr($match[1], 0, 7) != 'http://') $match[1] = 'http://'.$host.$match[1];

    switch($ext) {

     case 'gif':
      copy($match[1], $_SERVER['DOCUMENT_ROOT'].'/links/favicons/'.$host.'.gif');
      $icon = 'favicons/'.$host.'.gif';
     break;

     case 'png':
      copy($match[1], $_SERVER['DOCUMENT_ROOT'].'/links/favicons/'.$host.'.gif');
      $img = imagecreatefrompng($_SERVER['DOCUMENT_ROOT'].'/links/favicons/'.$host.'.gif');
      imagegif($img, $_SERVER['DOCUMENT_ROOT'].'/links/favicons/'.$host.'.gif');
      $icon = 'favicons/'.$host.'.gif';
     break;

     case 'ico':
      include($_SERVER['DOCUMENT_ROOT'].'/links/class.ico.php');
      $ico = new Ico($match[1]);
      $ico->SetBackgroundTransparent();
      $img = $ico->GetIcon(0);
      imagegif($img, $_SERVER['DOCUMENT_ROOT'].'/links/favicons/'.$host.'.gif');
      $icon = 'favicons/'.$host.'.gif';
     break;

     default:
      $icon = NULL;
     break;

    }

  } else {

    $http = new httpquery($host, '/favicon.ico');
    $http->sendHeaders['User-Agent'] = 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2b5) Gecko/20091222 Gentoo Firefox/3.6b5';
    $http->SendQuery();
    $http->GetResponse();
    $http->Close();
    if(200 == $http->responseCode) {
      $f = fopen($_SERVER['DOCUMENT_ROOT'].'/links/favicons/'.$host.'.gif', 'wb');
      fwrite($f, $http->responseData);
      fclose($f);
      $ico = new Ico($match[1]);
      $ico->SetBackgroundTransparent();
      $img = $ico->GetIcon(0);
      imagegif($img, $_SERVER['DOCUMENT_ROOT'].'/links/favicons/'.$host.'.gif');
      $icon = 'favicons/'.$host.'.gif';
    } else {
      $icon = NULL;
    }

  }

} else {

  $icon = NULL;

}

$arr[2] = $icon."\n";
$file[$n] = implode('|:|', $arr);
$f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/links.dat', 'w');
flock($f, LOCK_EX);
fwrite($f, implode(NULL, $file));
flock($f, LOCK_UN);
fclose($f);
Header('Location: index.php?'.SID);

?> 