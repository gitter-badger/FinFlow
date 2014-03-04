<?php

/**
 * Implements the Free Open Exchange rates API from AppSpot
 * Class OERFree_ExchangeRateParser
 */
class OERFree_ExchangeRateParser extends fn_ExchangeRatesParserBase implements fn_ExchangeRatesParser{

    const name                                   = 'openexchangerates.appspot.com';
    const website                               = 'http://openexchangerates.appspot.com';

    const EndpointURL                       = 'http://openexchangerates.appspot.com/currency';
    const CurrenciesEndpointURL       = 'http://openexchangerates.appspot.com/currencies-en_US.json';

    const defaultCurrency     = 'USD';

	/**
	 * raw json document
	 * @var string
	 */
	var $jsonDocument = "";
	 
	 
	/**
	 * currency
	 * @var associative array
	 */
	var $currency = array();
	 
	var $origCurrency = NULL;
	var $timestamp 	= 0;

    /**
     * Constructor
     * @param bool $base the desired base currency
     */
    public function __construct( $base=false ){
        $this->origCurrency = $base ? $base : self::defaultCurrency; $this->timestamp = time();
	}

    public function getEndpointURL($ccode){
        return ( self::EndpointURL . '?from='. $this->origCurrency .'&to=' . $ccode . '&q=1' );
    }

	/**
	 * parseXMLDocument method
	 *
	 * @access        public
	 * @return         void
	 */
	public function parseJSONCurrencies(){

        $this->currency         = array();
        $this->jsonDocument = @file_get_contents( self::CurrenciesEndpointURL );

        $json = @json_decode( $this->jsonDocument, true );

        if( empty( $json ) ){
            fn_Log::to_file('Fisierul nu poate fi parsat ca si json. Eroare la descarcarea fisierului.' ); return false;
        }

        $this->date 			= date(FN_DATETIME_FORMAT, time());
        $this->timestamp 	= time();

        if( is_array( $json ) and count( $json ) ) foreach($json as $currency=>$details){
            $this->currency[] = $currency;
        }
	}

    public function getAvailableCurrencies(){
        $this->parseJSONCurrencies(); return $this->currency;
    }

	/**
	 * Get current exchange rate: example getExchangeRate("USD")
	 * @access  public
     * @param $ccode
     * @return float
	 */
    public function getExchangeRate( $ccode ){

        $this->jsonDocument = @file_get_contents( $this->getEndpointURL( $ccode ) );

        $json = @json_decode( $this->jsonDocument, true );

        if( empty( $json ) or empty( $json['rate'] ) ){
            fn_Log::to_file('Fisierul cu schimbul valutar poate fi parsat ca si json. Eroare la descarcarea fisierului.' ); return false;
        }

		return floatval( $json['rate'] );

	}

    public function setBaseCurrency( $ccode ){
        $this->origCurrency = $ccode;
    }

    public function getBaseCurrency(){
        return $this->origCurrency;
    }

    public function getTimestamp(){
        return $this->timestamp;
    }

    public function isServiceAvailable(){
        $this->jsonDocument = @file_get_contents( $this->getEndpointURL( self::defaultCurrency ) ); $json = @json_decode( $this->jsonDocument, true ); if( empty( $json ) or empty( $json['rate'] ) ) return false; return true;
    }

}