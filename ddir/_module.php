<?php

$DB_STRUCT = '
CREATE TABLE `dirs` (
  `id` int(10) unsigned NOT NULL,
  `did` int(10) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `name` tinytext NOT NULL,
  `path` tinytext NOT NULL,
  `count` mediumint(8) unsigned NOT NULL,
  `listed` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `did` (`did`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `files` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `did` int(10) unsigned NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `type` tinytext NOT NULL,
  `name` tinytext NOT NULL,
  `path` tinytext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `did` (`did`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
';

function install() {
  create_db_tables();
  global $CFG;
  if(!isset($CFG['DDIR'])) {
    // defaults:
    $CFG['DDIR']['sort'] = 0;
    $CFG['DDIR']['rev'] = FALSE;
    $CFG['DDIR']['ico'] = 'gif,jpg,jad,jar,mid,mp3,amr,txt,zip,3gp,thm';
    $CFG['DDIR']['unzip'] = FALSE;
    $CFG['DDIR']['ffmpeg'] = FALSE;
    $CFG['DDIR']['view'] = 1;
    save_cfg();
  }
  fstools::make_dir($_SERVER['DOCUMENT_ROOT'].'/var/cache/zip');
  fstools::make_dir($_SERVER['DOCUMENT_ROOT'].'/tmp/mp3');
  fstools::make_dir($_SERVER['DOCUMENT_ROOT'].'/tmp/jar');
  include($_SERVER['DOCUMENT_ROOT'].'/etc/ddir/listroot.php');
}

function uninstall() {
  drop_db_tables();
  global $CFG;
  unset($CFG['DDIR']);
  save_cfg();
  fstools::remove_if_exists($_SERVER['DOCUMENT_ROOT'].'/var/cache/zip');
}

function update() {
  update_db_tables();
  global $CFG;
  if(!isset($CFG['DDIR']['view'])) $CFG['DDIR']['view'] = 1;
  fstools::make_dir($_SERVER['DOCUMENT_ROOT'].'/var/cache/zip');
  fstools::make_dir($_SERVER['DOCUMENT_ROOT'].'/var/cache/imgs');
  fstools::make_dir($_SERVER['DOCUMENT_ROOT'].'/tmp/mp3');
  fstools::make_dir($_SERVER['DOCUMENT_ROOT'].'/tmp/jar');
}

?>