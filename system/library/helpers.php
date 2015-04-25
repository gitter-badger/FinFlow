<?php
/**
 * FinFlow 1.0 - Helper functions file
 * @author Adrian S. (adrian@finflow.org)
 * @version 1.0
 */

use FinFlow\UI;
use FinFlow\Util;

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