<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');
require($_SERVER['DOCUMENT_ROOT'].'/etc/ddir/require.php');

if (!IsModInstalled('ddir'))
  raise_error('Данный модуль на данный момент не установлен.');

$did =& getvar('d');
$did = intval($did);
if (1>$did) $did = 2043925204;

$n =& getvar('n');
$n = intval($n);

$mysql = new mysql;

$dir = $mysql->GetRow('*', 'dirs', '`id`='.$did);

if (!$dir) {
  header('Location: /ddir/?'.SID);
  exit;
}

// автоматическое обновление папки
if ($dir['time']<filemtime($_SERVER['DOCUMENT_ROOT'].'/ddir'.$dir['path'])) include($_SERVER['DOCUMENT_ROOT'].'/etc/ddir/upddir.php');
// обновление всех счетчиков
if (1<$USER['state'] && isset($_GET['rcnt'])) include($_SERVER['DOCUMENT_ROOT'].'/etc/ddir/recount.php');
// индексирование содержимого папки если, папка не проиндексирована
if (!$dir['listed']) require($_SERVER['DOCUMENT_ROOT'].'/etc/ddir/listdir.php');

$tmpl = new template;
$tmpl->SendHeaders();
header('Last-Modified: '.gmdate('D, d M Y H:i:s', $dir['time']).' GMT');
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Файлы - '.$dir['name'];
$tmpl->Vars['CDIR'] = $dir;

// страница выбора сортировки
if (isset($_GET['sort'])) {
  $tmpl->Vars['TITLE'] = 'Выбор сортировки';
  echo $tmpl->Parse('ddir/sort.tmpl');
  exit;
}
// сохранение сортировки в сессию
if (isset($_POST['sort'])) {
  $USER['sort'] = intval($_POST['sort']);
  $USER['rev'] = (bool)postvar('rev');
}

// для описаний
if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ddir'.$dir['path'].'/_about.txt')) {
  $tmpl->Vars['ABOUT'] = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/ddir'.$dir['path'].'/_about.txt');
} else {
  $tmpl->Vars['ABOUT'] = FALSE;
}

// "верхние" папки
$tmpl->Vars['UPDIRS'] = array();
// если не в корне, до добавляем корневую папку 
if (2043925204!=$did) $tmpl->Vars['UPDIRS'][0] = array('name'=>'Root', 'id'=>2043925204);
// разбиваем путь на папки. последнюю папку не считаем
$arr = explode('/', $dir['path']);
$cnt = count($arr)-1;
for ($i=1; $i<$cnt; $i++) {
  $tmpl->Vars['UPDIRS'][$i]['name'] = fname($arr[$i]);
  $tmpl->Vars['UPDIRS'][$i]['id'] = '';
  for ($j=1; $j<$i+1; $j++) {
    $tmpl->Vars['UPDIRS'][$i]['id'] .= '/'.$arr[$j];
  }
  $tmpl->Vars['UPDIRS'][$i]['id'] = abs(crc32($tmpl->Vars['UPDIRS'][$i]['id']));
}

$lim = $USER['np']+2;

if (!isset($USER['sort'])) $USER['sort'] = $CFG['DDIR']['sort'];
if (!isset($USER['rev'])) $USER['rev'] = $CFG['DDIR']['rev'];

switch ($USER['sort']) {
  case 0: $order = ' ORDER BY `id`'; break;
  case 1: $order = ' ORDER BY `name`'; break;
  case 2: $order = ' ORDER BY `size`'; break;
  case 3: $order = ' ORDER BY `time`'; break;
  case 4: $order = ' ORDER BY `type`'; break;
  default: $order = ' ORDER BY `id`'; break;
}

$rev = $USER['rev'] ? ' DESC' : 'ASC';

// получаем папки
$mysql->Query('SELECT SQL_CALC_FOUND_ROWS * FROM `dirs` WHERE `did`='.$dir['id'].' ORDER BY `name` '.$rev.' LIMIT '.$n.','.$lim);
$tmpl->Vars['DIRS'] = array();
while ($arr = $mysql->FetchAssoc()) {
  $tmpl->Vars['DIRS'][] = $arr;
}

$dirs = $mysql->GetFoundRows();

// получаем файлы
$mysql->Query('SELECT SQL_CALC_FOUND_ROWS * FROM `files` WHERE `did`='.$dir['id'].$order.' '.$rev.' LIMIT '.$n.','.$lim);
$tmpl->Vars['FILES'] = array();
while ($arr = $mysql->FetchAssoc()) {
  $arr['size'] = round($arr['size']/1024, 1).'kb';
  require($_SERVER['DOCUMENT_ROOT'].'/etc/ddir/handlers.php');
  $tmpl->Vars['FILES'][] = $arr;
}

$files = $mysql->GetFoundRows();

$tmpl->UseNav();
$tmpl->Vars['NAV']['pos'] = $n;
$tmpl->Vars['NAV']['total'] = max($dirs, $files);
$tmpl->Vars['NAV']['limit'] = $lim;
$tmpl->Vars['NAV']['add'] = 'd='.$dir['id'];

$online->Add($dir['name']);

echo $tmpl->Parse('ddir/index.tmpl');

?>