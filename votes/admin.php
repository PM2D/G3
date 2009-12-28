<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

if(2>$USER['state']) raise_error('Доступ запрещён.');

$do =& getvar('do');
$file = file($_SERVER['DOCUMENT_ROOT'].'/var/votes.dat');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

switch($do) {

 case 'del':
  $tmpl->Vars['TITLE'] = 'Удаление опроса';
  if(!isset($_GET['n'])) raise_error('$_GET[\'n\'] isn\'t set.');
  $n = intval($_GET['n']);
  $arr = explode('|', rtrim($file[$n]));
  unset($file[$n]);
  $f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/votes.dat', 'w');
  fwrite($f, implode(NULL, $file));
  fclose($f);
  $rating = new rating;
  $rating->Remove('/votes/'.$arr[0]);
  $tmpl = new template;
  $tmpl->Vars['MESSAGE'] = 'Опрос "'.$arr[0].'" удалён.';
  $tmpl->Vars['BACK'] = 'admin.php?'.SID;
  echo $tmpl->Parse('notice.tmpl');
 break;

 case 'new':
  $tmpl->Vars['TITLE'] = 'Создать новый опрос';
  echo $tmpl->Parse('votes/admin/vote_new.tmpl');
 break;

 case 'add':
  $tmpl->Vars['TITLE'] = 'Добавление опроса';
  if(!trim($_POST['ques']))
    raise_error('Не заполнено поле вопроса.', 'admin.php?'.SID);
  $arr = array();
  $arr[] = stripslashes(htmlspecialchars($_POST['ques']));
  for($i=1; $i<13; $i++) {
    if(trim($_POST['v'.$i])) $arr[] = stripslashes(htmlspecialchars(strtr($_POST['v'.$i], array("\n"=>NULL, '|'=>NULL))));
  }
  if(2>sizeof($arr)) raise_error('Должен быть хотя бы один вариант ответа.', 'admin.php?'.SID);
  $f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/votes.dat', 'a');
  fwrite($f, implode('|', $arr)."\n");
  fclose($f);
  $tmpl = new template;
  $tmpl->Vars['MESSAGE'] = 'Опрос добавлен.';
  $tmpl->Vars['BACK'] = 'admin.php?'.SID;
  echo $tmpl->Parse('notice.tmpl'); 
 break;

 case 'update':
  $tmpl->Vars['TITLE'] = 'Обновление опроса';
  if(!isset($_GET['n'])) raise_error('$_GET[\'n\'] isn\'t set.');
  if(!trim($_POST['ques']))
    raise_error('Не заполнено поле вопроса.', 'admin.php?'.SID);
  $n = intval($_GET['n']);
  $arr = array();
  $arr[] = stripslashes(htmlspecialchars(strtr($_POST['ques'], array("\n"=>NULL, '|'=>NULL))));
  for($i=1; $i<13; $i++) {
    if(trim($_POST['v'.$i])) $arr[] = stripslashes(htmlspecialchars(strtr($_POST['v'.$i], array("\n"=>NULL, '|'=>NULL))));
  }
  if(2>sizeof($arr)) raise_error('Должен быть хотя бы один вариант ответа.', 'admin.php?'.SID);
  $file[$n] = implode('|', $arr)."\n";
  $f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/votes.dat', 'w');
  fwrite($f, implode(NULL, $file));
  fclose($f);
  $tmpl->Vars['MESSAGE'] = 'Опрос изменён.';
  $tmpl->Vars['BACK'] = 'admin.php?'.SID;
  echo $tmpl->Parse('notice.tmpl');
 break;

 case 'edit':
  $tmpl->Vars['TITLE'] = 'Редактирование опроса';
  if(!isset($_GET['n'])) raise_error('$_GET[\'n\'] isn\'t set.', 'admin.php?'.SID);
  $n = intval($_GET['n']);
  $arr = explode('|', rtrim($file[$n]));
  $cnt = count($arr);
  $tmpl->Vars['QID'] = $n;
  $tmpl->Vars['QUESTION'] = $arr[0];
  $tmpl->Vars['VARIANTS'] = array();
  for($i=1; $i<13; $i++) {
    $tmpl->Vars['VARIANTS'][$i] = isset($arr[$i]) ? $arr[$i] : NULL;
  }
  echo $tmpl->Parse('votes/admin/vote_edit.tmpl');
 break;

 default:
  $tmpl->Vars['TITLE'] = 'Управление опросами';
  $fcnt = count($file);
  $tmpl->Vars['VOTES'] = array();
  for($i=0; $i<$fcnt; $i++) {
    $arr = explode('|', $file[$i]);
    $vcnt = count($arr);
    $tmpl->Vars['VOTES'][$i]['title'] = $arr[0];
    $tmpl->Vars['VOTES'][$i]['variants'] = array();
    for($j=1; $j<$vcnt; $j++) {
      $tmpl->Vars['VOTES'][$i]['variants'][$j] = $arr[$j];
    }
  }
  echo $tmpl->Parse('votes/admin/votes.tmpl');
 break;

}

?>