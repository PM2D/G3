<?php
// This file is a part of GIII (g3.steelwap.org)

if(isset($_COOKIE['t2b4y'])){
  header('Location: /banned.php');
  exit;
};

$arr = file($_SERVER['DOCUMENT_ROOT'].'/var/ban.dat');

$cnt = count($arr);

$banned = FALSE;

for($i=0; $i<$cnt; $i++){

  $a = explode('|:|', $arr[$i]);

  switch($a[0]){

   case 0:
    if($_SERVER['HTTP_USER_AGENT'].' IP:'.$_SERVER['REMOTE_ADDR']."\n"==$a[1]) $banned = TRUE;
   break;

   case 1:
    if($_SERVER['HTTP_USER_AGENT']."\n"==$a[1]) $banned = TRUE;
   break;

   case 2:
    if($_SERVER['REMOTE_ADDR']."\n"==$a[1]) $banned = TRUE;
   break;

   case 3:
    if($USER['id']."\n"==$a[1]){
     if(setcookie('t2b4y', TRUE, $TIME+604800,'/')){
      unset($arr[$i]);
      $f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/ban.dat', 'w');
      fwrite($f, implode(NULL, $arr));
      fclose($f);
     };
     $banned = TRUE;
    }
   break;

   case 4:
    if($USER['id']."\n"==$a[1]) $banned = TRUE;
   break;

   case 5:
    $banned = TRUE;
   break;

  }

  if(TRUE===$banned){
    Header('Location: /banned.php');
    exit;
  };

}

?>