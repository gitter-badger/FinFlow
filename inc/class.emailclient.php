<?php
/**
 * FinFlow 1.0 - Email Reader class
 * @author Adrian S. (http://adrian.silimon.eu/)
 * @version 1.0
 */

class fn_MailClient{

    protected $is_imap = false;
    protected $is_pop3 = false;

    protected $connection = null;

    public function connect($host, $username, $password='', $protocol='imap', $port='995', $is_ssl=true){
        //TODO ...
    }

    protected function connect_pop3(){
        //TODO!
    }

    protected function connect_imap(){
        //TODO!
    }

    public function read(){
        //TODO
    }

}