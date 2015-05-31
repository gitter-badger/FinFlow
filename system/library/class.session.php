<?php
/**
 * FinFlow 1.0 - Better PHP Session support
 * @author Adrian S. (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow;

use FinFlow\Drivers\AbstractCache;
use FinFlow\Drivers\PHPSessionCache;
use Psr\Log\InvalidArgumentException;

$Session = false;

/**
 * Class Session
 * @package FinFlow
 *
 * @property AbstractCache $engine
 */
class Session{

	const COOKIE = 'fn_session';
	const ENCRYPT= true;

	const DRIVER_PHP        = 'php';
	const DRIVER_APC        = 'apc';
	const DRIVER_MEMCACHED  = 'memcached';

	const DRIVER_DEFAULT = 'php';

	protected static $instance = null;
	protected static $driver   = null;

	protected $engine = null;

	private function __construct(AbstractCache $cache){
		$this->engine = $cache;
	}

	private function __start(){
		$this->engine->init();
	}

	private function __vset($key, $value=1){
		$this->engine->set($key, $value);
	}

	private function __vget($key){
		return $this->engine->get($key);
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

	public function __set($key, $value=1){
		$this->__vset($key, $value=1);
	}

	public function __get($key){
		$this->__vget($key);
	}

	public static function getInstance($driver=false, $config=array()){

		if( empty($driver) )
			$driver = self::$driver ?: self::DRIVER_DEFAULT;

		if( null == self::$driver )
			self::$driver = $driver;
		else if( $driver != self::$driver )
			throw new \Exception('Cannot change driver after session initialization.');

		if( null == self::$instance ){

			switch($driver){

				case self::DRIVER_PHP: {
					$engine         = Cache::fire(Cache::ENGINE_PHP);
					self::$instance = new Session($engine);
				};
				break;

				//TODO add support for apc and memcached

				default:
					throw new InvalidArgumentException(
						__t('Driver %s is not supported.', $driver)
					);

			}

		}

		return self::$instance;

	}

	public static function start($driver, $config=array()){
		$session = self::getInstance();

		$session->__start();
	}

	public static function restart(){

		$session = self::getInstance();

		$session->__destroy();
		$session->__start();
	}

	public static function destroy(){
		$session = self::getInstance();
		$session->__destroy();
	}

	public static function getCurrentSession(){
		return self::getInstance();
	}

	public static function update(){
		$session = self::getInstance();
		$session->__vset('last_update', time());
	}

	public static function store($key, $value=1){
		$session = self::getInstance();
		$session->__vset($key, $value);
	}

	public static function set($key, $value=1){
		self::store($key, $value);
	}

	public static function fetch($key){
		$session = self::getInstance(); return $session->__vget($key);
	}

	public static function get($key){
		return self::fetch($key);
	}

}