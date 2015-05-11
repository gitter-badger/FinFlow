<?php
/**
 * FinFlow 1.0 - [file description]
 * @author Adrian S. (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow\Drivers;

final class EmailAgent_Exception extends \FinFlow\FN_Exception {


	const ERR_PROTOCOL_NOT_SUPPORTED = 1024;
	const ERR_CONNECTION_TIMEOUT     = 1032;
	const ERR_CONNECTION_FAILED      = 1040;
	const ERR_INVALID_HOST           = 1048;

	/**
	 * @param string $msg
	 * @param integer $code
	 *
	 * @return EmailAgent_Exception
	 */
	public function __construct( $msg='', $code=null ) {

		$this->__setMessage($msg); parent::__construct($msg, $code);
	}


}