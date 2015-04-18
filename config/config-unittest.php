<?php
/**
 * FinFlow Configuration file
 * @version  1.2.3
 * @since 0.5
 */

/** URL-ul de baza al aplicatiei */
define('FN_URL', 'localhost');

/** Setari pentru conexiuna la baza de date, le poti obtine de la furnizorul tau de servicii de gazduire web */
define('FN_DB_HOST'		, 'localhost');
define('FN_DB_USER'		, 'root');
define('FN_DB_PASS'		, 'parola123');
define('FN_DB_NAME'	    , 'unittest');

/** prefixul pentru tabelele din baza de date */
define('FN_DB_PREFIX'	, 'fn_');

/** String folosit ca salt pentru criptarea parolelor, poti genera unul la http://tinyurl.com/SecureRandomString7 */
define('FN_PW_SALT'	, 'hpovZ2q0hOmV5Gx0');


/** Setari de pentru formatul datelor afisate, vezi http://php.net/manual/en/function.date.php */
define('FN_DATETIME_FORMAT', 'l, jS F Y h:i:s A');

define('FN_DAY_FORMAT'		    , 'jS \of F Y');
define('FN_MONTH_FORMAT'	, 'F Y');
define('FN_YEAR_FORMAT'		, 'Y');


/** Numarul de rezultate de afisat pe pagina */
define('FN_RESULTS_PER_PAGE', 15);

/**Numele directorului cache*/
define('FN_CACHE_FOLDER', '/var/www/live/.cache');
define('FN_CACHE_EXPIRE', 7200);

/**Activeaza debuging*/
define('FN_DEBUG', true);