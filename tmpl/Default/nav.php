<?php
// This file is a part of AugurCMS (g3.pm2d.ru)

// функция для простой навигации
function NAV($page_url, $pos, $total, $limit, $addvars=null){
  if($addvars) $addvars = '&amp;'.$addvars;
  if(($pos+$limit)>$limit){
    if($pos<$limit) $pos = $limit;
    echo('<a href="'.$page_url.'?n='.($pos-$limit).$addvars.'&amp;'.SID.'">&lt;нaзaд</a> | ');
  } else echo('&lt;нaзaд | ');
  if($total>($pos+$limit)){
    echo('<a href="'.$page_url.'?n='.($pos+$limit).$addvars.'&amp;'.SID.'">дaлee&gt;</a>');
  } else echo('дaлee&gt;');
}

// функция для постраничной навигации
function PAGES($page_url, $pos, $total, $limit, $addvars=null){
  if($total>$limit){
    if($addvars) $addvars = '&amp;'.$addvars;
    $pos++;
    $max = ceil($total/$limit);
    $now = ceil($pos/$limit);
    (1==$now) ? print('1..') : print('<a href="'.$page_url.'?n=0'.$addvars.'&amp;'.SID.'">1</a>..');
    if($max<5){
      for($i=2; $i<$max; $i++){
        if($i==$now){
          print($now.'..');
        } else {
          print('<a href="'.$page_url.'?n='.($limit*($i-1)).$addvars.'&amp;'.SID.'">'.$i.'</a>..');
        }
      }
    }elseif($now<4){
      for($i=2; $i<5; $i++){
        if($i==$now){
          print($now.'..');
        } else {
          print('<a href="'.$page_url.'?n='.($limit*($i-1)).$addvars.'&amp;'.SID.'">'.$i.'</a>..');
        }
      }
    }elseif($now>($max-3)){
      for($i=$max-3; $i<$max; $i++){
        if($i==$now){
          print($now.'..');
        } else {
          print('<a href="'.$page_url.'?n='.($limit*($i-1)).$addvars.'&amp;'.SID.'">'.$i.'</a>..');
        }
      }
    } else {
      for($i=($now-2); $i<($now+3); $i++){
        if($i==$now){
          print($now.'..');
        } elseif($i==($now-2) || $i==($now+2)){
          print('<a href="'.$page_url.'?n='.($limit*($i-1)).$addvars.'&amp;'.SID.'">'.$i.'</a>..');
        };
      }
    }
    if($now==$max) print($max);
    else print('<a href="'.$page_url.'?n='.($limit*($max-1)).$addvars.'&amp;'.SID.'">'.$max.'</a>');
    print('<br />');
  }
}

?>