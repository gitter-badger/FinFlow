<?php
/**
 * FinFlow 1.0 - Email Reader class
 * @author Adrian S. (http://adrian.silimon.eu/)
 * @version 1.0
 */

$imapClient = null;
$pop3Client = null;

class fn_MailClient{

    public static function connect($host, $username, $password='', $protocol='imap', $port='995', $is_ssl=true){

        //TODO

    }

    public static function connect_pop3(){
        //TODO!
    }

    public static function connect_imap(){
        //TODO!
    }

}