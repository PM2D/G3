<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

if (2>$USER['state'])
  raise_error('Доступ запрещен.');

if (isset($_POST['name']) && isset($_POST['url'])) {

  $name = stripslashes(htmlspecialchars($_POST['name']));
  $url = stripslashes(htmlspecialchars($_POST['url']));

  if (substr($url, 0, 7) == 'http://') $url = substr($url, 7);
  if (!strpos($url, '/')) $url .= '/';
  $slpos = strpos($url, '/');
  $host = substr($url, 0, $slpos);
  $uri = substr($url, $slpos);

  if (ini_get('allow_url_fopen')) {

    include($_SERVER['DOCUMENT_ROOT'].'/links/class.ico.php');

    $http = new httpquery($host, $uri);
    $http->sendHeaders['User-Agent'] = 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2b5) Gecko/20091222 Gentoo Firefox/3.6b5';
    $http->SendQuery();
    $http->GetResponse();
    $http->Close();
    $data = $http->responseData;
    $match = array();
    preg_match('/"([^"]*?favicon[^"]+)"/i', $data, $match);

    if (isset($match[1])) {
      $ext = substr($match[1], strrpos($match[1], '.')+1);
      $ext = strtolower($ext);

      if (substr($match[1], 0, 7) != 'http://') $match[1] = 'http://'.$host.'/'.$match[1];

      switch($ext) {

       case 'gif':
        copy($match[1], $_SERVER['DOCUMENT_ROOT'].'/links/favicons/'.$host.'.gif');
        $icon = $host.'.gif';
       break;

       case 'png':
        copy($match[1], $_SERVER['DOCUMENT_ROOT'].'/links/favicons/'.$host.'.gif');
        $img = imagecreatefrompng($_SERVER['DOCUMENT_ROOT'].'/links/favicons/'.$host.'.gif');
        imagegif($img, $_SERVER['DOCUMENT_ROOT'].'/links/favicons/'.$host.'.gif');
        $icon = $host.'.gif';
       break;

       case 'ico':
        $ico = new Ico($match[1]);
        $ico->SetBackgroundTransparent();
        $img = $ico->GetIcon(0);
        if($img) {
          imagegif($img, $_SERVER['DOCUMENT_ROOT'].'/links/favicons/'.$host.'.gif');
          $icon = $host.'.gif';
        } else {
          $icon = NULL;
        }
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
      if (200 == $http->responseCode) {
        $f = fopen($_SERVER['DOCUMENT_ROOT'].'/links/favicons/'.$host.'.gif', 'wb');
        fwrite($f, $http->responseData);
        fclose($f);
        $ico = new Ico($match[1]);
        $ico->SetBackgroundTransparent();
        $img = $ico->GetIcon(0);
        if ($img) {
          imagegif($img, $_SERVER['DOCUMENT_ROOT'].'/links/favicons/'.$host.'.gif');
          $icon = $host.'.gif';
        } else {
          $icon = NULL;
        }
      } else {
        $icon = NULL;
      }

    }

  } else {

    $icon = NULL;

  }

  $arr = file($_SERVER['DOCUMENT_ROOT'].'/var/links.dat');
  $cnt = count($arr);
  $arr[$cnt+1] = $name.'|:|http://'.$url.'|:|'.$icon."\n";
  sort($arr, SORT_STRING);
  $c = count($arr);
  if($c<1) exit;
  $f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/links.dat', 'w');
  flock($f, LOCK_EX);
  fwrite($f, implode(NULL, $arr));
  flock($f, LOCK_UN);
  fclose($f);
  Header('Location: index.php?'.SID);
  exit;

}

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Добавить ссылку';
echo $tmpl->Parse('links/addlink.tmpl');

?>
