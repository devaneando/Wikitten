-- Just a random table from a project of mine

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_parent` int(10) unsigned NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT 'n/a',
  `created` datetime NOT NULL,
  `meta` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_files_files1` (`id_parent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;