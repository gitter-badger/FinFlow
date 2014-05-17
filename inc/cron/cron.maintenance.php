<?php
/**
 * FinFlow 1.0 - Maintenance Cron Job: sends emails, removes expired cache files
 * @author Adrian S. (http://www.gridwave.co.uk/)
 * @version 1.0
 */

define('INCPATH', rtrim(str_replace(basename(dirname(__FILE__)), "", dirname(__FILE__)), DIRECTORY_SEPARATOR));
define('FN_IS_CRON', 7);

include_once (INCPATH . '/init.php');
include_once (INCPATH . '/class.cronassistant.php');

$in_window = ( ( php_sapi_name() != 'cli' ) and strlen($_SERVER['REMOTE_ADDR']) );

fn_CronAssistant::get_lock(__FILE__); //lock the cron instance

//TODO all of the stuff

fn_CronAssistant::release_lock(__FILE__);