<?php
/**
 * Creates a password for the desired e-mail address
 */

define('FN_IS_CRON', true);
define('IS_CRON', true);

include_once '../../system/init.php';

use FinFlow\UI;
use FinFlow\User;

if( isset($_GET['email']) and isset($_GET['pass']) ){
    $epass  = User::hash_password($_GET['pass']);
    $output = (
	    '<strong>Email:</strong> ' . $_GET['email'] . '
	    <br/><strong>Password:</strong> ' . $_GET['pass'] . '
	    <br/><strong>Encrypted Password:</strong> <em>' . $epass . '</em>'
    );

    UI::fatal_error($output, true, true, UI::MSG_NOTE, '');

}

else
	UI::fatal_error("mkpass.php?email=you@example.com&pass=123456", true, true, UI::MSG_NOTE, 'Usage: ', 'MKPASS');