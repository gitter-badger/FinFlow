-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `fn_accounts`;
CREATE TABLE `fn_accounts` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_iban` varchar(128) DEFAULT NULL,
  `account_currency_id` int(11) NOT NULL,
  `account_slug` varchar(128) DEFAULT NULL,
  `holder_name` varchar(255) NOT NULL,
  `holder_swift` varchar(128) DEFAULT NULL,
  `balance` float DEFAULT NULL,
  `balance_won` float NOT NULL DEFAULT '0',
  `balance_spent` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`account_id`),
  UNIQUE KEY `account_slug` (`account_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `fn_assoc`;
CREATE TABLE `fn_assoc` (
  `trans_id` bigint(20) NOT NULL,
  `label_id` bigint(20) NOT NULL,
  UNIQUE KEY `trans_id` (`trans_id`,`label_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `fn_contacts`;
CREATE TABLE `fn_contacts` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_slug` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `postcode` varchar(24) DEFAULT NULL,
  `address` text,
  PRIMARY KEY (`contact_id`),
  KEY `contact_id` (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `fn_currency`;
CREATE TABLE `fn_currency` (
  `currency_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `csymbol` varchar(12) DEFAULT NULL,
  `cname` varchar(64) NOT NULL,
  `ccode` varchar(12) NOT NULL,
  `cexchange` float NOT NULL DEFAULT '1',
  PRIMARY KEY (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `fn_currency_history`;
CREATE TABLE `fn_currency_history` (
  `currency_id` int(11) NOT NULL,
  `regdate` datetime NOT NULL,
  `cexchange` float NOT NULL DEFAULT '1',
  UNIQUE KEY `currency_id` (`currency_id`,`regdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `fn_files`;
CREATE TABLE `fn_files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `fn_labels`;
CREATE TABLE `fn_labels` (
  `label_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `slug` varchar(125) NOT NULL,
  `title` varchar(225) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`label_id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `fn_op`;
CREATE TABLE `fn_op` (
  `trans_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `optype` enum('in','out') NOT NULL DEFAULT 'out',
  `value` float NOT NULL,
  `currency_id` int(11) NOT NULL DEFAULT '1',
  `account_id` int(11) NOT NULL DEFAULT '0',
  `contact_id` int(11) NOT NULL DEFAULT '0',
  `comments` varchar(255) DEFAULT NULL,
  `sdate` datetime NOT NULL,
  `mdate` datetime DEFAULT NULL,
  PRIMARY KEY (`trans_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `fn_op_meta`;
CREATE TABLE `fn_op_meta` (
  `meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `trans_id` bigint(20) NOT NULL,
  `meta_key` varchar(64) NOT NULL,
  `meta_value` text,
  PRIMARY KEY (`meta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `fn_op_pending`;
CREATE TABLE `fn_op_pending` (
  `trans_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `root_id` int(11) DEFAULT '0',
  `contact_id` int(11) NOT NULL DEFAULT '0',
  `optype` enum('in','out') NOT NULL DEFAULT 'out',
  `value` float NOT NULL,
  `currency_id` int(11) NOT NULL DEFAULT '1',
  `recurring` enum('no','daily','monthly','yearly') DEFAULT 'no',
  `sdate` datetime NOT NULL,
  `fdate` datetime DEFAULT NULL,
  `active` enum('yes','no') DEFAULT 'yes',
  `autoconfirm` enum('yes','no') DEFAULT NULL,
  `metadata` text,
  PRIMARY KEY (`trans_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `fn_op_pending_meta`;
CREATE TABLE `fn_op_pending_meta` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `meta_key` varchar(255) NOT NULL,
  `meta_value` text,
  PRIMARY KEY (`meta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `fn_settings`;
CREATE TABLE `fn_settings` (
  `setting_key` varchar(225) NOT NULL,
  `setting_val` varchar(225) DEFAULT NULL,
  `setting_type` varchar(12) DEFAULT NULL,
  `settting_desc` text,
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `fn_users`;
CREATE TABLE `fn_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `level` smallint(6) DEFAULT '1',
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `status` enum('offline,online,disabled') DEFAULT NULL,
  `pw_reset_key` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `fn_users_meta`;
CREATE TABLE `fn_users_meta` (
  `meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `meta_key` varchar(255) NOT NULL,
  `meta_value` text,
  PRIMARY KEY (`meta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2015-05-05 21:15:37
