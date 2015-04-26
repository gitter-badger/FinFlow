<?php
/**
 * FinFlow 1.0 - Better PHP Session support
 * @author Adrian S. (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow;

class Session{

	const DRIVER_DEFAULT = 'apc';

	const DRIVER_PHP        = 'php';
	const DRIVER_APC        = 'apc';
	const DRIVER_MEMCACHED  = 'memcached';

	//TODO implement session class

	public function __construct($driver, $config=array()){
		//TODO...
	}

	public static function getCurrentSession($autostart=true){
		//TODO...
	}

}