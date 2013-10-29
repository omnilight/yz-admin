<?php

class m130218_105112_admin_install extends CDbMigration
{
    public function safeUp()
    {
        $sql=<<<SQL
SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS {{admin_authitem}} (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS {{admin_authitemchild}} (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS {{admin_authassignment}} (
  `userid` int(11) NOT NULL,
  `itemname` varchar(64) NOT NULL,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`userid`,`itemname`),
  KEY `itemname` (`itemname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS {{admin_users}} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `login` varchar(255) NOT NULL,
  `passhash` varchar(128) NOT NULL,
  `salt` varchar(16) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_superadmin` tinyint(1) NOT NULL DEFAULT '0',
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO {{admin_users}} (`id`, `name`, `login`, `passhash`, `salt`, `email`, `is_superadmin`, `is_enabled`) VALUES
(1, 'Admin', 'admin', '381b12e07ddead3d7320010f25ca3c5b', 'e&iv~o?$>i^i!(wz', 'demo@demo.com', 1, 1);

ALTER TABLE {{admin_authassignment}}
  ADD CONSTRAINT {{admin_authassignment_ibfk_1}} FOREIGN KEY (`userid`) REFERENCES {{admin_users}} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT {{admin_authassignment_ibfk_2}} FOREIGN KEY (`itemname`) REFERENCES {{admin_authitem}} (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE {{admin_authitemchild}}
  ADD CONSTRAINT {{admin_authitemchild_ibfk_1}} FOREIGN KEY (`parent`) REFERENCES {{admin_authitem}} (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT {{admin_authitemchild_ibfk_2}} FOREIGN KEY (`child`) REFERENCES {{admin_authitem}} (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS=1;
SQL;
        $this->execute($sql);
    }

    public function safeDown()
    {
        echo "m130218_105112_admin_install does not support migration down.\n";
        return false;
    }
}