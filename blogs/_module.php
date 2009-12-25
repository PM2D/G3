<?php

$DB_STRUCT = '
CREATE TABLE `blogs` (
  `id` smallint(6) NOT NULL auto_increment,
  `name` tinytext NOT NULL,
  `owner` smallint(6) NOT NULL default \'0\',
  `time` int(11) unsigned NOT NULL default \'0\',
  `perm` tinyblob,
  `favorites` tinyblob,
  PRIMARY KEY  (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `blogs_posts` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `uid` smallint(6) unsigned default NULL,
  `time` int(11) unsigned NOT NULL default \'0\',
  `title` tinytext,
  `data` text NOT NULL,
  `mood` tinytext NOT NULL,
  `music` tinytext NOT NULL,
  `nocomm` tinyint(3) unsigned NOT NULL default \'0\',
  PRIMARY KEY  (`id`),
  KEY `rid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `blogs_comms` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `pid` smallint(6) unsigned NOT NULL default \'0\',
  `buid` smallint(5) unsigned NOT NULL default \'0\',
  `uid` smallint(6) NOT NULL default \'3\',
  `time` int(11) unsigned NOT NULL default \'0\',
  `msg` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
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