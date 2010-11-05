<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$cid =& getvar('c');
$cid = intval($cid);

$n =& getvar('n');
$n = intval($n);

$mysql = new mysql;
$category = $mysql->GetRow('*', 'filex_cats', '`id`='.$cid);

if (!$category['id']) raise_error('Heт тaкой категории.', 'index.php?'.SID);

if ($category['passw']) {
  if (isset($_COOKIE['fc'.$category['id'].'p']) && $_COOKIE['fc'.$category['id'].'p']) {
    if ($_COOKIE['fc'.$category['id'].'p']!=$category['passw']) {
      setcookie('fc'.$category['id'].'p', NULL, $TIME-3600, '/filex/view.php', $_SERVER['HTTP_HOST']);
    }
  } elseif (isset($_POST['passw'])) {
    if ($_POST['passw']!=$category['passw']) {
      raise_error('Неверный пароль. Доступ запрещен.', 'index.php?'.SID);
    } else {
      setcookie('fc'.$category['id'].'p', $_POST['passw'], $TIME+2592000, '/filex/view.php', $_SERVER['HTTP_HOST']);
    }
  } else {
    $tmpl = new template;
    $tmpl->SendHeaders();
    $compress->Enable();
    $tmpl->Vars['TITLE'] = 'Обменник - '.$category['title'].' - пароль';
    $tmpl->Vars['CID'] = $category['id'];
    echo $tmpl->Parse('filex/passw.tmpl');
    exit;
  }
}

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Обменник - '.$category['title'];
$tmpl->Vars['CATEGORY'] = $category;

$rating = new rating;

$tmpl->Vars['FILEVIEW'] = isset($_GET['f']);

