<?php
/**
 * FinFlow 1.0 - Ajax Gateway Class
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow;

//TODO implement ajax gateway helper
class Ajax{

	public static function output($data){

		$json = json_encode($data);

		echo $json;
	}

	public static function error($message, $status=400){

		$data = array(
			'message'=>strval($message)
		);

		if( function_exists('UI::header_' . $status) )
			call_user_func('UI::header_' . $status);
		else
			throw new \InvalidArgumentException("Status header {$status} cannot be set.");

		self::output($data);
	}

	public static function getOpTypes(){
		$types = OP::getTypesArray(); self::output($types);
	}

}