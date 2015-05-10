<?php
/**
 * FinFlow 1.0 - [file description]
 * @author Adrian S. (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow\Drivers;

final class IMAP_Exception extends \FinFlow\FN_Exception {


	const ERR_PROTOCOL_NOT_SUPPORTED = 0;
	const ERR_CONNECTION_TIMEOUT     = 2;

	/**
	 * @param string $msg
	 * @param integer $code
	 *
	 * @return IMAP_Exception
	 */
	public function __construct( $msg='', $code ) {
		parent::__construct($msg, $code);
	}


}