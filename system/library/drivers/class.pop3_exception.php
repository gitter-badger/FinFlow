<?php
/**
 * FinFlow 1.0 - [file description]
 * @author Adrian S. (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow\Drivers;

final class POP3_Exception extends \Exception {
	/**
	 * @param string $strErrMessage
	 * @param integer $intErrCode
	 *
	 * @return POP3_Exception
	 */
	function __construct( $strErrMessage, $intErrCode )
	{
		switch( $intErrCode )
		{
			case POP3::ERR_NOT_IMPLEMENTS:
				if( empty($strErrMessage) ) $strErrMessage = "This function isn't implements at time.";
				break;

			case POP3::ERR_SOCKETS:
				$strErrMessage = "Sockets Error: (". socket_last_error() .") -- ". socket_strerror(socket_last_error());
				break;

			case POP3::ERR_STREAM:
			case POP3::ERR_LOG:
				$aError = error_get_last();
				$strErrMessage = "Stream Error: (". $aError["type"] .") -- ". $aError["message"];
				break;
		}
		parent::__construct($strErrMessage, $intErrCode);
	}


	/**
	 * Store the Exception string to a given file
	 *
	 * @param string $strLogFile  logfile name with path
	 */
	public function saveToFile($strLogFile)
	{
		if( !$resFp = @fopen($strLogFile,"a+") )
		{
			return false;
		}
		$strMsg = date("Y-m-d H:i:s -- ") . $this;
		if( !@fputs($resFp, $strMsg, strlen($strMsg)) )
		{
			return false;
		}
		@fclose($resFp);
	}
	/**
	 * @return string Exception with StackTrace as String
	 */
	public function __toString()
	{
		return __CLASS__ ." [". $this->getCode() ."] -- ". $this->getMessage() ." in file ". $this->getFile() ." at line ". $this->getLine(). PHP_EOL ."Trace: ". $this->getTraceAsString() .PHP_EOL;
	}

}