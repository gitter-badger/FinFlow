<?php

//cron states
define('FN_CRON_STATE_RUNNING'		, 'running');
define('FN_CRON_STATE_FINISHED'		, 'finished');
define('FN_CRON_STATE_UNKONWN'	, 'unknown');

class fn_CronAssistant{
	
	public static function may_cron_run($cron){

		$is_running = fn_Settings::get(self::skey($cron), 'unknown');
		
		if( $is_running == FN_CRON_STATE_RUNNING )
			return FALSE;
		
		return TRUE;
		
	}

    public static function is_running_in_window(){
        return ( php_sapi_name() != 'cli' ) and strlen($_SERVER['REMOTE_ADDR']);
    }

    public static function is_cli(){
        return ( php_sapi_name() == 'cli' ) and empty($_SERVER['REMOTE_ADDR']);
    }
	
	
	public static function skey($cron, $prefix=""){
		
		$skey = md5($cron); 
		$skey	= ($prefix . $skey);
		
		return "cron_{$skey}";
	}
	
	public static function set_cron_state($cron, $state="finished"){
		
		if( !in_array($state, array(FN_CRON_STATE_FINISHED, FN_CRON_STATE_RUNNING)) ) 
			die("Invalid state {$state} . Available states are running or finished.");
		
		fn_Settings::set(self::skey($cron), $state);
		
	}
	
	public static function get_cron_state($cron){
		return fn_Settings::get(self::skey($cron), 'unknown');;	
	}
	
	public static function set_lastrun($cron, $lastrun=FALSE){
		
		if ( empty($lastrun) ) $lastrun = time(); 
		
		fn_Settings::set(self::skey($cron, "lastrun_"), intval($lastrun));
		
	}
	
	public static function get_lastrun($cron){
		return fn_Settings::get(self::skey($cron, "lastrun_"), 0);
	}
	
	public static function get_lock($cron, $stop=TRUE){

		if ( self::may_cron_run($cron) ){
			
			self::set_cron_state($cron, FN_CRON_STATE_RUNNING); 
			self::set_lastrun($cron);
			
			return TRUE;
			
		}elseif ($stop){
            if( self::is_running_in_window() )
                fn_UI::fatal_error('Cronul ' . $cron . ' a fost blocat. Se pare ca o alta instanta a aceluiasi fisier ruleaza in acest moment.');
            else
			    die( date(FN_MYSQL_DATE) . " Eroare: Cronul " . $cron . " a fost blocat. Se pare ca o alta instanta a aceluiasi fisier ruleaza in acest moment." );
        }
		
		return FALSE;
		
	}
	
	public static function release_lock($cron){
		self::set_cron_state($cron, FN_CRON_STATE_FINISHED);
	}

    public static function cron_error($msg, $onscreen=false, $fatal=true){

        //TODO remove absolute paths from error messages, show instead relative paths

        if( $onscreen )
            fn_UI::fatal_error($msg, $fatal);
        else
            if($fatal) die("Eroare: " . $msg); else echo ("Eroare: " . $msg); //most servers will send an email with the message
    }

}