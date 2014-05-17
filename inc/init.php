<?php

define('FN_VERSION'     , '0.9.3');
define('FN_DB_VERSION', '1.0');

define('FNPATH', rtrim(str_replace(basename(dirname(__FILE__)), '', dirname(__FILE__)), DIRECTORY_SEPARATOR));

define('FN_OP_IN'		, 'in');
define('FN_OP_OUT'	, 'out');

define('FN_SERVER_TIMEZONE'	, date_default_timezone_get());
define('FN_MYSQL_DATE'			, 'Y-m-d H:i:s');

define('FN_LOGFILE', (FNPATH . "/application.log"));

define('FN_UPLOADS_DIR', (FNPATH . "/uploads"));
define('FN_BACKUP_DIR'  , (FNPATH . "/uploads/backup"));
define('FN_UPGRADE_DIR', (FNPATH . "/uploads/upgrade"));

error_reporting(E_ALL ^ E_NOTICE);

include_once ( FNPATH . '/inc/interface.exr.php' );

include_once ( FNPATH . '/inc/class.pop3.php' );
include_once ( FNPATH . '/inc/class.mysqlidb.php' );
include_once ( FNPATH . '/inc/class.sqlstatement.php' );
include_once ( FNPATH . '/inc/class.ui.php' );
include_once ( FNPATH . '/inc/class.op.php' );
include_once ( FNPATH . '/inc/class.op-pending.php' );
include_once ( FNPATH . '/inc/class.settings.php' );
include_once ( FNPATH . '/inc/class.validate.php' );
include_once ( FNPATH . '/inc/class.currency.php' );
include_once ( FNPATH . '/inc/class.accounts.php' );
include_once ( FNPATH . '/inc/class.contacts.php' );
include_once ( FNPATH . '/inc/class.label.php' );
include_once ( FNPATH . '/inc/class.util.php' );
include_once ( FNPATH . '/inc/class.user.php' );
include_once ( FNPATH . '/inc/class.log.php' );

@include_once ( FNPATH . '/config.php' );

//--- init debug ---//
if( defined('FN_DEBUG') and FN_DEBUG ) {
    @ini_set('display_errors', 'On'); //TODO add error reporting level @error_reporting(E_ALL);
}
//--- init debug ---//

//--- setup base url ---//
define('FN_URL', fn_Util::get_base_url( false, ( defined('FN_FORCE_HTTPS') and FN_FORCE_HTTPS ) ));
//--- setup base url ---//

//--- setup uploads dir ---//
define('FN_UPLOADS_DIR', ( FNPATH . DIRECTORY_SEPARATOR . 'uploads' ) );
//--- setup uploads dir ---//

//--- check installation status ---//
if( !defined('FN_DB_HOST') and ( strpos($_SERVER['REQUEST_URI'], '/setup') === false ) ){
    header("Location: " . FN_URL . '/setup/install.php');
}
//--- check installation status ---//

$fnsql= new SQLStatement();
$fndb = new MySQLiDB(FN_DB_HOST, FN_DB_USER, FN_DB_PASS, FN_DB_NAME);

if ( !$fndb->connected and defined('FN_DB_HOST') )
    fn_UI::fatal_error("Nu se poate realiza conexiunea cu baza de date pe " . FN_DB_HOST);

//--- setup timezone ---//
define('FN_TIMEZONE', fn_Settings::get('timezone', 'Europe/London')); date_default_timezone_set(FN_TIMEZONE);
//--- setup timezone ---//

if( !defined('FN_IS_CRON') ) @session_start();

$fnPublicPages = array('login', 'pwreset');

if ( !fn_User::is_authenticated() and !in_array($_GET['p'], $fnPublicPages)) $_GET['p'] = 'login';