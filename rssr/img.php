<?php
// This file is a part of GIII (g3.steelwap.org)
if (!isset($_SERVER['QUERY_STRING']) || empty($_SERVER['QUERY_STRING'])) exit;
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

/* так не работает, нужно будет разобраться
$url = parse_url($_SERVER['QUERY_STRING']);
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
*/
$url = &$_SERVER['QUERY_STRING'];
if (substr($url, 0, 5) != 'http:') {
  header('HTTP/1.1 204 No Content', TRUE, 204);
  exit;
}
$cached = $_SERVER['DOCUMENT_ROOT'].'/var/cache/imgs/'.abs(crc32($url)).'.jpg';
if (!file_exists($cached)) {
  $data = file_get_contents($url);
  $img1 = imagecreatefromstring($data);
  $ox = imagesx($img1);
  $oy = imagesy($img1);
  $px = 92;
  $py = round($oy / round($ox/$px, 1));
  $img2 = imagecreatetruecolor($px, $py);
  imagecopyresampled($img2, $img1, 0, 0, 0, 0, $px, $py, $ox, $oy);
  imagejpeg($img2, $cached);
}
header('Content-Type: image/jpeg');
header('Content-Disposition: ; filename="'.basename($url).'"');
readfile($cached);
?>