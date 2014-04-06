-- phpMyAdmin SQL Dump
-- version 4.0.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 03, 2014 at 11:23 PM
-- Server version: 5.5.35-0+wheezy1
-- PHP Version: 5.4.4-14+deb7u8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `finflow`
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `cash_accounts_assoc`
--

CREATE TABLE IF NOT EXISTS `cash_accounts_assoc` (
  `account_id` int(11) NOT NULL,
  `trans_id` int(11) NOT NULL,
  UNIQUE KEY `account_id` (`account_id`,`trans_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cash_assoc`
--

CREATE TABLE IF NOT EXISTS `cash_assoc` (
  `trans_id` bigint(20) NOT NULL,
  `label_id` bigint(20) NOT NULL,
  UNIQUE KEY `trans_id` (`trans_id`,`label_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `cash_currency_history`
--

CREATE TABLE IF NOT EXISTS `cash_currency_history` (
  `currency_id` int(11) NOT NULL,
  `regdate` datetime NOT NULL,
  `cexchange` float NOT NULL DEFAULT '1',
  UNIQUE KEY `currency_id` (`currency_id`,`regdate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `cash_op`
--

CREATE TABLE IF NOT EXISTS `cash_op` (
  `trans_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `optype` enum('in','out') NOT NULL DEFAULT 'out',
  `value` float NOT NULL,
  `currency_id` int(11) NOT NULL DEFAULT '1',
  `comments` varchar(255) DEFAULT NULL,
  `sdate` datetime NOT NULL,
  `mdate` datetime DEFAULT NULL,
  PRIMARY KEY (`trans_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- Table structure for table `cash_op_pending`
--

CREATE TABLE IF NOT EXISTS `cash_op_pending` (
  `trans_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `root_id` int(11) DEFAULT '0',
  `optype` enum('in','out') NOT NULL DEFAULT 'out',
  `value` float NOT NULL,
  `currency_id` int(11) NOT NULL DEFAULT '1',
  `recurring` enum('no','daily','monthly','yearly') DEFAULT 'no',
  `sdate` datetime NOT NULL,
  `fdate` datetime DEFAULT NULL,
  `active` enum('yes','no') DEFAULT 'yes',
  `metadata` text,
  PRIMARY KEY (`trans_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

--
-- Table structure for table `cash_settings`
--

CREATE TABLE IF NOT EXISTS `cash_settings` (
  `setting_key` varchar(225) NOT NULL,
  `setting_val` varchar(225) DEFAULT NULL,
  `settting_desc` text,
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
