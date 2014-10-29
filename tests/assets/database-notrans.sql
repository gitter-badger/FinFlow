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

TRUNCATE `fn_accounts`;
INSERT INTO `fn_accounts` (`account_id`, `account_iban`, `account_currency_id`, `account_slug`, `holder_name`, `holder_swift`, `balance`, `balance_won`, `balance_spent`) VALUES
(1,	'0123PAYPAL',	4,	'paypal',	'PayPal',	'PAYPAL001',	0,	0,	0),
(2,	'8764BANK21',	3,	'some-bank',	'Some Bank',	'SMBNK764',	0,	0,	0);

DROP TABLE IF EXISTS `fn_assoc`;
CREATE TABLE `fn_assoc` (
  `trans_id` bigint(20) NOT NULL,
  `label_id` bigint(20) NOT NULL,
  UNIQUE KEY `trans_id` (`trans_id`,`label_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

TRUNCATE `fn_assoc`;

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

TRUNCATE `fn_contacts`;

DROP TABLE IF EXISTS `fn_currency`;
CREATE TABLE `fn_currency` (
  `currency_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `csymbol` varchar(12) DEFAULT NULL,
  `cname` varchar(64) NOT NULL,
  `ccode` varchar(12) NOT NULL,
  `cexchange` float NOT NULL DEFAULT '1',
  PRIMARY KEY (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

TRUNCATE `fn_currency`;
INSERT INTO `fn_currency` (`currency_id`, `csymbol`, `cname`, `ccode`, `cexchange`) VALUES
(1,	'✪',	'Base Currency',	'BSC',	1),
(2,	'♠',	'Spades',	'SPD',	2),
(3,	'♣',	'Club',	'CLB',	0.5),
(4,	'♥',	'Hearts',	'HRT',	0.25);

DROP TABLE IF EXISTS `fn_currency_history`;
CREATE TABLE `fn_currency_history` (
  `currency_id` int(11) NOT NULL,
  `regdate` datetime NOT NULL,
  `cexchange` float NOT NULL DEFAULT '1',
  UNIQUE KEY `currency_id` (`currency_id`,`regdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

TRUNCATE `fn_currency_history`;
INSERT INTO `fn_currency_history` (`currency_id`, `regdate`, `cexchange`) VALUES
(2,	'2014-09-03 15:23:04',	0.4),
(2,	'2014-09-17 15:19:50',	0.8),
(2,	'2014-10-01 15:18:57',	1.2),
(2,	'2014-10-15 15:17:35',	1.6),
(2,	'2014-10-28 15:23:04',	2),
(3,	'2014-09-03 15:23:04',	0.1),
(3,	'2014-09-17 15:19:50',	0.2),
(3,	'2014-10-01 15:18:57',	0.3),
(3,	'2014-10-15 15:17:35',	0.4),
(3,	'2014-10-28 15:23:04',	0.5),
(4,	'2014-09-03 15:23:04',	0.05),
(4,	'2014-09-17 15:19:50',	0.1),
(4,	'2014-10-01 15:18:57',	0.15),
(4,	'2014-10-15 15:17:35',	0.2),
(4,	'2014-10-28 15:23:04',	0.25);

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

TRUNCATE `fn_labels`;
INSERT INTO `fn_labels` (`label_id`, `parent_id`, `slug`, `title`, `description`) VALUES
(1,	0,	'video-games',	'Video games',	'money spend or won at video games'),
(2,	0,	'holidays',	'Holidays',	''),
(4,	0,	'gadgets',	'Gadgets',	'apple, iphones etc.'),
(5,	0,	'clothes-fashion',	'Clothes & Fashion',	''),
(6,	4,	'apple',	'Apple',	''),
(7,	4,	'samsung',	'Samsung',	''),
(8,	0,	'software',	'Software',	''),
(9,	0,	'hardware',	'Hardware',	'');

DROP TABLE IF EXISTS `fn_op`;
CREATE TABLE `fn_op` (
  `trans_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `optype` enum('in','out') NOT NULL DEFAULT 'out',
  `value` float NOT NULL,
  `value_cdf` float NOT NULL DEFAULT '1',
  `currency_id` int(11) NOT NULL DEFAULT '1',
  `account_id` int(11) NOT NULL DEFAULT '0',
  `contact_id` int(11) NOT NULL DEFAULT '0',
  `comments` varchar(255) DEFAULT NULL,
  `sdate` datetime NOT NULL,
  `mdate` datetime DEFAULT NULL,
  PRIMARY KEY (`trans_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

TRUNCATE `fn_op`;

DROP TABLE IF EXISTS `fn_op_meta`;
CREATE TABLE `fn_op_meta` (
  `meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `trans_id` bigint(20) NOT NULL,
  `meta_key` varchar(64) NOT NULL,
  `meta_value` text,
  PRIMARY KEY (`meta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

TRUNCATE `fn_op_meta`;

DROP TABLE IF EXISTS `fn_op_pending`;
CREATE TABLE `fn_op_pending` (
  `trans_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `root_id` int(11) DEFAULT '0',
  `contact_id` int(11) NOT NULL DEFAULT '0',
  `optype` enum('in','out') NOT NULL DEFAULT 'out',
  `value` float NOT NULL,
  `currency_id` int(11) NOT NULL DEFAULT '1',
  `recurring` enum('no','daily','monthly','yearly') DEFAULT 'no',
  `sdate` datetime NOT NULL,
  `fdate` datetime DEFAULT NULL,
  `active` enum('yes','no') DEFAULT 'yes',
  `metadata` text,
  PRIMARY KEY (`trans_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

TRUNCATE `fn_op_pending`;

DROP TABLE IF EXISTS `fn_settings`;
CREATE TABLE `fn_settings` (
  `setting_key` varchar(225) NOT NULL,
  `setting_val` varchar(225) DEFAULT NULL,
  `settting_desc` text,
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

TRUNCATE `fn_settings`;

DROP TABLE IF EXISTS `fn_users`;
CREATE TABLE `fn_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `level` smallint(6) DEFAULT '1',
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `pw_reset_key` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

TRUNCATE `fn_users`;
INSERT INTO `fn_users` (`user_id`, `level`, `email`, `password`, `last_login`, `pw_reset_key`) VALUES
(1,	1,	'adrian@example.com',	'460eaee0d906ad1c23f8162e32fdea5c',	'2014-10-29 13:44:36',	NULL);

-- 2014-10-30 11:34:50
