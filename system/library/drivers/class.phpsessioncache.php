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

	const PREFIX = 'fn_';

	protected $cfg = array();

	public function __construct($config=array(), $autoinit=true){

		if( PHP_SESSION_DISABLED == session_status() )
			throw new \Exception(
				__t('PHP session support is disabled. PHPSessionCache needs sessions enabled.')
			);

		$this->cfg = parse_args($config);

		if( $autoinit )
			$this->init($config);

	}

	public function init($config=array()){

		$_defaults = array(
			'lifetime'  => 7200,
			'path'      => '/',
			'domain'    => Util::get_hostname(),
			'secure'    => is_ssl(),
		);

		if( empty($config) ){
			$this->cfg = array_merge($_defaults, $this->cfg);
		}
		else
			$this->cfg = array_merge($_defaults, parse_args($config));

		if( ! session_id() )
			session_set_cookie_params(
				$this->cfg['lifetime'],
				$this->cfg['path'],
				$this->cfg['domain'],
				$this->cfg['secure']
			);

		@session_start();

		Log::debug('Started session with id=' . session_id());

	}

	public function set($key, $value=1){
		$_SESSION[self::PREFIX . $key] = $value;
	}

	public function get($key){
		return isset($_SESSION[self::PREFIX . $key]) ? $_SESSION[self::PREFIX . $key] : null;
	}

	public function remove($key){
		unset( $_SESSION[self::PREFIX . $key] );
	}

	public function clean(){
		session_destroy();
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