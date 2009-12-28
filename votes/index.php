<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Опросы';
$tmpl->Vars['VOTES'] = array();

$file = file($_SERVER['DOCUMENT_ROOT'].'/var/votes.dat');
$fcnt = count($file);

$rating = new rating;

// просмотр отдельного опроса
if(isset($_GET['v'])) {

  $i = intval($_GET['v']);

  if(!isset($file[$i])) raise_error('Нет такого опроса.', '/votes/?'.SID);

  $arr = explode('|', rtrim($file[$i]));
  $vcnt = count($arr);

  $rating->SetKey('/votes/'.$arr[0], $vcnt);
  $rarr = $rating->Get();

  $tmpl->Vars['VOTE']['id'] = $i;
  $tmpl->Vars['VOTE']['title'] = $arr[0];
  $tmpl->Vars['VOTE']['total'] = $rarr['cnt'];

  // расчет одного процента
  if(0 == $rarr['cnt']) {
    $percent = 1;
  // если голосов еще нет, то процент будет равным 1 чтобы не было деления на ноль далее
  } else {
    $percent = $rarr['cnt'] / 100;
  }

  for($j=1; $j<$vcnt; $j++) {
    $total = $rating->GetByValue($j);
    $tmpl->Vars['VARIANTS'][$j]['id'] = $j;
    $tmpl->Vars['VARIANTS'][$j]['title'] = $arr[$j];
    $tmpl->Vars['VARIANTS'][$j]['percent'] = round($total/$percent, 1);
    $tmpl->Vars['VARIANTS'][$j]['total'] = $total;
  }
  // соседние опросы
  $tmpl->Vars['PREV'] = isset($file[$i-1]) ? ($i-1) : FALSE;
  $tmpl->Vars['NEXT'] = isset($file[$i+1]) ? ($i+1) : FALSE;

  echo $tmpl->Parse('votes/view.tmpl');

// вывод всех опросов
} else {

  for($i=0; $i<$fcnt; $i++) {

    $arr = explode('|', rtrim($file[$i]));
    $vcnt = count($arr);

    $rating->SetKey('/votes/'.$arr[0], $vcnt);
    $rarr = $rating->Get();

    $tmpl->Vars['VOTES'][$i]['id'] = $i;
    $tmpl->Vars['VOTES'][$i]['title'] = $arr[0];
    $tmpl->Vars['VOTES'][$i]['total'] = $rarr['cnt'];
    $tmpl->Vars['VOTES'][$i]['variants'] = $vcnt-1;
    $tmpl->Vars['VOTES'][$i]['most'] = ($rarr['avg']) ? $arr[round($rarr['avg'])] : 'Нет';

    // расчет одного процента
    if(0 == $rarr['cnt']) {
      $percent = 1;
    // если голосов еще нет, то процент будет равным 1 чтобы не было деления на ноль далее
    } else {
      $percent = $rarr['cnt'] / 100;
    }

    for($j=1; $j<$vcnt; $j++) {
      $total = $rating->GetByValue($j);
      $tmpl->Vars['VOTES'][$i]['VARIANTS'][$j]['id'] = $j;
      $tmpl->Vars['VOTES'][$i]['VARIANTS'][$j]['title'] = $arr[$j];
      $tmpl->Vars['VOTES'][$i]['VARIANTS'][$j]['percent'] = round($total/$percent, 1);
      $tmpl->Vars['VOTES'][$i]['VARIANTS'][$j]['total'] = $total;
    }

  }

  echo $tmpl->Parse('votes/index.tmpl');

}

?>