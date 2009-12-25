<?php

$DB_STRUCT = '
CREATE TABLE `forums` (
  `id` smallint unsigned NOT NULL auto_increment,
  `rid` smallint unsigned NOT NULL default \'0\',
  `name` tinytext NOT NULL,
  `about` text NOT NULL,
  `count` smallint unsigned NOT NULL default \'0\',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `forum_themes` (
  `id` smallint unsigned NOT NULL auto_increment,
  `prid` smallint unsigned NOT NULL default \'0\',
  `rid` smallint unsigned NOT NULL default \'0\',
  `time` int(11) unsigned NOT NULL default \'0\',
  `name` tinytext NOT NULL,
  `lastuid` smallint(5) unsigned NOT NULL default \'3\',
  `lastuser` tinytext NOT NULL,
  `fixed` tinyint(3) unsigned NOT NULL,
  `closed` tinyint(3) unsigned NOT NULL,
  `count` smallint(5) unsigned NOT NULL default \'0\',
  PRIMARY KEY  (`id`),
  KEY `rid` (`rid`),
  KEY `time` (`time`),
  KEY `prid` (`prid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `forum_posts` (
  `id` mediumint unsigned NOT NULL auto_increment,
  `tid` smallint unsigned NOT NULL default \'0\',
  `uid` smallint unsigned NOT NULL default \'3\',
  `time` int(11) NOT NULL default \'0\',
  `msg` text NOT NULL,
  `sign` tinytext NOT NULL,
  `attid` smallint unsigned NOT NULL default \'0\',
  PRIMARY KEY  (`id`),
  KEY `tid` (`tid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
';

function install(){
  create_db_tables();
  global $CFG;
  $CFG['FORUM'] = array('guests'=>FALSE, 'attcid'=>FALSE);
  save_cfg();
}

function uninstall(){
  drop_db_tables();
  global $CFG;
  unset($CFG['FORUM']);
  save_cfg();
}

function update(){
  update_db_tables();
  global $CFG;
  if(!isset($CFG['FORUM']['guests'])){
    $CFG['FORUM']['guests'] = FALSE;
    save_cfg();
  }
  if(!isset($CFG['FORUM']['attcid'])){
    $CFG['FORUM']['attcid'] = FALSE;
    save_cfg();
  }
}

?>