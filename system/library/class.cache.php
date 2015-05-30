<?php
/**
 * FinFlow 1.0 - [file description]
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow;

use FinFlow\Drivers\FileCache;
use FinFlow\Drivers\PHPSessionCache;

class Cache{

	const ENGINE_PHP        = 1;
	const ENGINE_FILE       = 2;
	const ENGINE_APC        = 3;
	const ENGINE_MEMCACHED  = 4;

	public static function getSupportedEngines(){
		return array(
			self::ENGINE_PHP        => 'PHP Session',
			self::ENGINE_FILE       => 'File',
			self::ENGINE_APC        => 'APC Cache',
			self::ENGINE_MEMCACHED  => 'Memcached',
		);
	}


	public function fire($engine, $config=array()){

		if( array_key_exists($engine, self::getSupportedEngines()) ){

			switch($engine){

				case self::ENGINE_PHP :
					return new PHPSessionCache($config);

				case self::ENGINE_FILE:
					return new FileCache($config);

				//TODO...

			}

		}

	}

}