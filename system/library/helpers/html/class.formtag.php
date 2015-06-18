<?php
/**
 * FinFlow 1.0 - [file description]
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow\Helpers\HTML;

class Form{

	public static function __callStatic($name, $arguments){
		//TODO...
	}

	public static function generate($what, $args=array()){
		if( method_exists(self, $what) ){
			return call_user_func_array('self::' . $what, array($args));
		}

	}

	public static function optypesTag($args=array()){
		//TODO
	}

	public static function currenciesTag($args=false){
		//TODO...
	}

	public static function labelsTag($args=true){
		//TODO...
	}

	public static function accountsTag($args=false){
		//TODO...
	}

	public static function usersTag($args=array()){
		//TODO
	}

	public static function contactsTag($args=array()){
		//TODO...
	}
}