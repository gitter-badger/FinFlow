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

	const FLASHDATA_KEY       = 'flash';
	const FLASHDATA_AUTOCLEAN = false; //TODO...

	const DRIVER_PHP        = 'php';
	const DRIVER_APC        = 'apc';
	const DRIVER_MEMCACHED  = 'memcached';

	const DRIVER_DEFAULT = 'php';

	protected static $instance = null;
	protected static $driver   = null;

	protected $engine    = null;
	protected $flashdata = array();

	protected $ttl       = 7200;
	protected $cookie    = 'session';
	protected $prefix    = 'session_';

	protected $config = array();

	private function __construct(AbstractCache $cache, $config=array()){

		$_defaults = array(
			'lifetime'  => 7200,
			'path'      => '/',
			'domain'    => Util::get_cookie_domain(),
			'secure'    => is_ssl(),
		);

		$this->engine = $cache;
		$this->config = array_merge($this->config, $_defaults, $config);
	}

	private function __start(){

		//TODO..
		//



		//$this->cookie = substr();

		//--- check session cookie ---//
		//die("starting session..." . __LINE__);
		//--- check session cookie ---//

		$this->engine->init();
	}

	private function __vset($key, $value=1){
		$this->engine->set($key, $value);
	}

	private function __vget($key){
		return $this->engine->get($key);
	}

	private function __destroy(){
		//TODO only update session prefix
		//TODO engine cleanup is likely to remove all cache vars
		$this->engine->clean();
	}

	private function __wakeup(){
		return null;
	}

	private function __clone(){
		throw new \Exception("Cloning not allowed");
	}

	protected function setCookieName(){
		$this->cookie = substr(md5(FN_CRYPT_SALT . '-' . date('Y-m 07:07:07')), 0, 12); return $this->cookie;
	}

	protected function getCookieName(){
		return $this->setCookieName();
	}

	protected function isCookieValid(){
		//TODO check cookie validity ...
	}

	protected function generateCookie($force=false){

		$this->setCookieName();

		$this->ttl    = isset($this->config['lifetime']) ? intval( $this->config['lifetime'] ) : $this->ttl;
		$this->prefix = ( Util::random_string(7) . '_' );

		$regen = ( $force or ! isset($_COOKIE[$this->cookie]) or empty($_COOKIE[$this->cookie]) );

		if( $regen ){
			$cookie_value = serialize( array('p'=>$this->prefix, 'u'=>time()) );
			$cookie_value = Cryptographer::encrypt($cookie_value, FN_CRYPT_SALT );
		}
		else
			$cookie_value = $_COOKIE[$this->cookie];

		//TODO implement session persistence die($cookie_value);

		//set session cookie //TODO add configuration options
		setcookie(
			$this->cookie,
			$cookie_value,
			time()+$this->ttl,
			$this->config['path'],
			$this->config['domain'],
			$this->config['secure']
		);


		return $this->cookie;

	}

	public function __set($key, $value=1){
		$this->__vset($key, $value=1);
	}

	public function __get($key){
		$this->__vget($key);
	}

	public function __update(){
		//TODO...
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
					$engine         = Cache::fire(Cache::ENGINE_PHP, $config);
					self::$instance = new Session($engine, $config);
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
		$session = self::getInstance($driver, $config);

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

	public static function setFlashdata($key, $value=1){

		$new       = is_array($key) ? $key : array($key=>$value);
		$flashdata = self::get(self::FLASHDATA_KEY);

		$flashdata = ( empty($flashdata) or ! is_array($flashdata) ) ? $new : array_merge($flashdata, $new);

		self::set(self::FLASHDATA_KEY, $flashdata);

	}

	public static function getFlashdata($key, $autoclean=self::FLASHDATA_AUTOCLEAN){

		$flashdata = self::get(self::FLASHDATA_KEY);

		if( $autoclean )
			self::removeFlashdata($key);

		return isset($flashdata[$key]) ? $flashdata[$key] : null;

	}

	public static function removeFlashdata($key){

		$flashdata = self::get(self::FLASHDATA_KEY);

		if( ! empty($flashdata) and is_array($flashdata) and isset($flashdata[$key]) ){
			unset($flashdata[$key]); self::set(self::FLASHDATA_KEY, $flashdata);
		}
	}

}