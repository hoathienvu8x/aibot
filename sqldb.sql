CREATE DATABASE IF NOT EXISTS `aibot` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `aibot`;
DROP TABLE IF EXISTS `aiml`;
CREATE TABLE IF NOT EXISTS `aiml` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` int(11) NOT NULL DEFAULT '1',
  `pattern` text NOT NULL,
  `template` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(255) NOT NULL DEFAULT '',
  `cat_desc` varchar(255) NOT NULL DEFAULT '',
  `cat_parent` int(11) NOT NULL DEFAULT '0',
  `cat_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cat_name` (`cat_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `chats`;
CREATE TABLE IF NOT EXISTS `chats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) NOT NULL DEFAULT '',
  `botname` varchar(255) NOT NULL DEFAULT '',
  `guestname` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `sendtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `ramdoms`;
CREATE TABLE IF NOT EXISTS `ramdoms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aiml` int(11) NOT NULL DEFAULT '0',
  `template` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `aiml` (`aiml`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;