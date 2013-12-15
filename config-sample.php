<?php
/**
 * FinFlow Configuration file
 * @version 1.2
 * @since 0.5
 */


/** Setari pentru conexiuna la baza de date, le poti obtine de la furnizorul tau de servicii de gazduire web */
define('FN_DB_HOST'		, 'localhost');
define('FN_DB_USER'		, 'UTILIZATOR_BD');
define('FN_DB_PASS'		, 'PAROLA_BD');
define('FN_DB_NAME'	    , 'NUME_BD');

/** String folosit ca salt pentru criptarea parolelor, poti genera unul la http://tinyurl.com/RandomString7 */
define('FN_PW_SALT'	, 'CUVANT_SECRET');


/** Setari de pentru formatul datelor afisate, vezi http://php.net/manual/en/function.date.php */
define('FN_DATETIME_FORMAT', 'l, jS F Y h:i:s A');

define('FN_DAY_FORMAT'		    , 'jS \of F Y');
define('FN_MONTH_FORMAT'	, 'F Y');
define('FN_YEAR_FORMAT'		, 'Y');


/** Numarul de rezultate de afisat pe pagina */
define('FN_RESULTS_PER_PAGE', 15);


/** Setari pentru actualizarea cursului valutar, acestea sunt singurele valori compatibile cu aceasta versiune */
define('FN_EXCHANGERATES_XML_URL'		 , 'http://www.bnr.ro/nbrfxrates.xml');
define('FN_EXCHANGERATES_XML_PARSER', 'bnr');