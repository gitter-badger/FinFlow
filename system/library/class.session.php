<?php
/**
 * FinFlow 1.0 - Better PHP Session support
 * @author Adrian S. (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow;

$Session = false;

class Session{

	const COOKIE = 'fn_session';
	const ENCRYPT= true;

	const DRIVER_DEFAULT = 'apc';

	const DRIVER_PHP        = 'php';
	const DRIVER_APC        = 'apc';
	const DRIVER_MEMCACHED  = 'memcached';

	//TODO implement session class

	public function __construct($driver, $config=array()){
		//TODO...
	}

	public function start(){
		global $Session; if( empty($Session) ) $Session = new Session(self::DRIVER_DEFAULT);
	}

	public function restart(){
		//TODO...
	}

	public static function getCurrentSession($autostart=true){
		//TODO...
	}

	public static function update(){
		//TODO...
	}

	public static function store($key, $value=1){
		//TODO...
	}

	public static function fetch($key){
		//TODO...
	}

	public static function destroy(){
		//TODO...
	}

	public static function create(){

	}

}