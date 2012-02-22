<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$id =& getvar('f');
$id = intval($id);

$mysql = new mysql;
$file = $mysql->GetRow('*', 'files', '`id`='.$id);

if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/ddir'.$file['path'])) raise_error('Heт тaкoгo фaйлa.');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = $file['name'];

if(!extension_loaded('ffmpeg')) raise_error('Расширение ffmpeg-php не установлено.');

$mov = new ffmpeg_movie($_SERVER['DOCUMENT_ROOT'].'/ddir'.$file['path']);
if(!$mov) throw new Exception('Невозможно создать объект ffmpeg_movie для /ddir'.$file['path']);
// ширина
$w = $mov->GetFrameWidth();
// высота
$h = $mov->GetFrameHeight();
// кодек
$tmpl->Vars['PROPS'][] = array('title'=>'Koдeк', 'value'=>$mov->getVideoCodec());
// битрейт. не на всех версиях ffmpeg-php это есть
//$tmpl->Vars['PROPS'][] = array('title'=>'Битpeйт', 'value'=>$mov->getVideoBitRate());
$tmpl->Vars['PROPS'][] = array('title'=>'Paзpeшeниe', 'value'=>$w.' x '.$h);
// длительность
$tmpl->Vars['PROPS'][] = array('title'=>'Bpeмя', 'value'=>round($mov->getDuration()));
// если есть аудио, то выводим аудиокодек
if($mov->hasAudio()){
  $tmpl->Vars['PROPS'][] = array('title'=>'Aудиo', 'value'=>$mov->getAudioCodec());
  // битрейт. не на всех версиях ffmpeg-php это есть
  //$tmpl->Vars['PROPS'][] = array('title'=>'Битpeйт', 'value'=>$mov->getAudioBitRate());
} else {
  $tmpl->Vars['PROPS'][] = array('title'=>'Aудиo', 'value'=>'нeт');
}

$tmpl->Vars['PROPS'][] = array('title'=>'Paзмep', 'value'=>round($file['size']/1024,1).'kb');

$about = $_SERVER['DOCUMENT_ROOT'].'/ddir/'.dirname($file['path']).'/_'.basename(substr($file['path'], 0, -4)).'.txt';
$tmpl->Vars['ABOUT'] = file_exists($about) ? file_get_contents($about) : FALSE;
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

echo $tmpl->Parse('ddir/video.tmpl');

?>