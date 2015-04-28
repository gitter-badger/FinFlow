<?php
/**
 * Transaction filter vars, setting up
 */

use FinFlow\UI;
use FinFlow\Util;
use FinFlow\Currency;

//--- set up some defaults ---//
$currmonthstart = ( date('Y-m') . '-01' );
$labels 	    = array();
$accounts       = array();
$type           = false;
$currency_id    = 0;

$sdate = ( date('Y-m') . '-01' );
$edate = ( date('Y-m-') . date('t') );

if ( get('sdate') )
	$sdate = get('sdate');

if ( get('edate') )
	$edate = get('edate');

if ( get('type') )
	$type = get('type');

if ( get('labels') ){
	$labels  = trim( urldecode( get('labels') ), ',' );
	
	if ( strlen($labels) )
		$labels = @explode(',', $labels);
	else 
		$labels = array();
}

if( get('accounts') ){
    $accounts  = trim(urldecode( get('accounts') ), ',');

    if ( strlen($accounts) )
        $accounts = @explode(',', $accounts);
    else
        $accounts = array();
}

if ( get('currency_id') )
	$currency_id = intval( get('currency_id') );

$count	= FN_RESULTS_PER_PAGE;
$start 	= get('pag') ? UI::pagination_get_current_offset(get('pag'), $count) : 0;

$year  	= date('Y', strtotime($sdate));
$month	= date('n', strtotime($sdate));
$day    = date('j', strtotime($sdate));

$eyear  = date('Y', strtotime($edate));
$emonth = date('n', strtotime($edate));
$eday	= date('j', strtotime($edate));

$days 	= range(1, date('t', strtotime("{$year}-{$month}-05")));
$edays 	= range(1, date('t', strtotime("{$year}-{$emonth}-05")));

$months = range(1, 12);
$years 	= range($year-10, date('Y')); //TODO the min value, maybe not hardcoded, but taken from db

//---prepare filters ---//
$filters = array('startdate'=>$sdate, 'enddate'=>$edate, 'type'=>$type, 'labels'=>$labels, 'accounts'=>$accounts, 'currency_id'=>$currency_id);
$filters = Util::clean_array($filters);
//---prepare filters ---//

if ( isset($filters['currency_id']) )
	$Currency = Currency::get($currency_id);
else 
	$Currency = Currency::get_default();

//--- prepare an array of pagevars, to be used for pagination ---//
$pagevars = array(
    'sdate'      => isset($filters['startdate']) ? $filters['startdate'] : '',
    'edate'      => isset( $filters['enddate'] ) ? $filters['enddate'] : '',
    'labels'     => isset( $filters['labels'] ) ?  ( is_array($filters['labels']) ? @implode(',', $filters['labels']) : $filters['labels'] ) : '',
    'accounts'   => isset( $filters['accounts'] ) ? ( is_array($filters['accounts']) ? @implode(',', $filters['accounts']) : $filters['accounts'] ) : '',
    'currency_id'=> isset($filters['currency_id']) ? $filters['currency_id'] : '',
    'type'       => isset($filters['type']) ? $filters['type'] : ''
);

$pagevars = Util::clean_array($pagevars);

//--- prepare an array of pagevars, to be used for pagination ---//