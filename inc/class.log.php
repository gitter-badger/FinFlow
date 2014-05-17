<?php

$fn_log_messages = array();

class fn_Log{

    public static function to_file($msg, $prefix="MESSAGE: ", $sizelimit=1){ //TODO add sizelimit support

        if( defined('FN_DEBUG') and !FN_DEBUG ) return false; //debug deactivated

        if( defined('FN_LOGFILE')  ){

            if( @file_exists(FN_LOGFILE) ); else @file_put_contents(FN_LOGFILE, 'Jurnal FinFlow -' . FN_VERSION . "\n\n", FILE_APPEND);

            $now               = date(FN_MYSQL_DATE);
            $sizelimitBytes = ($sizelimit * 1024 * 1024);
            $filesize          = intval( @filesize(FN_LOGFILE) );
            $logfile            = @fopen(FN_LOGFILE, 'a');

            if( $logfile ){

                if( $filesize > $sizelimitBytes ) @ftruncate($logfile, $sizelimitBytes);

                @fwrite($logfile,  "{$now} {$prefix} {$msg}\n");  @fclose($logfile);

                return true;

            }

            return false;

        }
        else fn_UI::fatal_error("Constanta FN_LOGFILE nu este definita!");
    }

    public static function to_screen($msg, $prefix="MESSAGE: "){

        global $fn_log_messages;

        if( defined('FN_DEBUG') and !FN_DEBUG ) return false; //debug deactivated

        $now = date(FN_MYSQL_DATE);

        $fn_log_messages[] = ("{$now} {$prefix} {$msg}\n");

    }

    public static function display($limit=125){

        global $fn_log_messages;

        if( empty($fn_log_messages) ) return;

       $messages = array_slice ($fn_log_messages, 0 , $limit);

        echo('<div class="fn-log-messages"><div class="pre-wrap"><pre>');

        if( count($messages) ) foreach($messages as $message){
            echo($message);
        }

        echo('</pre></div></div>');

    }

}