<?php

$DB_STRUCT = '
CREATE TABLE `letters` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `to` smallint(5) unsigned NOT NULL default \'0\',
  `new` tinyint(1) unsigned NOT NULL default \'0\',
  `uid` smallint(6) unsigned NOT NULL default \'0\',
  `time` int(11) unsigned NOT NULL default \'0\',
  `subj` tinytext NOT NULL,
  `msg` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `to` (`to`),
  KEY `uid` (`uid`),
  KEY `new` (`new`)
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