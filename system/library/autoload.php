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
	$className = array_pop($parts);
	$className = array( 'class.' . $className . '.php' );
	$className = strtolower( implode(DIRECTORY_SEPARATOR, array_merge($parts, $className)) );

	if( empty($namespace) or ( $namespace == $default_ns ) )
		$relative_path = $className;
	else {
		$relative_path = ( strtolower($namespace) . DIRECTORY_SEPARATOR . $className );
	}

	$classpath = ( FNPATH . '/system/library/' . $relative_path );

	if( file_exists($classpath) )
		include_once ($classpath);
	else
		return false; //failed to load

});