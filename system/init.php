<?php

/**
 * Application version
 */
define('FN_VERSION'   , '0.7.2');

/**
 * Corresponding database version
 */
define('FN_DB_VERSION', '1');

/**
 * Application path on disk
 */
define('FNPATH', rtrim(str_replace(basename(dirname(__FILE__)), '', dirname(__FILE__)), DIRECTORY_SEPARATOR));

define('FN_SERVER_TIMEZONE'	, date_default_timezone_get());
define('FN_LOGFILE'         , ( FNPATH . '/logs/fn-' . date('Y-m-d') . '.log') );

require_once ( FNPATH . '/system/library/constants.php');
require_once ( FNPATH . '/system/library/autoload.php');
require_once ( FNPATH . '/system/thirdparty/autoload.php');
require_once ( FNPATH . '/system/library/helpers.php');

use FinFlow\UI;
use FinFlow\Session;
use FinFlow\Log;
use FinFlow\Util;
use FinFlow\MySQLiDB;
use FinFlow\SQLStatement;
use FinFlow\Settings;

//--- set environment ---//

if( file_exists( FNPATH . '/environment.php' ) )
	@include_once ( FNPATH . '/environment.php' );

if( file_exists( FNPATH . '/config/environment.php' ) )
	@include_once ( FNPATH . '/config/environment.php' );

if( !defined('FN_ENVIRONMENT') )
	define('FN_ENVIRONMENT', ( isset($_SERVER['APP_ENV']) ? $_SERVER['APP_ENV'] : 'production' ) );

//--- set environment ---//

//--- include config ---//
$cfg_path = Util::cfg_file_path(); @include_once ( $cfg_path );
//--- include config ---//

//--- init debug ---//
if( ( defined('FN_DEBUG') and FN_DEBUG ) or Util::is_development_environment() ) {

    @ini_set('display_errors', 'On');
	@error_reporting(E_ALL);

	Log::init();
	Log::addHandler( new Monolog\Handler\BrowserConsoleHandler() );
	Log::setLevel(Log::LEVEL_DEBUG);

}
//--- init debug ---//

//--- setup base url (in case not set yet) ---//
if( !defined('FN_URL') ){

    if( defined('FN_IS_INSTALLING') )
        define('FN_URL', Util::get_base_url( false, false, '/setup/'));
    else
        define('FN_URL', Util::get_base_url( false, ( defined('FN_FORCE_HTTPS') and FN_FORCE_HTTPS ) ));

}
//--- setup base url ---//

//--- cron/task constants --//
if( ! defined('FN_IS_CRON') or ! defined('IS_CRON') ){
	define('FN_IS_CRON', false);
	define('IS_CRON'   , false);
}
//--- cron/task constants --//

//--- setup uploads dir ---//
define('FN_UPLOADS_DIR'   , ( FNPATH . '/uploads' ) );
define('FN_DOWNLOADS_DIR' , ( FNPATH . '/downloads') );
//--- setup uploads dir ---//

//--- check installation status ---//
/*
if( !defined('FN_DB_HOST') and ( strpos($_SERVER['REQUEST_URI'], '/setup') === false ) ){
    header("Location: " . FN_URL . '/setup/install.php'); //TODO add setup check
}
*/
//--- check installation status ---//

$fnsql = $fndb = null;

if( defined('FN_DB_HOST') ){

	$fnsql = new SQLStatement();
	$fndb  = new MySQLiDB(FN_DB_HOST, FN_DB_USER, FN_DB_PASS, FN_DB_NAME);

}
else{
	UI::fatal_error( __t( "Can't load the %s configuration file.", $cfg_path ) );
}

//--- phptestunit globals workaround ---//
$GLOBALS['fndb']  = $fndb;
$GLOBALS['fnsql'] = $fnsql;
//--- phptestunit globals workaround ---//

if ( $fndb and ! $fndb->connected )
    UI::fatal_error( __t( "Can't connect to database on %s ", FN_DB_HOST ) );

if ( Settings::get('db_version', 0) == 0 ) //database not installed
	UI::fatal_error( __t( 'The database has not yet been installed on %s ... .', FN_DB_HOST ) );

//--- setup timezone ---//
define('FN_TIMEZONE', Settings::get('timezone', 'Europe/London') ); date_default_timezone_set(FN_TIMEZONE);
//--- setup timezone ---//

//--- setup language ---//
//TODO
//--- setup language ---//

//--- setup session ---//
//TODO
if( ! FN_IS_CRON )
	Session::start(Session::DRIVER_PHP);
//--- setup session ---//

//--- setup headers ---//

if( Util::is_production_environment() ){

	header('X-Frame-Options: SAMEORIGIN', true);

}

$_SERVER['REQUEST_URI'] = str_replace(Util::get_base_url(), '', UI::current_page_url());

if( ! FN_IS_CRON )
	include_once ( FNPATH . '/system/routes/web.php' );