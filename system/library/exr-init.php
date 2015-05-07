<?php
/**
 * FinFlow 1.0 - ExchangeRates init file
 * @author Adrian S. (http://adrian.silimon.eu/)
 * @version 1.0
 */

if( !defined('FNPATH') ) exit;

use FinFlow\Settings;
use FinFlow\UI;

include_once ( FNPATH .  '/system/library/interface.exr.php' );

include_once ( FNPATH .  '/system/library/class.exr-bnr.php' );
include_once ( FNPATH .  '/system/library/class.exr-oer.php' );
include_once ( FNPATH .  '/system/library/class.exr-oerfree.php' );

use FinFlow\BNR_ExchangeRateParser;
use FinFlow\OER_ExchangeRateParser;
use FinFlow\OERFree_ExchangeRateParser;

//--- initialize the exchange rates global object ---//
$parser = Settings::get('exchange_rates_parser', 'bnr');
$fnexr  = null;

switch( $parser ){

    case ( BNR_ExchangeRateParser::id ):
	    $fnexr = new BNR_ExchangeRateParser(); break;

    case ( OERFree_ExchangeRateParser::id ):
	    $fnexr = new OERFree_ExchangeRateParser(); break;

    case ( OER_ExchangeRateParser::id ): {
        $app_id = defined('FN_OER_API_KEY') ? FN_OER_API_KEY : Settings::get('oer_api_key');
	    $fnexr  = new OER_ExchangeRateParser($app_id);
    } break;

}

if( empty( $fnexr ) )
    UI::fatal_error('Parserul pentru cursul valutar nu este configurat corect.', true, (FN_IS_CRON ? false : true));

//--- initialize the exchange rates global object ---//