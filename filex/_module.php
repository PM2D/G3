<?php

$DB_STRUCT = '
CREATE TABLE `filex_cats` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `title` tinytext NOT NULL,
  `time` int(12) unsigned NOT NULL,
  `types` tinytext NOT NULL,
  `max` int(10) unsigned NOT NULL,
  `limit` smallint(5) unsigned NOT NULL,
  `passw` tinytext,
  `about` text,
  PRIMARY KEY  (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `filex_files` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `cid` smallint(5) unsigned NOT NULL,
  `uid` smallint(5) unsigned NOT NULL,
  `time` int(12) unsigned NOT NULL,
  `type` tinytext NOT NULL,
  `size` mediumint(8) unsigned NOT NULL,
  `width` smallint(5) unsigned NOT NULL,
  `height` smallint(5) unsigned NOT NULL,
  `title` tinytext NOT NULL,
  `about` tinytext NOT NULL,
  `dloads` smallint(5) unsigned NOT NULL,
  `comms` smallint(6) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cid` (`cid`,`time`),
  KEY `uid` (`uid`),
  KEY `comms` (`comms`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `filex_comms` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `cid` smallint(5) unsigned NOT NULL,
  `fuid` smallint(5) unsigned NOT NULL,
  `fid` smallint(5) unsigned NOT NULL,
  `uid` smallint(5) unsigned NOT NULL,
  `time` int(12) unsigned NOT NULL,
  `msg` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `gid` (`fuid`,`fid`,`uid`,`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
';

function install(){
  if(!is_writable($_SERVER['DOCUMENT_ROOT'].'/filex/files'))
    throw new Exception('Недостаточно прав для записи в папку /filex/files. Установка отменена.');
  create_db_tables();
  global $CFG;
  $CFG['FILEX']['guests'] = FALSE;
  $CFG['FILEX']['view'] = 0;
  save_cfg();
  fstools::make_dir($_SERVER['DOCUMENT_ROOT'].'/var/cache/imgs');
}

function uninstall(){
  drop_db_tables();
  fstools::clear_dir($_SERVER['DOCUMENT_ROOT'].'/filex/files');
  global $CFG;
  unset($CFG['FILEX']);
  save_cfg();
}

function update(){
  update_db_tables();
  global $CFG;
  if (!isset($CFG['FILEX']['guests'])) {
    $CFG['FILEX']['guests'] = FALSE;
    save_cfg();
  }
  if (!isset($CFG['FILEX']['view'])) {
    $CFG['FILEX']['view'] = 0;
    save_cfg();
  }
  fstools::make_dir($_SERVER['DOCUMENT_ROOT'].'/var/cache/imgs');
}

?>