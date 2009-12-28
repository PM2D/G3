<?php

function install(){
  fstools::make_file($_SERVER['DOCUMENT_ROOT'].'/var/votes.dat');
}

function uninstall(){
  fstools::remove_if_exists($_SERVER['DOCUMENT_ROOT'].'/var/votes.dat');
}

function update(){
  fstools::make_file($_SERVER['DOCUMENT_ROOT'].'/var/votes.dat');
}

?>