if ($tmpl->Vars['FILEVIEW']) {

  $fid = intval($_GET['f']);
  $file = $mysql->GetRow('`filex_files`.*,`users`.`login`', 'filex_files` LEFT JOIN `users` ON `filex_files`.`uid`=`users`.`id', '`filex_files`.`id`='.$fid);
  if (!$file) raise_error('Heт тaкoгo файла.');
  // получаем id "соседних" файлов
  $file['back'] = $mysql->GetField('`id`', 'filex_files', '`cid`='.$cid.' AND `id`<'.$fid.' ORDER BY `id` DESC');
  $file['next'] = $mysql->GetField('`id`', 'filex_files', '`cid`='.$cid.' AND `id`>'.$fid.' ORDER BY `id` ASC');
  // формируем путь к файлу и его размер
  $file['filename'] = $file['cid'].'/'.$file['id'].'.'.$file['type'];
  $file['size'] = round($file['size']/1024, 1).'kb';
  // оценки
  $rating->SetKey('/filex/'.$file['id']);
  $file['rating'] = $rating->Get();
  // округление средней оценки
  $file['rating']['avg'] = $file['rating'] ? round($file['rating']['avg']) : 'нет оценок';
  $file['rating']['rateable'] = $rating->IsRateable();
  // предпросмотр по умолчанию false (используется для изображений)
  $tmpl->Vars['PREV'] = FALSE;

  switch ($file['type']) {

   case 'gif':
   case 'jpg':
   case 'jpeg':
   case 'png':
    if (!$file['width']) {
     if ($file['type']=='gif') {
      $img = imagecreatefromgif($_SERVER['DOCUMENT_ROOT'].'/filex/files/'.$file['filename']);
     } elseif ($file['type']=='jpg' || $file['type']=='jpeg') {
      $img = imagecreatefromjpeg($_SERVER['DOCUMENT_ROOT'].'/filex/files/'.$file['filename']);
     } elseif ($file['type']=='png') {
      $img = imagecreatefrompng($_SERVER['DOCUMENT_ROOT'].'/filex/files/'.$file['filename']);
     };
     $file['width'] = $upd['width'] = imagesx($img);
     $file['height'] = $upd['height'] = imagesy($img);
     $mysql->Update('filex_files', $upd, '`id`='.$file['id']);
    };
    $tmpl->Vars['PROPS'][] = array('title'=>'Разрешение', 'value'=>$file['width'].'x'.$file['height']);
    $tmpl->Vars['PREV'] = TRUE;
   break;

   case 'mp3':
    $mp3 = new mp3;
    $data = $mp3->get_mp3($_SERVER['DOCUMENT_ROOT'].'/filex/files/'.$file['filename'], TRUE);
    if($data['id3v1']['title']) $tmpl->Vars['PROPS'][] = array('title'=>'Haзвaниe', 'value'=>$data['id3v1']['title']);
    if($data['id3v1']['artist']) $tmpl->Vars['PROPS'][] = array('title'=>'Иcпoлнитeль', 'value'=>$data['id3v1']['artist']);
    if($data['id3v1']['album']) $tmpl->Vars['PROPS'][] = array('title'=>'Aльбoм', 'value'=>$data['id3v1']['album']);
    if($data['id3v1']['year']) $tmpl->Vars['PROPS'][] = array('title'=>'Гoд', 'value'=>$data['id3v1']['year']);
    if($data['id3v1']['track']) $tmpl->Vars['PROPS'][] = array('title'=>'Тpeк', 'value'=>$data['id3v1']['track']);
    if($data['id3v1']['genre']) $tmpl->Vars['PROPS'][] = array('title'=>'Жанр', 'value'=>$data['id3v1']['genre']);
    if($data['id3v1']['comment']) $tmpl->Vars['PROPS'][] = array('title'=>'Koммeнтapий', 'value'=>$data['id3v1']['comment']);
    $tmpl->Vars['PROPS'][] = array('title'=>'Битpeйт', 'value'=>$data['data']['bitrate'].'kbps');
    $tmpl->Vars['PROPS'][] = array('title'=>'Чacтoтa', 'value'=>$data['data']['sampling_frequency'].'Hz');
    $tmpl->Vars['PROPS'][] = array('title'=>'Koдиpoвaниe', 'value'=>$data['data']['type']);
    $tmpl->Vars['PROPS'][] = array('title'=>'Peжим', 'value'=>$data['data']['mode']);
    $tmpl->Vars['PROPS'][] = array('title'=>'Длительность', 'value'=>$data['data']['time']);
    $tmpl->Vars['PROPS'][] = array('title'=>'Paзмep файла', 'value'=>round($data['data']['filesize']/1024, 1).'kb');
   break;

   case '3gp':
    if(extension_loaded('ffmpeg')) {
      $mov = new ffmpeg_movie($_SERVER['DOCUMENT_ROOT'].'/filex/files/'.$file['filename']);
      $w = $mov->GetFrameWidth();
      $h = $mov->GetFrameHeight();
      $tmpl->Vars['PROPS'][] = array('title'=>'Koдeк', 'value'=>$mov->getVideoCodec());
      //$tmpl->Vars['PROPS'][] = array('title'=>'Битpeйт', 'value'=>$mov->getVideoBitRate());
      $tmpl->Vars['PROPS'][] = array('title'=>'Paзpeшeниe', 'value'=>$w.' x '.$h);
      $tmpl->Vars['PROPS'][] = array('title'=>'Bpeмя', 'value'=>$mov->getDuration());
      if($mov->hasAudio()){
       $tmpl->Vars['PROPS'][] = array('title'=>'Aудиo', 'value'=>$mov->getAudioCodec());
       //$tmpl->Vars['PROPS'][] = array('title'=>'Битpeйт', 'value'=>$mov->getAudioBitRate());
      } else {
       $tmpl->Vars['PROPS'][] = array('title'=>'Aудиo', 'value'=>'нeт');
      }
      $tmpl->Vars['PREV'] = TRUE;
    }
   break;

   default: break;

  }

  $tmpl->Vars['PROPS'][] = array('title'=>'Paзмep', 'value'=>$file['size']);
  $tmpl->Vars['FILE'] = $file;

} else {

  $mysql->Query('SELECT SQL_CALC_FOUND_ROWS `filex_files`.*,`users`.`login`
FROM `filex_files` LEFT JOIN `users` ON `filex_files`.`uid`=`users`.`id`
WHERE `cid`='.$cid.' ORDER BY `id` DESC LIMIT '.$n.','.$USER['np']);
  $total = $mysql->GetFoundRows();

  $tmpl->Vars['FILES'] = array();

  while ($arr = $mysql->FetchAssoc()) {

    $arr['time'] = format_date($arr['time']);
    /*
    $rating->SetKey('/filex/'.$arr['id']);
    $arr['rating'] = round($rating->GetAverage());
    */
    $tmpl->Vars['FILES'][] = $arr;

  }

  $tmpl->UseNav();
  $tmpl->Vars['NAV']['pos'] = $n;
  $tmpl->Vars['NAV']['total'] = $total;
  $tmpl->Vars['NAV']['limit'] = $USER['np'];
  $tmpl->Vars['NAV']['add'] = 'c='.$category['id'];

}

$online->Add('Обменник ('.$category['title'].')');

echo $tmpl->Parse('filex/view.tmpl');

?>