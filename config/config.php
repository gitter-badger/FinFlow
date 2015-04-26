<?php
/**
 * FinFlow Configuration file
 * @version  1.2.3
 * @since 0.5
 */

/** URL-ul de baza al aplicatiei */
define('FN_URL', 'http://localhost:8087/live');


/** Setari pentru conexiuna la baza de date, le poti obtine de la furnizorul tau de servicii de gazduire web */
define('FN_DB_HOST'	, 'localhost');
define('FN_DB_USER'	, 'root');
define('FN_DB_PASS'	, 'parola123');
define('FN_DB_NAME'	, 'test');

/** prefixul pentru tabelele din baza de date */
define('FN_DB_PREFIX'	, 'PREFIX_DB');


/** Setari de pentru formatul datelor afisate, vezi http://php.net/manual/en/function.date.php */
define('FN_DATETIME_FORMAT', 'l, jS F Y h:i:s A');

define('FN_DAY_FORMAT'		, 'jS \of F Y');
define('FN_MONTH_FORMAT'	, 'F Y');
define('FN_YEAR_FORMAT'		, 'Y');


/** Numarul de rezultate de afisat pe pagina */
define('FN_RESULTS_PER_PAGE', 15);

/**Numele directorului cache*/
define('FN_CACHE_FOLDER', '/var/www/live/.cache');
define('FN_CACHE_EXPIRE', 7200);

/**Activeaza debuging*/
define('FN_DEBUG', true);