<?php

//currently running cron
define('FN_TASK'     , __FILE__);
define('FN_TASK_NAME', 'op_mailparse');

include_once ('task-init.php');

use FinFlow\Settings;
use FinFlow\TaskAssistant;
use FinFlow\Util;
use FinFlow\CanValidate;

//default settings
$Settings = array(
		'host'			=> '',
		'protocol'		=> 'pop3',
		'port'			=> 110,
		'encryption'	=> 'tcp',
		'username'	    => 'anonymous',
		'password'	    => ''
);

$Settings = TaskAssistant::get_task_params(FN_TASK_NAME, $Settings);

$active     = TaskAssistant::is_task_active(FN_TASK_NAME);
$testing    = isset($_GET['test']) ? TRUE : FALSE;
$in_browser = TaskAssistant::is_browser();

if ($active){
	$Settings['password'] = Util::s_decrypt($Settings['password'], FN_PW_SALT);
}

//--- validate stuff ---//
if ( empty($Settings['host']) )
	TaskAssistant::task_error("Adresa serverului de mail nu este configurat&#259;.");

if ( empty($Settings['port']) )
	TaskAssistant::task_error("Portul de conectare prin protocolul POP nu este configurat.");

if ( empty($Settings['encryption']) )
	TaskAssistant::task_error("Tipul de criptare utilizat de server, nu este configurat.");

//--- validate stuff ---//

if ( !$active and !$testing )
    TaskAssistant::task_error("The task <em>%1s</em> is deactivated!", array(FN_TASK_NAME));

if ( !$testing and !$active ) 
	exit(); //silent exit

//--- validate the hostname --//
if ( ! CanValidate::hostname($Settings['host']) )
	TaskAssistant::task_error("Serverul de mail {$Settings['host']} nu poate fi contactat.");
//--- validate the hostname --//

try{

    //TODO! add IMAP protocol support

	//lock the task
	TaskAssistant::get_lock(FN_TASK);
	
	$connectionTimeout = array( "sec" => 10, "usec" => 500 );

	$MailFetch = new POP3(FN_LOGFILE, TRUE, TRUE, $Settings['encryption'], FALSE);
	
	$MailFetch->connect($Settings['host'], $Settings['port'], $connectionTimeout);
	$MailFetch->login($Settings['username'], $Settings['password'], FALSE);
	
	$ostatus = $MailFetch->getOfficeStatus();
	
	$Parser = new mime_parser_class();
	
	$Parser->mbox = 0;
	
	if ( $testing ){
		TaskAssistant::release_lock(__FILE__);
        UI::fatal_error("Scriptul s-a conectat cu succes la serverul {$Settings['host']}.", true, true, 'note', "Not&#259;: ", 'Not&#259;');
	}
	
	$unread = intval($ostatus['count']);
	
	for($i= 1; $i<=$unread; $i++ ){
		
		$message = $MailFetch->getMsg($i); 
		
		$params    = array('Data'=>$message);
		$decoded  = array();
		
		$Parser->Decode($params, $decoded);
		
		if ( count($decoded) ){
			
			$mail = $decoded[0];
			
			$subject     = $mail['Headers']['subject:'];
            $timestamp= @strtotime($mail['Headers']['delivery-date:']);
			$body		= $mail['Body'];
			
			if ( isset($mail['Parts'][0]['Body']) ) $body = $mail['Parts'][0]['Body'];
			
			$vars = fn_OP::extract_op_vars($subject);

			if ( $vars === FALSE ); else{

                if( !isset($vars['time']) or empty($vars['time']) ) $vars['time'] = $timestamp;

				if ( strlen($body) ) $metadata = array('details'=>$body);

				fn_OP::create($vars['optype'], $vars['value'], $vars['ccode'], $vars['account'], $vars['labels'], $vars['time'], $metadata);
				
				$MailFetch->deleteMsg($i);
				
			}
			
		}
		
		
	}
	
	$MailFetch->quit(); //disconnect and delete marked messages

    if( $in_window )
        fn_UI::fatal_error("Tranzac&#355;iile trimise pe email au fost actualizate.", false, true, 'note', "Not&#259;: ", 'Not&#259;');

	fn_CronAssistant::release_lock(__FILE__);
	
}
catch( POP3_Exception $e ){
	fn_CronAssistant::release_lock(__FILE__);

    if( strpos( strtolower($e), 'authentication failed' ) === false ) //unknown error
        fn_CronAssistant::cron_error($e, ($testing or $in_window));
    else                                                                                   //authentication error
        fn_CronAssistant::cron_error("Nu se poate realiza autentificarea la {$Settings['host']}. Verfic&#259; numele de utilizator, parola &#351;i tipul de criptare cerute de server &#351;i &#238;ncearc&#259; din nou.", ($testing or $in_window));
}

//---- run the mailupdate cron ---//