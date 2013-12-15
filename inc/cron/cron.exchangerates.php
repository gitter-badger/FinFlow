<?php

define('INCPATH', rtrim(str_replace(basename(dirname(__FILE__)), "", dirname(__FILE__)), DIRECTORY_SEPARATOR));
define('FN_IS_CRON', 7);

include_once (INCPATH . '/init.php');
include_once (INCPATH . '/class.exrbnr.php');
include_once (INCPATH . '/class.cronassistant.php');

fn_CronAssistant::get_lock(__FILE__);

$testing = isset($_GET['test']) ? TRUE : FALSE;

$exchange = new exchangeRatesBNR(FN_EXCHANGERATES_XML_URL);

if ( $exchange->xmlDocument ){
	
	$defaultCurrency = fn_Currency::get_default();
	
	if ( $defaultCurrency->ccode != $exchange->origCurrency)
		fn_CronAssistant::cron_error( "Moneda implicit&#259; nu este configurat&#259; corect fa&#355;&#259; de cea raportat&#259; de parser: {$exchange->origCurrency}. ", $testing );
	
	$Currencies = fn_Currency::get_all(TRUE);
	
	if ( count($Currencies) ) foreach ($Currencies as $currency){
		
		$rate = $exchange->getCurs($currency->ccode);
		
		if ( $testing ) continue;
		
		if ( $rate ){
			
			$fnsql->update(fn_Currency::$table, array('cexchange'=>$rate), array('currency_id'=>$currency->currency_id));
			$fndb->execute_query( $fnsql->get_query() );
			
			//--- also insert rate in history table ---//
			fn_Currency::record_historic_rate($currency->currency_id, $rate);
			//--- also insert rate in history table ---//
			
		}
		else{ 
			fn_CronAssistant::release_lock(__FILE__);
            fn_CronAssistant::cron_error(" Moneda {$currency->ccode} nu este disponibi&#259; &#238;n lista de monede extrase de parser.", false);
		}
		
	}
	
	if ( $testing ) fn_UI::fatal_error("Scriptul func&#355;ioneaz&#259; normal.", false, true, 'note', "Not&#259;: ", 'Not&#259;');
	
	fn_CronAssistant::release_lock(__FILE__);
	
}
else{
	fn_CronAssistant::release_lock(__FILE__);
    fn_CronAssistant::cron_error("Nu se poate lua cursul valutar de la adresa " . FN_EXCHANGERATES_XML_URL);
}