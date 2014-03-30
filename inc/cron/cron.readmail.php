<?php

define('INCPATH', rtrim(str_replace(basename(dirname(__FILE__)), "", dirname(__FILE__)), DIRECTORY_SEPARATOR));
define('FN_IS_CRON', 7);

include_once (INCPATH . '/init.php');

include_once (INCPATH . '/class.cronassistant.php');
include_once (INCPATH . '/class.mime_parser.php');
include_once (INCPATH . '/class.rfc822_addresses.php');

$Settings = array(
		'host'			    =>'',
		'port'			    =>110,
		'encryption'	=>'tcp',
		'username'	    =>'anonymous',
		'password'	    =>''
);

$active  = fn_Settings::get('mup_state') == 'active' ? TRUE :FALSE;
$testing = isset($_GET['test']) ? TRUE : FALSE;

if ($active){
	//--- get settings from database ---//
	$Settings = array(
			'host'			=> trim(fn_Settings::get('mup_host')),
			'port'			=> intval(fn_Settings::get('mup_port')),
			'encryption'	=> trim(fn_Settings::get('mup_encription')),
			'username'	=> trim(fn_Settings::get('mup_user')),
			'password'	=> trim(fn_Settings::get('mup_password'))
	);
	//--- get settings from database ---//
	
	$Settings['password'] = fn_Util::s_decrypt($Settings['password'], FN_PW_SALT);
}

if ( $testing )
	//--- override settings with $_POST array ---//
	$Settings = array(
			'host'			     => trim($_POST['mup_host']),
			'port'			     => intval($_POST['mup_port']),
			'encryption'	 => trim($_POST['mup_encription']),
			'username'	     => trim($_POST['mup_user']),
			'password'	     => trim($_POST['mup_password'])
	);
	//--- override settings with $_POST array ---//

//--- validate stuff ---//
if ( empty($Settings['host']) ) 		    fn_CronAssistant::cron_error("Adresa serverului de mail nu este configurat&#259;.", $testing);
if ( empty($Settings['port']) ) 		    fn_CronAssistant::cron_error("Portul de conectare prin protocolul POP nu este configurat.", $testing);
if ( empty($Settings['encryption']) ) fn_CronAssistant::cron_error("Tipul de criptare utilizat de server, nu este configurat.", $testing);
//--- validate stuff ---//

if ( !$active and !$testing and count($_POST) )
    fn_CronAssistant::cron_error("Scriptul este dezactivat!");

if ( !$testing and !$active ) 
	exit(); //silent exit

//--- validate the hostname --//
if ( !fn_CheckValidityOf::hostname($Settings['host']) ) fn_CronAssistant::cron_error("Serverul de mail {$Settings['host']} nu poate fi contactat.", $testing);

try{

    //TODO add IMAP protocol support

	fn_CronAssistant::get_lock(__FILE__);
	
	$connectionTimeout = array( "sec" => 10, "usec" => 500 );

	$MailFetch = new POP3(FN_LOGFILE, TRUE, TRUE, $Settings['encryption'], FALSE);
	
	$MailFetch->connect($Settings['host'], $Settings['port'], $connectionTimeout);
	$MailFetch->login($Settings['username'], $Settings['password'], FALSE);
	
	$ostatus = $MailFetch->getOfficeStatus();
	
	$Parser = new mime_parser_class();
	
	$Parser->mbox = 0;
	
	if ( $testing ){
		fn_CronAssistant::release_lock(__FILE__);
        fn_UI::fatal_error("Scriptul s-a conectat cu succes la serverul {$Settings['host']}.", true, true, 'note', "Not&#259;: ", 'Not&#259;');
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
	
	fn_CronAssistant::release_lock(__FILE__);
	
}
catch( POP3_Exception $e ){
	fn_CronAssistant::release_lock(__FILE__);

    if( strpos( strtolower($e), 'authentication failed' ) === false ) //unknown error
        fn_CronAssistant::cron_error($e, $testing);
    else                                                                                   //authentication error
        fn_CronAssistant::cron_error("Nu se poate realiza autentificarea la {$Settings['host']}. Verfic&#259; numele de utilizator, parola &#351;i tipul de criptare cerute de server &#351;i &#238;ncearc&#259; din nou.", $testing);
}

//---- run the mailupdate cron ---//