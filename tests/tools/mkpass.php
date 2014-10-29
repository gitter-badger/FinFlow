<?php
/**
 * Creates a password for the desired e-mail address
 */

include_once '../../inc/init.php';

if( isset($_GET['email']) and isset($_GET['pass']) ){
    $epass  = fn_User::crypt_password($_GET['email'], $_GET['pass'], FN_PW_SALT);
    $output = ( 'Email: ' . $_GET['email'] . ' <br/>Password: ' . $_GET['pass'] . '<br/>Encrypted Password: <em>' . $epass . '</em>' );

    fn_UI::fatal_error($output, true, true, fn_UI::$MSG_NOTE, '');

}

else fn_UI::fatal_error("mkpass.php?email=you@example.com&pass=123456", true, true, fn_UI::$MSG_NOTE, 'Usage: ', 'MKPASS');