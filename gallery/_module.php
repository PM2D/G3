<?php

$DB_STRUCT = '
CREATE TABLE `gallery_albums` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `uid` smallint(5) unsigned NOT NULL,
  `title` tinytext NOT NULL,
  `time` int(12) unsigned NOT NULL,
  `perm` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `gallery_files` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `uid` smallint(5) unsigned NOT NULL,
  `time` int(12) unsigned NOT NULL,
  `type` tinytext NOT NULL,
  `filesize` mediumint(8) unsigned NOT NULL,
  `width` smallint(5) unsigned NOT NULL,
  `height` smallint(5) unsigned NOT NULL,
  `title` tinytext NOT NULL,
  `about` tinytext NOT NULL,
  `views` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`,`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `gallery_comms` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `auid` smallint(5) unsigned NOT NULL,
  `fid` smallint(5) unsigned NOT NULL,
  `uid` smallint(5) unsigned NOT NULL,
  `time` int(12) unsigned NOT NULL,
  `msg` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `gid` (`auid`,`fid`,`uid`,`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
';

function install(){
  if(!is_writable($_SERVER['DOCUMENT_ROOT'].'/gallery/files'))
    throw new Exception('Запись в папку /gallery/files невозможна. Установка отменена.');
  create_db_tables();
  global $CFG;
  $CFG['GALLERY'] = array('max'=>65536, 'allowed'=>'jpg,gif');
  save_cfg();
  fstools::make_dir($_SERVER['DOCUMENT_ROOT'].'/var/cache/imgs');
}

function uninstall(){
  drop_db_tables();
  global $CFG;
  if(isset($CFG['GALLERY'])){
    unset($CFG['GALLERY']);
    save_cfg();
  }
  fstools::clear_dir($_SERVER['DOCUMENT_ROOT'].'/filex/files');
}

function update(){
  update_db_tables();
  global $CFG;
  if(!isset($CFG['GALLERY'])){
    $CFG['GALLERY'] = array('max'=>65536, 'allowed'=>'jpg,gif');
    save_cfg();
  }
}

?>