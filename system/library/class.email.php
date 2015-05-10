<?php
/**
 * FinFlow 1.0 - [file description]
 * @author Adrian S. (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow;

use FinFlow\Drivers\POP3;

class MailReader{

	const IMAP = 'imap';
	const POP3 = 'pop3';

	public static $defaultPorts = array(
		'pop3'      => 110,
		'pop3-ssl'  => 995,
		'pop3-tls'  => 995,
		'imap'      => 143,
		'imap-ssl'  => 993,
		'imap-tls'  => 993,
	);

	protected $protocol;
	protected $host;
	protected $encryption;

	protected $config = array(
		'host'			=> 'localhost',
		'protocol'		=> 'imap',
		'port'			=> 993,
		'encryption'	=> 'ssl',
		'username'	    => 'anonymous',
		'password'	    => ''
	);

	protected $pop3 = false;
	protected $imap = false;

	public function __construct($cfg=array(), $autoconnect=true){

		$this->config = array_merge($this->config, $cfg);

		if( $autoconnect )

			$this->connect(
				$this->config['host'],
				$this->config['protocol'],
				$this->config['username'],
				$this->config['password'],
				$this->config['encryption'],
				$this->config['port']
			);

	}

	public function connect($host, $protocol='imap', $username='anonymous', $password='', $encryption='tls', $port=null){

		$protocol = strtolower($protocol);

		if( empty($port) )
			$port = self::$defaultPorts[ $protocol . '-' . $encryption];

		switch($protocol){

			case self::IMAP: {

				//TODO imap class

			} break;

			case self::POP3: {

				$this->pop3 = new POP3(FN_LOGFILE, true, true, $encryption);

				$this->pop3->connect($host, $port, array('sec'=>10, 'usec'=>500));
				$this->pop3->login($username, $password);

				return true;

			} break;

			case self::IMAP: {

				$this->imap = new imap_

				//TODO pop3 class

			} break;

			default:
				throw new FN_Exception( __t('%s protocol is not supported!', $protocol) );
		}

		return false;

	}

	public function getUnreadMessage($index){
		//TODO...
	}

	public function isEncrypted(){
		//TODO...
	}

}