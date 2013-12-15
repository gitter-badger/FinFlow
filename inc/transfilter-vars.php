<?php
/**
 * Transaction filter vars, setting up
 */

//--- set up some defaults ---//
$currmonthstart = ( date('Y-m') . "-01" );
$labels 			   = array();
$accounts         = array();

$sdate = (date('Y-m') . "-01");
$edate= (date('Y-m-') . date('t'));

if ( isset($_GET['sdate']) )
	$sdate = $_GET['sdate'];

if ( isset($_GET['edate']) )
	$edate = $_GET['edate'];

if ( isset($_GET['type']) )
	$type = trim($_GET['type']);

if ( isset($_GET['labels']) ){
	$labels  = trim(urldecode($_GET['labels']), ",");
	
	if ( strlen($labels) )
		$labels = @explode(",", $labels);
	else 
		$labels = array();
}

if( isset($_GET['accounts']) ){
    $accounts  = trim(urldecode($_GET['accounts']), ",");

    if ( strlen($accounts) )
        $accounts = @explode(",", $accounts);
    else
        $accounts = array();
}

if ( isset($_GET['currency_id']) )
	$currency_id = intval($_GET['currency_id']);

$start 	= isset($_GET['pag']) ? intval($_GET['pag']) : 0;
$count	= FN_RESULTS_PER_PAGE;

$year  	= date('Y', strtotime($sdate));
$month	= date('n', strtotime($sdate));
$day		= date('j', strtotime($sdate));

$eyear  	= date('Y', strtotime($edate));
$emonth= date('n', strtotime($edate));
$eday	= date('j', strtotime($edate));

$days 	= range(1, date('t', strtotime("{$year}-{$month}-05")));
$edays 	= range(1, date('t', strtotime("{$year}-{$emonth}-05")));

$months = range(1, 12);
$years 	= range($year-10, date('Y')); //TODO the min value, maybe not hardcoded, but taken from db

//---prepare filters ---//
$filters = array('startdate'=>$sdate, 'enddate'=>$edate, 'type'=>$type, 'labels'=>$labels, 'accounts'=>$accounts, 'currency_id'=>$currency_id);
$filters = fn_Util::clean_array($filters);
//---prepare filters ---//

if ( isset($filters['currency_id']) )
	$Currency = fn_Currency::get($currency_id);
else 
	$Currency = fn_Currency::get_default();

//--- prepare an array of pagevars, to be used for pagination ---//
$pagevars = array(
    'sdate'             => $filters['startdate'],
    'edate'            => $filters['enddate'],
    'labels'            => $filters['labels'],
    'accounts'        => $filters['accounts'],
    'currency_id'   => $filters['currency_id'],
    'type'             => $filters['type']
);

$pagevars = fn_Util::clean_array($pagevars);

//--- prepare an array of pagevars, to be used for pagination ---//