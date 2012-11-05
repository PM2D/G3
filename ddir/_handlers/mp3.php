<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$id =& getvar('f');
$id = intval($id);

$mysql = new mysql;
$file = $mysql->GetRow('*', 'files', '`id`='.$id);

if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/ddir'.$file['path'])) raise_error('Heт тaкoгo фaйлa.');

// spl не подгружает классы заканчивающиеся на цифру
// подгружаем этот класс вручную в качестве исключения
require($_SERVER['DOCUMENT_ROOT'].'/lib/mp3.php');

$mp3 = new mp3;
$data = $mp3->get_mp3($_SERVER['DOCUMENT_ROOT'].'/ddir'.$file['path'], FALSE);

if ( !isset($data['id3v1']) ) $data = $mp3->get_mp3($_SERVER['DOCUMENT_ROOT'].'/ddir'.$file['path'], TRUE);

if ( isset($_GET['start']) && isset($_GET['end']) )
{

  // сборщик "мусора" (более 10 минут == мусор)
  $d = opendir($_SERVER['DOCUMENT_ROOT'].'/tmp/mp3');
  while ( $str = readdir($d) )
  {
    if ( $str[0]!='.' && filemtime($_SERVER['DOCUMENT_ROOT'].'/tmp/mp3/'.$str)<($TIME-600) )
    {
     unlink($_SERVER['DOCUMENT_ROOT'].'/tmp/mp3/'.$str);
    }
  }

  $start = intval($_GET['start']);
  $end = intval($_GET['end']);
  if ( $start == $end ) raise_error('Начало это конец? О_о', 'mp3.php?f='.$id.'&amp;'.SID);
  if ( $start > $data['data']['length'] ) raise_error('Начало больше длинны? О_о', 'mp3.php?f='.$id.'&amp;'.SID);
  if ( 0 > $end ) $end = 1;
  if ( 0 > $start ) $start = 0;
  $end = min($end, $data['data']['length']);
  $tmpname = basename($file['path']);

  if ( $mp3->cut_mp3($_SERVER['DOCUMENT_ROOT'].'/ddir'.$file['path'], $_SERVER['DOCUMENT_ROOT'].'/tmp/mp3/'.$tmpname, $start, $end, 'second', FALSE) )
  {
    Header('Location: /tmp/mp3/'.$tmpname); exit;
  }
  else
  {
    raise_error('Не удалось обрезать mp3-файл.', 'mp3.php?f='.$id.'&amp;'.SID);
  }

}

$tmpl = new template;
$tmpl->Vars['TITLE'] = $file['name'];

if ( $data['id3v1']['title'] ) $tmpl->Vars['PROPS'][] = array('title'=>'Haзвaниe', 'value'=>$data['id3v1']['title']);
if ( $data['id3v1']['artist'] ) $tmpl->Vars['PROPS'][] = array('title'=>'Иcпoлнитeль', 'value'=>$data['id3v1']['artist']);
if ( $data['id3v1']['album'] ) $tmpl->Vars['PROPS'][] = array('title'=>'Aльбoм', 'value'=>$data['id3v1']['album']);
if ( trim($data['id3v1']['year']) ) $tmpl->Vars['PROPS'][] = array('title'=>'Гoд', 'value'=>$data['id3v1']['year']);
if ( $data['id3v1']['track'] ) $tmpl->Vars['PROPS'][] = array('title'=>'Тpeк', 'value'=>$data['id3v1']['track']);
if ( $data['id3v1']['genre'] ) $tmpl->Vars['PROPS'][] = array('title'=>'Жанр', 'value'=>$data['id3v1']['genre']);
if ( trim($data['id3v1']['comment']) ) $tmpl->Vars['PROPS'][] = array('title'=>'Koммeнтapий', 'value'=>$data['id3v1']['comment']);

$tmpl->Vars['PROPS'][] = array('title'=>'Битpeйт', 'value'=>$data['data']['bitrate'].'kbps');
$tmpl->Vars['PROPS'][] = array('title'=>'Чacтoтa', 'value'=>$data['data']['sampling_frequency'].'Hz');
$tmpl->Vars['PROPS'][] = array('title'=>'Koдиpoвaниe', 'value'=>$data['data']['type']);
$tmpl->Vars['PROPS'][] = array('title'=>'Peжим', 'value'=>$data['data']['mode']);
$tmpl->Vars['PROPS'][] = array('title'=>'Длительность', 'value'=>$data['data']['time']);
$tmpl->Vars['PROPS'][] = array('title'=>'Paзмep файла', 'value'=>round($data['data']['filesize']/1024, 1).'kb');

$tmpl->Vars['MAXSEC'] = round($data['data']['length']);

/* для описания удобнее использовать комментарии в mp3 тегах
$about = dirname($path).'/_'.basename($path, '.mp3').'.txt';
$tmpl->Vars['ABOUT'] = file_exists($about) ? file_get_contents($about)) : FALSE;
*/

$tmpl->Vars['FILEID'] = $id;
$tmpl->Vars['PATH'] = '/ddir'.$file['path'];
$tmpl->Vars['BACK'] = '/ddir/?d='.$file['did'].'&amp;'.SID;
// соседние файлы
$arr = $mysql->GetRow('*', 'files', '`did`='.$file['did'].' AND `id`<'.$id.' ORDER BY `id` DESC');
if($arr) require($_SERVER['DOCUMENT_ROOT'].'/etc/ddir/handlers.php');
$tmpl->Vars['PREV'] = $arr;
$arr = $mysql->GetRow('*', 'files', '`did`='.$file['did'].' AND `id`>'.$id.' ORDER BY `id` ASC');
if($arr) require($_SERVER['DOCUMENT_ROOT'].'/etc/ddir/handlers.php');
$tmpl->Vars['NEXT'] = $arr;
$mysql->Close();

$tmpl->SendHeaders();
$compress->Enable();
echo $tmpl->Parse('ddir/mp3.tmpl');

?>