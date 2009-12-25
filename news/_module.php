<?php

$DB_STRUCT = '
CREATE TABLE `news` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `uid` smallint(5) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `title` tinytext NOT NULL,
  `text` text NOT NULL,
  `tags` tinytext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `news_comms` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `nid` smallint(5) unsigned NOT NULL,
  `uid` smallint(5) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `msg` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `nid` (`nid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
';

function install(){
  create_db_tables();
}

function uninstall(){
  drop_db_tables();
}

function update(){
  update_db_tables();
}

?>