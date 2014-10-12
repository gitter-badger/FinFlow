CREATE TABLE IF NOT EXISTS `fn_currency` (
  `currency_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `csymbol` varchar(12) DEFAULT NULL,
  `cname` varchar(64) NOT NULL,
  `ccode` varchar(12) NOT NULL,
  `cexchange` float NOT NULL DEFAULT '1',
  PRIMARY KEY (`currency_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

INSERT INTO `fn_currency` (`currency_id`, `csymbol`, `cname`, `ccode`, `cexchange`) VALUES
(1, 'lei', 'Leul rom&#226;nesc', 'RON', 1),
(9, '&euro;', 'Euro', 'EUR', 4.4369),
(10, '$', 'Dolar', 'USD', 3.4628),
(11, '&pound;', 'Lira sterlină', 'GBP', 5.155),
(12, 'CHF', 'Francul elvețian', 'CHF', 3.5728),
(13, 'руб', 'Rubla rusească', 'RUB', 0.1052);
