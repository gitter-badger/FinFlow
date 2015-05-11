<?php

//currently running cron
define('FN_TASK'     , __FILE__);
define('FN_TASK_NAME', 'op_mailparse');

include_once ('task-init.php');

use FinFlow\UI;
use FinFlow\TaskAssistant;
use FinFlow\Util;
use FinFlow\CanValidate;
use FinFlow\Drivers\EmailFetchAgent;
use FinFlow\Drivers\EmailAgent_Exception;

//default settings
$Settings = array(
		'host'			=> 'localhost',
		'protocol'		=> 'imap',
		'port'		    => null,
		'encryption'	=> 'ssl',
		'username'	    => 'anonymous',
		'password'	    => '',
		'validate_cert'	=> true,
);

$Settings = TaskAssistant::get_task_params(FN_TASK_NAME, $Settings);

$active     = TaskAssistant::is_task_active(FN_TASK_NAME);
$testing    = isset($_GET['test']) ? TRUE : FALSE;
$in_browser = TaskAssistant::is_browser();

if ($active){
	$Settings['password'] = Util::decrypt($Settings['password'], FN_CRYPT_SALT);
}

//--- validate stuff ---//
if ( empty($Settings['host']) )
	TaskAssistant::task_error("Adresa serverului de mail nu este configurat&#259;.");

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

	//lock the task
	TaskAssistant::get_lock(FN_TASK_NAME);

	$MailFetch = new EmailFetchAgent($Settings['protocol'], $Settings['host'], $Settings['username'], $Settings['password'], $Settings['encryption'], $Settings['port'], 10, $Settings['validate_cert']);

	
	if ( $testing ){
		TaskAssistant::release_lock(FN_TASK_NAME);
        UI::fatal_error(
	        __t('The task has successfully connected to %s.', $Settings['host']),
	        true, true, UI::MSG_SUCCESS, "Info: ", 'Info'
        );
	}

	$messages = $MailFetch->getMessages();

	print_r($messages);

	die();
	
	for($i= 1; $i<=$unread; $i++ ){
		
		$message = $MailFetch->getMsg($i); 
		
		$params    = array('Data'=>$message);
		$decoded  = array();
		
		$Parser->Decode($params, $decoded);
		
		if ( count($decoded) ){
			
			$mail = $decoded[0];
			
			$subject   = $mail['Headers']['subject:'];
            $timestamp = @strtotime($mail['Headers']['delivery-date:']);
			$body	   = $mail['Body'];
			
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

    if( TaskAssistant::is_browser() )
        UI::fatal_error("Tranzac&#355;iile trimise pe email au fost actualizate.", false, true, 'note', "Not&#259;: ", 'Not&#259;');

	TaskAssistant::release_lock(FN_TASK_NAME);
	
}
catch( EmailAgent_Exception $e ){

	TaskAssistant::release_lock(FN_TASK_NAME);
	TaskAssistant::task_error( $e->getMessage() );

}

//---- run the mailupdate cron ---//