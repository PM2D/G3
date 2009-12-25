<?php

function install(){
  if(!touch($_SERVER['DOCUMENT_ROOT'].'/var/links.dat')) return FALSE;
  return TRUE;
}

function uninstall(){
  @unlink($_SERVER['DOCUMENT_ROOT'].'/var/links.dat');
  return TRUE;
}

?>