<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$n =& getvar('n');
$n = intval($n);

$id =& getvar('i');
$id = intval($id) - 1;

$arr = file($_SERVER['DOCUMENT_ROOT'].'/var/rssr.dat');
if (!isset($arr[$id])) raise_error('No feed?');
$arr = explode('|', $arr[$id]);
$file = $_SERVER['DOCUMENT_ROOT'].'/var/cache/rss/'.abs(crc32($arr[0])).'.dat';

if (file_exists($file)) {
  $mtime = filemtime($file);
} else {
  $mtime = 0;
}

$ttl = $arr[2];

if ($mtime < $TIME-$ttl) {
  $url = parse_url('http://'.$arr[0]);
  if (!isset($url['query'])) $url['query'] = NULL;
  // создаем новое подключение
  $http = new httpquery($url['host'], $url['path'].'?'.$url['query']);
  // добавляем для прикола user-agent и отправляем GET-запрос
  $http->sendHeaders['User-Agent'] = 'G3-RSSReader/2.0.2';
  $http->SendQuery();
  // слушаем ответ
  $http->GetResponse();
  // закрываем подключение
  $http->Close();
  $data = $http->responseData;
  if (strpos($data, 'g="windows-1251"')) {
    $data = strtr($data, array('g="windows-1251"'=>'g="UTF-8"'));
    $data = iconv('Windows-1251', 'UTF-8', $data);
  }

  // Известно 2 способа записи html кода в текст новости без нарушений правил xml:
  // 1. через замену тегов на коды символов вроде &gt;
  // 2. запись текста новости внутри CDATA
  // Если нет CDATA, то пробуем добавить CDATA в description
  // и возвращаем нормальные теги, если html код был записан по 1 способу
  if(!strpos($data, 'CDATA'))
    $data = strtr($data, array(
		'<description>'=>'<description><![CDATA[',
		'</description>'=>']]></description>',
		'&lt;'=>'<',
		'&gt;'=>'>',
		'&amp;'=>'&',
		'&quot;'=>'"'
	));

  // target нет в спецификации wml и xhtml-mobile
  $data = strtr($data, array('target=_blank'=>NULL));
  // подправляем некоторые теги и ссылки
  $data = strtr($data, array(
		'<br>'=>'<br/>',
		'<li>'=>'<br/>&middot; ',
		'"http:'=>'"/go.php?http:',
		'\'http:'=>'\'/go.php?http:'
	));
  // для картинок отправляем на ресайзер
  $data = strtr($data, array('src="/go.php?'=>'src="img.php?'));
  // мега костыль для тех лент, которые не используют нормальные &amp;
  $data = preg_replace('/&([a-z]*([\s=]|$))/', '&amp;\\1', $data);
  // на всякий случай
  $data = trim($data);
  if(!$data) throw new Exception('No data in '.$url['host'].$url['path'].'?'.$url['query']);
  // парсим xml в массив
  // может и не самый лучший способ, но работает нормально (вроде :))
  $xml = new XMLReader;
  $xml->XML($data);
  $arr = array();
  $i = 0;
  while ($xml->read()) {
    if ($xml->name == 'item' && $xml->nodeType == XMLReader::ELEMENT) {
      while ($xml->read() && $xml->name != 'item') {
        if ($xml->name[0]!='#') $name = $xml->name;
        if ($xml->nodeType == XMLReader::TEXT || $xml->nodeType == XMLReader::CDATA) {
          $arr[$i][$name] = strip_tags($xml->value, '<a><img><br/>');
        }
      }
      $i++;
    }
  }
  $xml->close();
  // сохраняем все это в кеш (файл)
  $f = fopen($file, 'w');
  fwrite($f, serialize($arr));
  //chmod($file, 0666);
  fclose($f);
  unset($data);
}

// Debug
// $arr = unserialize(file_get_contents($file)); print_r($arr); exit;

$tmpl = new template;
// правильное кэширование
header('Expires: '.gmdate('D, d M Y H:i:s', $TIME + $ttl).' GMT');
if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
  if (strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5)) >= $mtime) {
    header('HTTP/1.1 304 Not Modified', 304, TRUE); exit;
  }
}
header('Last-Modified: '.gmdate('D, d M Y H:i:s', $mtime).' GMT', TRUE);
$tmpl->SendHeaders();
header('Cache-Control: public', TRUE);
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Просмотр новостной ленты';
$tmpl->Vars['LIST'] = array();

$arr = unserialize(file_get_contents($file));

// если это не просмотр одной отдельной новости то используем цикл
if (!isset($_GET['e'])) {
  $tmpl->Vars['VIEWONE'] = FALSE;
  $total = sizeof($arr);
  $arr = array_slice($arr, $n, $USER['np']);
  $cnt = sizeof($arr);
  for ($i=0; $i<$cnt; $i++) {
    // чтобы не было ошибок, если канал не содержит некоторых данных, используем пустые
    if (!isset($arr[$i]['description'])) $arr[$i]['description'] = NULL;
    if (!isset($arr[$i]['pubDate'])) $arr[$i]['pubDate'] = NULL;
    if (!isset($arr[$i]['link'])) $arr['link'] = NULL;
    // обрезаем новость если слишком длинная
    if (isset($arr[$i]['description'][520])) {
      $arr[$i]['description'] = mb_substr($arr[$i]['description'], 0, 255, 'UTF-8');
      $arr[$i]['description'] = strip_tags($arr[$i]['description'], '<br/><img>');
      $arr[$i]['description'] = preg_replace('/&([a-z]){0,6}$/', NULL, $arr[$i]['description']);
      $arr[$i]['description'] .= '...';
      $arr[$i]['more'] = 'view.php?i='.($id+1).'&amp;e='.($i+$n).'&amp;'.SID;
    } else {
      $arr[$i]['more'] = FALSE;
    }
  }
  $tmpl->Vars['LIST'] = $arr;
  $tmpl->UseNav();
  $tmpl->Vars['NAV']['pos'] = $n;
  $tmpl->Vars['NAV']['total'] = $total;
  $tmpl->Vars['NAV']['limit'] = $USER['np'];
  $tmpl->Vars['NAV']['add'] = 'i='.($id+1);
  $tmpl->Vars['NEXT'] = FALSE;
  $tmpl->Vars['PREV'] = FALSE;
// вывод только одной новости полностью
} else {
   $item = intval($_GET['e']);
   $tmpl->Vars['VIEWONE'] = TRUE;
   $tmpl->Vars['FEEDID'] = $id+1;
   $tmpl->Vars['BACK'] = $item;
   $tmpl->Vars['NEXT'] = isset($arr[$item+1]) ? ($item+1) : FALSE;
   $tmpl->Vars['PREV'] = isset($arr[$item-1]) ? ($item-1) : FALSE;
   $arr = $arr[$item];
   if (!isset($arr['link'])) $arr['link'] = NULL;
   if (!isset($arr[$item]['pubDate'])) $arr[$item]['pubDate'] = NULL;
   $arr['more'] = FALSE;
   $tmpl->Vars['LIST'][] = $arr;
}

$online->Add('Читaeт rss-лeнту');

echo $tmpl->Parse('rssr/view.tmpl');

?>