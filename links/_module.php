<?php

function install() {
  fstools::make_file($_SERVER['DOCUMENT_ROOT'].'/var/links.dat');
}

function uninstall() {
  fstools::remove_if_exists($_SERVER['DOCUMENT_ROOT'].'/var/links.dat');
}

function update() {
  fstools::make_file($_SERVER['DOCUMENT_ROOT'].'/var/links.dat');
  $file = file($_SERVER['DOCUMENT_ROOT'].'/var/links.dat');
  if(isset($file[0])) {
    $arr = explode('|:|', $file[0]);
    if(3>count($arr)) {
      $cnt = count($file);
      for($i=0; $i<$cnt; $i++) {
        $file[$i] = substr($file[$i], 0 ,-1)."|:|\n";
      }
      $f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/links.dat', 'w');
      fwrite($f, implode(NULL, $file));
      fclose($f);
    }
  }
}

?>