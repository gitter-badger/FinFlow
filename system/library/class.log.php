<?php

namespace FinFlow;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Class Log
 * @package FinFlow
 * @property Logger $logger
 */
class Log{

	protected static $logger = null;
	protected static $level  = 0;

	const DEBUG     = Logger::DEBUG;
	const INFO      = Logger::INFO;
	const WARNING   = Logger::WARNING;
	const ERROR     = Logger::ERROR;
	const CRITICAL  = Logger::CRITICAL;
	const ALERT     = Logger::ALERT;

	//const EMERGENCY = Logger::EMERGENCY;
	//const NOTICE    = Logger::NOTICE;

	const LEVEL_DEBUG    = 0;
	const LEVEL_INFO     = 10;
	const LEVEL_ERROR    = 20;
	const LEVEL_CRITICAL = 30;

	const LEVEL_DEFAULT         = self::LEVEL_CRITICAL;
	const LOGGER_INIT_EXCEPTION = 'The logger has not been initialized. Use init() to initialize the logger.';

	public static function init($handler=null, $level=null, $name=null){

		if( empty($handler) )
			$handler = new StreamHandler( FN_LOGFILE );

		if( empty($level) )
			$level = self::LEVEL_DEFAULT;

		if( empty($name) )
			$name = ( 'FinFlow v' . FN_VERSION );

		self::$logger = new Logger($name);

		self::addHandler( $handler );
		self::setLevel($level);
	}

	public static function addHandler($handler){
		if( self::$logger )
			self::$logger->pushHandler($handler);
		else
			self::exception(self::LOGGER_INIT_EXCEPTION);
	}

	public static function setLevel($level){
		self::$level = $level;
	}

	public static function setThreshold($threshold){
		self::$level = $threshold;
	}

	public static function getLogger(){
		return self::$logger;
	}

	public static function info($msg){

		if( self::$level > self::LEVEL_INFO  )
			return;

		if( self::$logger )
			self::$logger->addInfo($msg);
		else
			self::exception(self::LOGGER_INIT_EXCEPTION);
	}

	public static function debug($msg){

		if( self::$level > self::LEVEL_DEBUG  )
			return;

		if( self::$logger )
			self::$logger->addDebug($msg);
		else
			self::exception(self::LOGGER_INIT_EXCEPTION);
	}

	public static function warning($msg){

		if( self::$level > self::LEVEL_INFO )
			return;

		if( self::$logger )
			self::$logger->addWarning($msg);
		else
			self::exception(self::LOGGER_INIT_EXCEPTION);
	}

	public static function error($msg){

		if( self::$level > self::LEVEL_ERROR)
			return;

		if( self::$logger )
			self::$logger->addError($msg);
		else
			self::exception(self::LOGGER_INIT_EXCEPTION);

	}

	public static function critical($msg){

		if( self::$level > self::LEVEL_CRITICAL )
			return;

		if( self::$logger )
			self::$logger->addCritical($msg);
		else
			self::exception(self::LOGGER_INIT_EXCEPTION);
	}

	public static function exception($msg){
		throw new \Exception($msg);
	}

}