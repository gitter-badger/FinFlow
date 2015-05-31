<?php
/**
 * FinFlow 1.0 - Cache implementation using php sessions
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow\Drivers;

use FinFlow\Log;
use FinFlow\Util;

class PHPSessionCache extends AbstractCache{

	/**
	 * Indicates maximum available TTL for a cache var
	 */
	const MAX_TTL = 86400;

	/**
	 * Session name
	 * @var string
	 */
	protected $cookie = 'cache_session';

	protected $prefix ='cache_';

	protected $cfg           = array();
	protected $lastAccessTime = 0;
	protected $lastUpdateTime = 0;

	public function __construct($config=array(), $autoinit=true){

		if( PHP_SESSION_DISABLED == session_status() )
			throw new \Exception(
				__t('PHP session support is disabled. PHPSessionCache needs sessions enabled.')
			);

		$this->cfg = parse_args($config);

		if( $autoinit )
			$this->init($config);

	}

	protected function upLastAccessTime($both=true){

		$this->lastAccessTime = time();

		if( $both )
			$this->upLastUpdateTime();

	}

	protected function upLastUpdateTime(){
		$this->lastUpdateTime = time();
	}

	public function init($config=array()){

		$cookie_name = substr(md5(FN_CRYPT_SALT . '-' . date('Y-m-d 07:07:07') ), 0, 12);
		$this->prefix= substr(md5($cookie_name), 0, 7);

		$_defaults = array(
			'name'      => $cookie_name,
			'lifetime'  => 7200,
			'path'      => '/',
			'domain'    => Util::get_cookie_domain(),
			'secure'    => is_ssl(),
		);

		$this->cfg = array_merge($_defaults, $this->cfg, parse_args($config));

		if( ! empty($this->cfg['name']) ){
			session_name($this->cfg['name']);
		}

		$this->cookie = session_name();

		session_set_cookie_params(
			$this->cfg['lifetime'],
			$this->cfg['path'],
			$this->cfg['domain'],
			$this->cfg['secure']
		);

		//start php session
		if( ! session_id() )
			session_start();

		$this->upLastAccessTime();

	}

	public function set($key, $value=1){
		$this->upLastAccessTime(); $_SESSION[$this->prefix . $key] = $value;
	}

	public function get($key){
		$this->upLastAccessTime(); return isset($_SESSION[$this->prefix . $key]) ? $_SESSION[$this->prefix . $key] : null;
	}

	public function remove($key){
		$this->upLastAccessTime(); unset( $_SESSION[$this->prefix . $key] );
	}

	public function clean(){
		session_destroy();
	}

	public function getLastAccessTime(){
		return $this->lastAccessTime;
	}

	public function getLastUpdateTime(){
		return $this->lastUpdateTime;
	}

	public function getCookieName(){
		return $this->cookie;
	}

	public function getSessionId(){
		return session_id();
	}

	/**
	 * Gets cache stats. Note: the size might not be the *actual size occupied by all of the cache variables
	 * @return array
	 */
	public function stats(){

		$serialized = serialize($_SESSION);

		if (function_exists('mb_strlen')) {
			$size = mb_strlen($serialized, '8bit');
		} else {
			$size = strlen($serialized);
		}

		return array(
			'entries_count'=> count($_SESSION),
			'size'         => Util::fmt_filesize($size),
		);
	}
}