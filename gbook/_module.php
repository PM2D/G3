<?php

$DB_STRUCT = '
CREATE TABLE `gbook` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `uid` smallint(5) unsigned NOT NULL default \'0\',
  `time` int(11) unsigned NOT NULL default \'0\',
  `msg` text NOT NULL,
  `sign` varchar(255) default NULL,
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