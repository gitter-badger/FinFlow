<?php

define('INCPATH', rtrim(str_replace(basename(dirname(__FILE__)), "", dirname(__FILE__)), DIRECTORY_SEPARATOR));
define('FN_IS_CRON', 7);

include_once (INCPATH . '/init.php');
include_once (INCPATH . '/exr-init.php');
include_once (INCPATH . '/class.cronassistant.php');

global $fnexr; $testing = isset($_GET['test']) ? TRUE : FALSE; $in_window = ( ( php_sapi_name() != 'cli' ) and strlen($_SERVER['REMOTE_ADDR']) );


if ( $fnexr->isServiceAvailable() ){
	
	$defaultCurrency = fn_Currency::get_default();

	if ( $defaultCurrency->ccode != $fnexr::defaultCurrency) if( !$fnexr->setBaseCurrency( $defaultCurrency->ccode )  ) {
            fn_CronAssistant::release_lock(__FILE__);
            fn_CronAssistant::cron_error( "Moneda implicit&#259; nu este configurat&#259; corect fa&#355;&#259; de cea raportat&#259; de parser: {$fnexr->origCurrency}. ", ( $testing or $in_window ) );
    }

    //--- tell currency parser to build it's cache ---//
    if( !$fnexr->isCacheValid() ){

        if( $in_window ){

            session_start(); //start browser session

            $status = $fnexr->buildCacheInWindow(); $_SESSION['fn_exr_cache_builder_runcount']++;

            if( $status and ( $status != 'ended' ) ){
                 fn_UI::loading_screen('Se pregate&#351;te cache-ul... ('  . $_SESSION['fn_exr_cache_builder_runcount'] . ' de monede).' . $status, true);
            }
            else{
                $_SESSION['fn_exr_cache_builder_runcount'] = 0;
            }

        }else
            $fnexr->buildCache();

    }
    //--- tell currency parser to build it's cache ---//

    fn_CronAssistant::get_lock(__FILE__); //lock the cron instance

	$Currencies = fn_Currency::get_all(TRUE);
	
	if ( count($Currencies) ) foreach ($Currencies as $currency){

		$rate = $fnexr->getExchangeRate( $currency->ccode ); if ( $testing and $rate ) continue;

		if ( $rate ){
			
			$fnsql->update(fn_Currency::$table, array('cexchange'=>$rate), array('currency_id'=>$currency->currency_id));
			$fndb->execute_query( $fnsql->get_query() );
			
			//--- also insert rate in history table ---//
			fn_Currency::record_historic_rate($currency->currency_id, $rate);
			//--- also insert rate in history table ---//
			
		}
		else{ 
			fn_CronAssistant::release_lock(__FILE__);
            fn_CronAssistant::cron_error(" Moneda {$currency->ccode} nu este disponibi&#259; &#238;n lista de monede extrase de parser.", ($testing or $in_window) );
		}
		
	}
	
	if ( $testing )
        fn_UI::fatal_error("Scriptul func&#355;ioneaz&#259; normal.", false, true, fn_UI::$MSG_NOTE, "Not&#259;: ", 'Not&#259;');

    elseif( $in_window )
        fn_UI::fatal_error("Ratele de schimb au fost actualizate.", false, true, fn_UI::$MSG_NOTE, "Not&#259;: ", 'Not&#259;');
	
	fn_CronAssistant::release_lock(__FILE__);
	
}
else{
	fn_CronAssistant::release_lock(__FILE__);
    fn_CronAssistant::cron_error("Nu se poate lua cursul valutar de la serviciul " . $fnexr::websiteURL, ($testing or $in_window) );
}