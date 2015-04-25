<?php
/**
 * FinFlow 1.0 - Class autoloader
 * @author Adrian S. (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

spl_autoload_register(function($className) {

	$default_ns = 'FinFlow';
	$className  = str_replace("\\" , DIRECTORY_SEPARATOR , $className);

	$parts     = @explode(DIRECTORY_SEPARATOR, $className);

	$namespace = array_shift($parts);
	$className = strtolower( implode(DIRECTORY_SEPARATOR, $parts) );

	if( empty($namespace) or ( $namespace == $default_ns ) )
		$relative_path = "class.{$className}.php";
	else {
		$relative_path = ( strtolower($namespace) . DIRECTORY_SEPARATOR . "class.{$className}.php" );
	}

	$classpath = ( FNPATH . '/system/library/' . $relative_path );

	if( file_exists($classpath) )
		include_once ($classpath);
	else
		return false; //failed to load

});