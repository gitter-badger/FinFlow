<?php
/**
 * FinFlow 1.0 - Helper functions file
 * @author Adrian S. (adrian@finflow.org)
 * @version 1.0
 */

use FinFlow\UI;
use FinFlow\Util;

function is_browser(){
	return ( php_sapi_name() != 'cli' ) and strlen($_SERVER['REMOTE_ADDR']);
}

function is_cli(){
	return ( php_sapi_name() == 'cli' ) and empty($_SERVER['REMOTE_ADDR']);
}

/**
 * Detects https protocol
 * @return bool
 */
function is_ssl(){

	//behind a load balancer;
	if ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' )
		$_SERVER['HTTPS'] = 'on';

	//fastcgi_param HTTPS on;
	if ( isset($_SERVER['HTTPS']) ) {

		if ( 'on' == strtolower($_SERVER['HTTPS']) )
			return true;

		if ( '1' == $_SERVER['HTTPS'] )
			return true;

	}

	//last if the port is 443, best guess it's ssl;
	elseif ( isset($_SERVER['SERVER_PORT']) and ( '443' == $_SERVER['SERVER_PORT'] ) ) {
		return true;
	}

	return false;

}

/**
 * Alias of is_ssl
 * @return bool
 * @see is_ssl()
 */
function is_https(){
	return is_ssl();
}

function get_input($key, $xss_filter=false){ //TODO add cli support
    return Util::get_input($key, $xss_filter);
}

/**
 * Returns the value of the $key from the input vars. Priority $_GET, $_POST
 * @param $key
 * @param bool $xss_filter
 * @return mixed|null
 */
function in($key, $xss_filter=false){
    return Util::get_input($key, $xss_filter);
}

function isset_in($key){
	return ( isset($_GET[$key]) or isset($_POST[$key]) );
}

/**
 * Returns the value for the array key
 * @param $array
 * @param $key
 * @param bool $xss_filter
 * @return mixed|null
 */
function av($array, $key, $xss_filter=false){
    return Util::array_value($array, $key, $xss_filter);
}

/**
 * Returns the value for the $_POST key
 * @param $key
 * @param bool $xss_filter
 * @return mixed|null
 */
function post($key, $xss_filter=true){
    return Util::array_value($_POST, $key, $xss_filter);
}

/**
 * Returns the value for the $_GET key
 * @param $key
 * @param bool $xss_filter
 * @return mixed|null
 */
function get($key, $xss_filter=true){
    return Util::array_value($_GET, $key, $xss_filter);
}

/**
 * Returns the value for the $_FILES key
 * @param $key
 * @return array|null
 */
function ufile($key){
	return Util::array_value($_FILES, $key, false);
}

/**
 * Returns the cookie value
 * @param $key
 * @param bool $xss_filter
 *
 * @return mixed|null
 */
function cookie($key, $xss_filter=true){
	return Util::array_value($_COOKIE, $key, $xss_filter);
}

/**
 * Parse arguments; if $args input is missing will parse all vars from $_GET, $_POST and $argv
 * @param array $args
 * @param bool $urldecode
 * @param bool $xss_filter
 *
 * @return array|mixed|string
 */
function parse_args($args=array(), $urldecode=true, $xss_filter=true){

	global $argv;

	if( empty($args) ){
		$args = is_cli() ? Util::xss_filter( $argv ) : Util::xss_filter( array_merge($_GET, $_POST) );
	}

	if( is_object($args) )
		$args = get_object_vars($args);

	if( is_string($args) and strlen($args) ){

		$args_str = $urldecode ? urldecode($args) : $args;
		$args_str = trim(trim($args_str), '?&');

		if( strpos($args_str, '&') === false ){
			$args = array($args_str=>'');
		}
		else{
			$args = array(); parse_str($args_str, $args);
		}

	}

	return $xss_filter ? Util::xss_filter( $args ) : $args;

}

/**
 * Returns the value of the cli argument index
 * @param $index
 *
 * @return string|null
 */
function arg($index){
	global $argv; $index = intval( $index ); return ( $index ? ( isset($argv[$index]) ? $argv[$index] : null ) : $argv[0] );
}

/**
 * Returns the url of an asset
 * @param string $relative_url
 * @param bool $echo
 * @param string $version
 * @return string
 */
function _url($relative_url, $echo=true, $version=null){
	if( $echo )
		UI::asset_url($relative_url,true, $version);
	else
		return UI::asset_url($relative_url, false, $version);
}


function url_part($index=0){
	return UI::url_part($index);
}

/**
 * Returns a translated message
 * @param $msg
 * @param array $args
 *
 * @return string
 */
function __t($msg, $args=array()){
	//TODO call the translate on the message

	if( is_array($args) ){
		$argsarray = $args;
	}
	else{

		$numargs  = func_num_args();
		$argsarray= array();

		if ($numargs > 1) for($i=1; $i<$numargs; $i++) $argsarray[] = func_get_arg($i);

	}

	return vsprintf($msg, $argsarray);

}


/**
 * Echoes a translated message
 * @param string $msg
 * @param array $args
 */
function __e($msg, $args=array()){

	if( is_array($args) ){
		$argsarray = $args;
	}
	else{

		$numargs  = func_num_args();
		$argsarray= array();

		if ($numargs > 1) for($i=1; $i<$numargs; $i++) $argsarray[] = func_get_arg($i);

	}

	echo __t($msg, $argsarray);
}


function esc_html($html){
	return htmlentities($html, ENT_QUOTES, 'UTF-8', false);
}

function esc_attr($value){
	return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
}

function create_form_nonce($name, $ttl=90){

	$nonces = $_SESSION['fn_nonces'];

	//TODO...
}

function form_nonce($prefix){
	//TODO
}

function form_nonce_input(){
	//TODO...
}

function form_nonce_valid($nonce, $name){
	//TODO...
}

function cleanup_nonces($force=true){
	//TODO...
}
