<?php

//currently running cron
define('FN_TASK'     , __FILE__);
define('FN_TASK_NAME', 'op_mailparse');

include_once ('task-init.php');

use FinFlow\UI;
use FinFlow\Log;
use FinFlow\TaskAssistant;
use FinFlow\Util;
use FinFlow\OP;
use FinFlow\Contacts;
use FinFlow\User;
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
		'timeout'	    => 10,
		'validate_cert'	=> true,

		'default_op_user_id' => 1,
		'restrict_from'      => '',
);

$Settings = TaskAssistant::get_task_params(FN_TASK_NAME, $Settings);

$active     = TaskAssistant::is_task_active(FN_TASK_NAME);
$testing    = isset($_GET['test']) ? TRUE : FALSE;
$in_browser = TaskAssistant::is_browser();

if ( $active and strlen($Settings['password']) ){
	$Settings['password'] = Util::decrypt($Settings['password'], FN_CRYPT_SALT);
}

//--- validate stuff ---//
if ( empty($Settings['host']) )
	TaskAssistant::task_error( __t('Mail server address is missing.') );

//--- validate stuff ---//

if ( ! $active and ! $testing )
    TaskAssistant::task_error("The task <em>%1s</em> is deactivated!", array(FN_TASK_NAME));

if ( ! $testing and ! $active )
	exit(); //silent exit

//--- validate the hostname --//
if ( ! CanValidate::hostname($Settings['host']) )
	TaskAssistant::task_error(
		__t("The email server at %s cannot be reached. Please check your server's host/address settings and try again.", $Settings['host'])
	);
//--- validate the hostname --//

try{

	//lock the task
	TaskAssistant::get_lock(FN_TASK_NAME);

	$MailFetch = new EmailFetchAgent($Settings['protocol'], $Settings['host'], $Settings['username'], $Settings['password'], $Settings['encryption'], intval($Settings['port']), intval($Settings['timeout']), intval($Settings['validate_cert']));

	if ( $testing ){
		TaskAssistant::release_lock(FN_TASK_NAME);
        UI::fatal_error(
	        __t('%1s has successfully connected to <em>%2s</em>.', FN_TASK_NAME, $Settings['host']),
	        true, true, UI::MSG_SUCCESS, "Info: ", 'Info'
        );
	}

	$messages = $MailFetch->getMessages(true);
	$processed= 0;
	$created  = 0;

	if( count($messages) ) foreach($messages as $message){

		$processed++;

		$message_id = $message['uid'];

		$subject = trim( $message['subject'] );
		$from    = trim( $message['from'] );
		$time    = ( $t = @strtotime( $message['date'] ) ) ? $t : time();
		$contents= trim($message['body']);

		$metadata = array();

		$contact_id = null;
		$user_id    = null;

		$vars  = OP::extract_op_vars($subject);
		$saved = false;

		if( $vars and count($vars) and isset($vars['optype']) and isset($vars['value']) ){

			//--- determine transaction time ---//
			if( empty($vars['time']) )
				$vars['time'] = $time;
			//--- determine transaction time ---//

			//--- determine contact id ---//
			if( $message['from'] ){

				if( is_array($message['from']) )
					$message['from'] = trim( strval( $message['from'][0] ) );

				$contact = null;

				$from = @explode(' ', $message['from']);
				$from = strtolower( array_pop($from) );

				if( strlen($Settings['restrict_from']) ){

					$restrict = @explode(',', trim(strtolower($Settings['restrict_from']), ','));

					if( count($restrict) and in_array($from, $restrict) ){
						$contact = Contacts::get_by($from, 'email');
					}

				}
				else
					$contact = Contacts::get_by($from, 'email');

				if( $contact )
					$contact_id = $contact->contact_id;

			}
			//--- determine contact id ---//


			//--- determine user id ---//
			if( $message['to'] ){


				if( is_array($message['to']) )
					$message['to'] = trim( strval( $message['to'][0] ) );

				$to = @explode(' ', $message['to']);
				$to = array_pop($to);

				$user = User::get_by($to, 'email');

				if( $user )
					$user_id = $user->user_id;

			}

			if( empty($user_id) ){
				$user_id = $Settings['default_op_user_id'];
			}
			//--- determine user id ---//

			//--- add metadata ---//
			$metadata['email_body'] = strip_tags( $contents );
			//--- add metadata ---//

			$saved = OP::create($vars['optype'], $vars['value'], $vars['ccode'], $vars['time'], $vars['account'], $vars['labels'], $user_id, $contact_id, $contents, $metadata);

			if( ! $saved ){
				Log::info(FN_TASK_NAME . ' Failed to parse email transaction type=' . $vars['optype'] . ' ccode=' . $vars['ccode']);
			}
		}

		if( $saved ){
			$created++; $saved = false; $MailFetch->deleteMessage($message_id);
		}

		//TODO parse attachments

	}

	$MailFetch->close();

    if( TaskAssistant::is_browser() ){

	    $msg = __t('Mailbox transactions have been updated. ');
	    $msg.= __t('Emails processed %s. ', $processed);
	    $msg.= __t('Transactions added %s.', $created);

	    UI::fatal_error($msg, false, true, UI::MSG_NOTE, 'Info: ', 'Info');
    }

	TaskAssistant::release_lock(FN_TASK_NAME);
	
}
catch( EmailAgent_Exception $e ){

	TaskAssistant::release_lock(FN_TASK_NAME);
	TaskAssistant::task_error( $e->getMessage() );

}

//---- run the mailupdate cron ---//