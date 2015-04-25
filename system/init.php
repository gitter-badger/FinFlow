<?php

define('FN_VERSION'   , '0.7.1');
define('FN_DB_VERSION', '1');

define('FNPATH', rtrim(str_replace(basename(dirname(__FILE__)), '', dirname(__FILE__)), DIRECTORY_SEPARATOR));

define('FN_OP_IN'	, 'in');
define('FN_OP_OUT'	, 'out');

define('FN_SERVER_TIMEZONE'	, date_default_timezone_get());
define('FN_MYSQL_DATE'		, 'Y-m-d H:i:s');

define('FN_LOGFILE', (FNPATH . "/application.log"));

require_once ( FNPATH . '/system/library/autoload.php');
require_once ( FNPATH . '/system/thirdparty/autoload.php');
require_once ( FNPATH . '/system/library/helpers.php');

use FinFlow\UI;
use FinFlow\Util;
use FinFlow\MySQLiDB;
use FinFlow\SQLStatement;
use FinFlow\Settings;

//--- set environment ---//

if( file_exists( FNPATH . '/environment.php' ) )
	@include_once ( FNPATH . '/environment.php' );

if( file_exists( FNPATH . '/config/environment.php' ) )
	@include_once ( FNPATH . '/config/environment.php' );

if( !defined('FN_ENVIRONMENT') ) define('FN_ENVIRONMENT', ( isset($_SERVER['APP_ENV']) ? $_SERVER['APP_ENV'] : 'production' ) );

//--- set environment ---//

//--- include config ---//
$cfg_path = Util::cfg_file_path(); @include_once ( $cfg_path );
//--- include config ---//

//--- init debug ---//
if( ( defined('FN_DEBUG') and FN_DEBUG ) or Util::is_development_environment() ) {
    @ini_set('display_errors', 'On'); @error_reporting(E_ALL);
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
	UI::fatal_error("Fisierul de configurare {$cfg_path} nu poate fi incarcat.");
}

//--- phptestunit globals workaround ---//
$GLOBALS['fndb']  = $fndb;
$GLOBALS['fnsql'] = $fnsql;
//--- phptestunit globals workaround ---//

if ( $fndb and ! $fndb->connected )
    UI::fatal_error('Nu se poate realiza conexiunea cu baza de date pe ' . FN_DB_HOST);

if ( Settings::get('db_version', 0) == 0 ) //database not installed
	UI::fatal_error('Baza de date nu a fost instalata pe ' . FN_DB_HOST . ' ...');

//--- setup timezone ---//
define('FN_TIMEZONE', Settings::get('timezone', 'Europe/London') ); date_default_timezone_set(FN_TIMEZONE);
//--- setup timezone ---//

//--- setup language ---//
//TODO
//--- setup language ---//

//--- setup session ---//
//TODO
//--- setup session ---//

$_SERVER['REQUEST_URI'] = str_replace(Util::get_base_url(), '', UI::current_page_url());

include_once (FNPATH . '/system/routes/web.php');