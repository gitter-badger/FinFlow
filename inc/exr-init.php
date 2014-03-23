<?php
/**
 * FinFlow 1.0 - ExchangeRates init file
 * @author Adrian S. (http://adrian.silimon.eu/)
 * @version 1.0
 */

if( !defined('FNPATH') ) exit;

include_once ( FNPATH .  '/inc/class.exr-bnr.php' );
include_once ( FNPATH .  '/inc/class.exr-oer.php' );
include_once ( FNPATH .  '/inc/class.exr-oerfree.php' );

//--- initialize the exchange rates global object ---//
$parser = fn_Settings::get('exchange_rates_parser', 'bnr');
$fnexr   = null;

switch( $parser ){

    case ( BNR_ExchangeRateParser::id ):        $fnexr = new BNR_ExchangeRateParser(); break;
    case ( OERFree_ExchangeRateParser::id ): $fnexr = new OERFree_ExchangeRateParser(); break;
    case ( OER_ExchangeRateParser::id ): {
        $app_id = defined('FN_OER_API_KEY') ? FN_OER_API_KEY : fn_Settings::get('oer_api_key'); $fnexr = new OER_ExchangeRateParser($app_id);
    } break;

}

if( empty( $fnexr ) )
    fn_UI::fatal_error('Parserul pentru cursul valutar nu este configurat corect.', true, (FN_IS_CRON ? false : true));

//--- initialize the exchange rates global object ---//