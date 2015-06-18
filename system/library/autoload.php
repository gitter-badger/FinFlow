<?php
/**
 * FinFlow 1.0 - Class autoloader
 * @author Adrian S. (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

include_once 'helpers.php';

spl_autoload_register(function($className) {

	//TODO update autoloader to load classes by name

	$incpath = dirname(__FILE__);

	$default_ns = 'FinFlow';
	$className  = str_replace("\\" , DIRECTORY_SEPARATOR , $className);

	$parts     = @explode(DIRECTORY_SEPARATOR, $className);

	$namespace = array_shift($parts);
	$className = array_pop($parts);
	$className = array( 'class.' . $className . '.php' );
	$className = strtolower( implode(DIRECTORY_SEPARATOR, array_merge($parts, $className)) );

	if( empty($namespace) or ( $namespace == $default_ns ) )
		$relative_path = $className;
	else {
		$relative_path = ( strtolower($namespace) . DIRECTORY_SEPARATOR . $className );
	}

	$classpath = ( $incpath. DIRECTORY_SEPARATOR . $relative_path );

	if( file_exists($classpath) )
		include_once ($classpath);
	else
		return false; //failed to load

});