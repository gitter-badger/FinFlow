<?php
/**
 * FinFlow 1.0 - Hello file
 * @author Adrian S.
 * @version 1.0
 */

include_once '../inc/init.php';
include_once '../inc/class.cache.php';

$cache = new fn_Cache(fn_Cache::$file_engine, array('folder'=>dirname(__FILE__) . '/cache'));

if( $cache->is_initialized() ){
    echo 'Cache initialized<br/>';

    //$cache->store('adrian', array('true', 'false'), 600);
    //$cache->store('superadrian', array('true', 'false', 'random'), 600);

    $value = $cache->get('adrian');

    echo 'data stored in cache<br/>';

    echo "Value of key from cache: ";

    print_r($value);

}else
    echo 'Could not initialize cache';