<?php
/**
 * FinFlow 1.0 - Better PHP Session support
 * @author Adrian S. (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow;

use FinFlow\Drivers\AbstractCache;

$Session = false;

class Session{

	const COOKIE = 'fn_session';
	const ENCRYPT= true;

	const DRIVER_DEFAULT = 'apc';

	const DRIVER_PHP        = 'php';
	const DRIVER_APC        = 'apc';
	const DRIVER_MEMCACHED  = 'memcached';

	protected static $instance = null;
	protected static $driver   = null;

	protected $engine = null;

	private function __construct(AbstractCache $cache){
		$this->engine = $cache;
	}

	private function __start(){
		$this->engine->init();
	}

	private function __set($key, $value=1){
		$this->engine->set($key, $value);
	}

	private function __get($key){
		$this->engine->get($key);
	}

	private function __destroy(){
		$this->engine->clean();
	}

	private function __wakeup(){
		return null;
	}

	private function __clone(){
		throw new \Exception("Cloning not allowed");
	}

	public static function getInstance($driver, $config=array()){

		if( null == self::$instance ){

			//TODO ... switch()

		}

		return self::$instance;

	}

	public static function start($driver, $config=array()){
		self::getInstance($driver, $config);
	}

	public static function restart(){
		//TODO...
	}

	public static function destroy(){
		//TODO..
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