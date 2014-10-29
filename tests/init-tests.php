<?php
/**
 * FinFlow 1.0 - Initialize unittests
 * @author Adrian S. (adrian@finflow.org)
 * @version 1.0
 */

define('INCLUDE_PATH', '/var/www/live');

define('FN_ENVIRONMENT'     , 'unittest');
define('FN_IS_CRON'             , 'yes');

define('FN_BYPASS_UI_FATAL_ERRORS' , true);
define('FN_TESTS_SUPPRESS_NOTES' , true);

include_once ( INCLUDE_PATH . '/inc/init.php' );
include_once ( INCLUDE_PATH . '/inc/class.installer.php' );
include_once ( INCLUDE_PATH . '/inc/class.cronassistant.php' );