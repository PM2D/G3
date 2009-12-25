<?php

$DB_STRUCT = '
CREATE TABLE `chat` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `roomid` smallint(5) unsigned NOT NULL default \'1\',
  `uid` smallint(5) unsigned NOT NULL default \'3\',
  `to` smallint(5) unsigned NOT NULL default \'0\',
  `time` int(11) unsigned NOT NULL default \'0\',
  `msg` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `roomid` (`roomid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `chatrooms` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` text NOT NULL,
  PRIMARY KEY  (`id`)
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