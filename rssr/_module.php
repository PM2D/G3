<?php

function install(){
  if(!ini_get('allow_url_fopen')) throw new Exception('Работа данного модуля невозможна т.к. allow_url_fopen отключено.');
  if(!extension_loaded('xmlreader')) throw new Exception('Работа данного модуля невозможна т.к. расширение xmlreader недоступно.');
  if(!fstools::make_dir($_SERVER['DOCUMENT_ROOT'].'/var/cache/rss'))
    throw new Exception('Невозможно создать папку /var/cache/rss');
  if(!fstools::make_file($_SERVER['DOCUMENT_ROOT'].'/var/rssr.dat'))
    throw new Exception('Невозможно создать файл /var/rssr.dat');
}

function uninstall(){
  fstools::remove_if_exists($_SERVER['DOCUMENT_ROOT'].'/var/cache/rss');
  fstools::remove_if_exists($_SERVER['DOCUMENT_ROOT'].'/var/rssr.dat');
}

function update(){
  fstools::make_dir($_SERVER['DOCUMENT_ROOT'].'/var/cache/rss');
  fstools::make_file($_SERVER['DOCUMENT_ROOT'].'/var/rssr.dat');
}

?>