<?php
/**
 * FinFlow 1.0 - [file description]
 * @author Adrian S. (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow;

/**
 * Class FN_Exception
 * @package FinFlow
 */
class FN_Exception extends \Exception{

	protected $class_name;
	protected $function_name;

	protected $__message;

	public function __construct($message, $code=null, $class=null, $function=null){

		$this->class_name    = $class;
		$this->function_name = $function;

		parent::__construct($message, $code);

	}

	public function getClass(){
		return $this->class_name;
	}

	public function getFunction(){
		return $this->function_name;
	}

	public function __setMessage($msg){
		$this->__message = $msg;
	}

	public function __getMessage(){
		return $this->__message;
	}

}