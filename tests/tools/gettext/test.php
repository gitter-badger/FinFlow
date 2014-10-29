<?php
/**
 * FinFlow 1.0 - [file description]
 * @author Adrian S. (http://www.gridwave.co.uk/)
 * @version 1.0
 */

// define constants
define('PROJECT_DIR',   realpath('./'));
define('LOCALE_DIR'   , PROJECT_DIR .'/locale'); //the locale dir can be set to anything else
define('DEFAULT_LOCALE', 'en_US');

include_once 'gettext.ini.php';

$supported_locales = array('en_US', 'sr_CS', 'de_CH');
$encoding = 'UTF-8';

$locale = (isset($_GET['lang']))? $_GET['lang'] : DEFAULT_LOCALE;

// gettext setup
T_setlocale(LC_MESSAGES, $locale);
// Set the text domain as 'messages'
$domain = 'messages'; //here you add your domain name (e.g. finflow.po) locale
T_bindtextdomain($domain, LOCALE_DIR);
T_bind_textdomain_codeset($domain, $encoding);
T_textdomain($domain);

