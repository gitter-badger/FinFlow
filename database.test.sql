-- phpMyAdmin SQL Dump
-- version 4.0.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 17, 2014 at 06:31 PM
-- Server version: 5.5.35-0+wheezy1-log
-- PHP Version: 5.4.4-14+deb7u8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `cash_accounts`
--

CREATE TABLE IF NOT EXISTS `cash_accounts` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `cash_accounts`
--

INSERT INTO `cash_accounts` (`account_id`, `account_iban`, `account_currency_id`, `account_slug`, `holder_name`, `holder_swift`, `balance`, `balance_won`, `balance_spent`) VALUES
(1, '', 2, 'paypal', 'PayPal', '', 500, 0, 67.0054),
(2, '', 3, 'intuit-payment', 'Intuit Payment', '', 250, 0, 50);

-- --------------------------------------------------------

--
-- Table structure for table `cash_assoc`
--

CREATE TABLE IF NOT EXISTS `cash_assoc` (
  `trans_id` bigint(20) NOT NULL,
  `label_id` bigint(20) NOT NULL,
  UNIQUE KEY `trans_id` (`trans_id`,`label_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cash_assoc`
--

INSERT INTO `cash_assoc` (`trans_id`, `label_id`) VALUES
(1, 5),
(1, 6),
(2, 2),
(3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `cash_contacts`
--

CREATE TABLE IF NOT EXISTS `cash_contacts` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cash_currency`
--

CREATE TABLE IF NOT EXISTS `cash_currency` (
  `currency_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `csymbol` varchar(12) DEFAULT NULL,
  `cname` varchar(64) NOT NULL,
  `ccode` varchar(12) NOT NULL,
  `cexchange` float NOT NULL DEFAULT '1',
  PRIMARY KEY (`currency_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `cash_currency`
--

INSERT INTO `cash_currency` (`currency_id`, `csymbol`, `cname`, `ccode`, `cexchange`) VALUES
(1, 'RON', 'Romanian Leu', 'RON', 1),
(2, '$', 'US Dollar', 'USD', 3.2277),
(3, '&euro;', 'Euro', 'EUR', 4.4275),
(4, '&pound;', 'British Pound Sterling', 'GBP', 5.4252),
(5, 'RUB', 'Russian Ruble', 'RUB', 0.0927);

-- --------------------------------------------------------

--
-- Table structure for table `cash_currency_history`
--

CREATE TABLE IF NOT EXISTS `cash_currency_history` (
  `currency_id` int(11) NOT NULL,
  `regdate` datetime NOT NULL,
  `cexchange` float NOT NULL DEFAULT '1',
  UNIQUE KEY `currency_id` (`currency_id`,`regdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cash_currency_history`
--

INSERT INTO `cash_currency_history` (`currency_id`, `regdate`, `cexchange`) VALUES
(2, '2014-05-15 17:44:05', 3.5277),
(2, '2014-05-16 17:39:16', 3.1077),
(2, '2014-05-17 17:38:55', 3.2277),
(3, '2014-05-15 17:44:05', 4.7275),
(3, '2014-05-16 17:39:16', 4.3075),
(3, '2014-05-17 17:38:55', 4.4275),
(4, '2014-05-15 17:44:05', 5.7252),
(4, '2014-05-16 17:39:16', 5.3052),
(4, '2014-05-17 17:38:55', 5.4252),
(5, '2014-05-15 17:44:05', 0.3927),
(5, '2014-05-16 17:39:16', -0.0273),
(5, '2014-05-17 17:38:55', 0.0927);

-- --------------------------------------------------------

--
-- Table structure for table `cash_labels`
--

CREATE TABLE IF NOT EXISTS `cash_labels` (
  `label_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `slug` varchar(125) NOT NULL,
  `title` varchar(225) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`label_id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `cash_labels`
--

INSERT INTO `cash_labels` (`label_id`, `parent_id`, `slug`, `title`, `description`) VALUES
(1, 0, 'food-drinks', 'Food & Drinks', 'mâncare și băutură'),
(2, 0, 'jewlery', 'Jewlery', 'altele'),
(3, 0, 'clothes', 'Clothes', 'altele'),
(4, 0, 'others', 'Others', ''),
(5, 0, 'software', 'Software', ''),
(6, 5, 'microsoft', 'Microsoft', ''),
(7, 5, 'apple', 'Apple', '');

-- --------------------------------------------------------

--
-- Table structure for table `cash_op`
--

CREATE TABLE IF NOT EXISTS `cash_op` (
  `trans_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `optype` enum('in','out') NOT NULL DEFAULT 'out',
  `value` float NOT NULL,
  `currency_id` int(11) NOT NULL DEFAULT '1',
  `account_id` int(11) NOT NULL DEFAULT '0',
  `contact_id` int(11) NOT NULL DEFAULT '0',
  `comments` varchar(255) DEFAULT NULL,
  `sdate` datetime NOT NULL,
  `mdate` datetime DEFAULT NULL,
  PRIMARY KEY (`trans_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `cash_op`
--

INSERT INTO `cash_op` (`trans_id`, `optype`, `value`, `currency_id`, `account_id`, `contact_id`, `comments`, `sdate`, `mdate`) VALUES
(1, 'out', 67.0054, 2, 1, 0, 'Driver imprimantă epson', '2014-05-01 00:00:00', NULL),
(2, 'out', 50, 3, 2, 0, 'cumpărat cristale Swrowski!', '2014-05-15 00:00:00', NULL),
(3, 'in', 100, 1, 0, 0, 'vândut moticicletă', '2014-05-17 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cash_op_meta`
--

CREATE TABLE IF NOT EXISTS `cash_op_meta` (
  `meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `trans_id` bigint(20) NOT NULL,
  `meta_key` varchar(64) NOT NULL,
  `meta_value` text,
  PRIMARY KEY (`meta_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `cash_op_meta`
--

INSERT INTO `cash_op_meta` (`meta_id`, `trans_id`, `meta_key`, `meta_value`) VALUES
(1, 1, 'attachments', 'a:2:{i:0;s:24:"attachment-1-676b46b.rtf";i:1;s:24:"attachment-1-b1179c6.doc";}'),
(2, 1, 'attachments_names', 'a:2:{i:0;s:24:"140426192532_Project.rtf";i:1;s:18:"gw-proposal-v1.doc";}'),
(3, 2, 'attachments', 'a:3:{i:0;s:24:"attachment-2-8015e67.jpg";i:1;s:24:"attachment-2-afc1551.pdf";i:2;s:24:"attachment-2-21ff7d6.pdf";}'),
(4, 2, 'attachments_names', 'a:3:{i:0;s:8:"map1.jpg";i:1;s:17:"Order Receipt.pdf";i:2;s:32:"Bill payment_ HSBC Bank UK 2.pdf";}'),
(5, 3, 'attachments', 'a:2:{i:0;s:24:"attachment-3-4b20dc9.doc";i:1;s:24:"attachment-3-e4612d2.pdf";}'),
(6, 3, 'attachments_names', 'a:2:{i:0;s:24:"APPLICATION FORM NEW.doc";i:1;s:30:"Bill payment_ HSBC Bank UK.pdf";}');

-- --------------------------------------------------------

--
-- Table structure for table `cash_op_pending`
--

CREATE TABLE IF NOT EXISTS `cash_op_pending` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `cash_op_pending`
--

INSERT INTO `cash_op_pending` (`trans_id`, `root_id`, `contact_id`, `optype`, `value`, `currency_id`, `recurring`, `sdate`, `fdate`, `active`, `metadata`) VALUES
(1, 0, 0, 'out', 100, 2, 'monthly', '2014-04-25 00:00:01', '2014-05-25 00:00:01', 'yes', 'a:4:{s:6:"labels";a:1:{i:0;s:1:"7";}s:5:"files";a:1:{s:51:"1006103_10152548603299734_2530066573552910861_n.jpg";s:32:"attachment-pending-1-cabb43f.jpg";}s:10:"account_id";i:1;s:8:"comments";s:27:"Subscripție iTunes Max Pro";}'),
(2, 1, 0, 'out', 100, 2, 'no', '2014-05-17 18:19:38', '2014-05-25 00:00:01', 'yes', 'a:5:{s:6:"labels";a:1:{i:0;s:1:"7";}s:5:"files";a:1:{s:51:"1006103_10152548603299734_2530066573552910861_n.jpg";s:32:"attachment-pending-1-cabb43f.jpg";}s:10:"account_id";i:1;s:8:"comments";s:27:"Subscripție iTunes Max Pro";s:11:"x_recurring";s:7:"monthly";}'),
(3, 0, 0, 'in', 25, 1, 'monthly', '2014-05-18 00:00:01', '2014-06-18 00:00:01', 'yes', 'a:4:{s:6:"labels";a:0:{}s:5:"files";a:0:{}s:10:"account_id";i:2;s:8:"comments";s:19:"Câinele la stână";}'),
(4, 3, 0, 'in', 25, 1, 'no', '2014-05-17 18:22:24', '2014-06-18 00:00:01', 'yes', 'a:5:{s:6:"labels";a:0:{}s:5:"files";a:0:{}s:10:"account_id";i:2;s:8:"comments";s:19:"Câinele la stână";s:11:"x_recurring";s:7:"monthly";}');

-- --------------------------------------------------------

--
-- Table structure for table `cash_settings`
--

CREATE TABLE IF NOT EXISTS `cash_settings` (
  `setting_key` varchar(225) NOT NULL,
  `setting_val` varchar(225) DEFAULT NULL,
  `settting_desc` text,
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cash_settings`
--

INSERT INTO `cash_settings` (`setting_key`, `setting_val`, `settting_desc`) VALUES
('cron_8a1abede085fc1d4e7df1934d37b7678', 'finished', ''),
('cron_lastrun_8a1abede085fc1d4e7df1934d37b7678', '1400345045', ''),
('db_version', '1.0', ''),
('exchange_rates_parser', 'bnr', '');

-- --------------------------------------------------------

--
-- Table structure for table `cash_users`
--

CREATE TABLE IF NOT EXISTS `cash_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `level` smallint(6) DEFAULT '1',
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `pw_reset_key` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `cash_users`
--

INSERT INTO `cash_users` (`user_id`, `level`, `email`, `password`, `last_login`, `pw_reset_key`) VALUES
(1, 1, 'adrian@finflow.org', '328f456d82a1ec48667de3d162067442', NULL, NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
