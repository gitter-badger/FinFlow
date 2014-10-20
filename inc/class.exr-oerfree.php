<?php

/**
 * Implements the Free Open Exchange rates API from AppSpot
 * Class OERFree_ExchangeRateParser
 */
class OERFree_ExchangeRateParser extends fn_ExchangeRatesParserBase implements fn_ExchangeRatesParser{

    const name                                   = 'openexchangerates.appspot.com';
    const id                                        = 'finflowoer';
    const websiteURL                          = 'http://openexchangerates.appspot.com';

    const EndpointURL                       = 'http://openexchangerates.appspot.com/currency';
    const CurrenciesEndpointURL       = 'http://openexchangerates.appspot.com/currencies.json';

    const defaultCurrency = 'USD';

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
        return ( self::EndpointURL . '?from='. $ccode .'&to=' . $this->origCurrency . '&q=1' );
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
            fn_Log::to_file('Fisierul  de la adresa ' .  self::CurrenciesEndpointURL . ' nu poate fi parsat ca si json. Eroare la descarcarea fisierului.' ); return false;
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

        if( $rate = $this->getExchangeRateFromCache($ccode) ) return $rate; //get it from cache if available

        $this->jsonDocument = @file_get_contents( $this->getEndpointURL( $ccode ) );

        $json = @json_decode( $this->jsonDocument, true );

        if( empty( $json ) or empty( $json['rate'] ) ){
            fn_Log::to_file('Fisierul cu schimbul valutar poate fi parsat ca si json. Eroare la descarcarea fisierului.' ); return false;
        }

		return floatval( $json['rate'] );

	}

    public function setBaseCurrency( $ccode ){
        $this->origCurrency = $ccode; return true;
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

    /**
     * Saves a cache file with all supported currencies
     * @param string $base
     * @return bool|string
     */
    public function buildCache( $base=null ){

        $origCurrency = $this->origCurrency;

        if( empty($base) )
            $base = $this->origCurrency;
        else
            $this->setBaseCurrency($base);

        $currencies =  $this->getAvailableCurrencies();
        $json          = array('base'=>$base, 'timestamp'=>time(), 'rates'=>array());
        $rates         = array();

        if( count($currencies) ) foreach($currencies as $ccode){
            if( $ccode ) $rates[$ccode] = $this->getExchangeRate($ccode); @sleep(1);
        }

        $this->setBaseCurrency($origCurrency); //reset original currency

        $json['rates'] = $rates; $json = json_encode($json); $path = $this->getCacheFilePath();

        if( @file_put_contents($path, $json) )
            return $path;
        else
            return false;

    }

    public function buildCacheInWindow( $base=null  ){

        $hash = md5(__FILE__); $per_batch = 1;

        $origCurrency = $this->origCurrency;

        $session_started = session_id() ? true : session_start(); if( $session_started ){

            $base = isset($_SESSION[$hash . '_pbase']) ? $_SESSION[$hash . '_pbase'] : $base; $_SESSION[$hash . '_pbase'] = $base;

            if( empty($base) )
                $base = $this->origCurrency;
            else
                $this->setBaseCurrency($base);

            $currencies = isset($_SESSION[$hash . '_pcurrencies']) ? $_SESSION[$hash . '_pcurrencies'] : $this->getAvailableCurrencies();
            $json          = isset($_SESSION[ $hash . '_pjson']) ? $_SESSION[ $hash . '_pjson'] : array('base'=>$base, 'timestamp'=>time(), 'rates'=>array());
            $rates         = isset($json['rates']) ? $json['rates'] : array();

            if( count($currencies) ) foreach($currencies as $ccode){

                if( $ccode and !isset($rates[$ccode]) ){

                    $rates[$ccode] = $this->getExchangeRate($ccode); $per_batch--; $json['rates'] = $rates;

                    $_SESSION[$hash . '_pcurrencies'] = $currencies;
                    $_SESSION[$hash . '_pjson']          = $json;

                    if( empty($per_batch) ){
                        return '<script type="text/javascript">setTimeout(\'window.location.reload()\', 1100);</script>'; //reload page to trigger next batch processing
                    }

                }

            }

            $this->setBaseCurrency($origCurrency); //reset original currency

            unset( $_SESSION[$hash . '_pcurrencies'] );
            unset( $_SESSION[$hash . '_pjson'] );

            $json['rates'] = $rates; $json = json_encode($json); $path = $this->getCacheFilePath();

            if( @file_put_contents($path, $json) )
                return 'ended';
            else
                return false;

        }
        else trigger_error("Function " . __CLASS__ . '::' . __FUNCTION__ . ' requires session support.', E_USER_WARNING);

    }

    /**
     * Checks if the cache file has been last generated today
     * @return bool
     */
    public function isCacheValid(){
        $mtime = @filemtime($this->getCacheFilePath()); if( $mtime < strtotime(date('Y-m-d 00:00:00')) ) return false; return true;
    }

    public function getCacheFilePath(){
        return ( rtrim(fn_Util::get_cache_folder_path(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'currency-rates-' . strtolower($this->origCurrency) . '.json' );
    }

    public function getExchangeRateFromCache($ccode){

        if( !$this->isCacheValid() ) return false;

        $json = json_decode( @file_get_contents( $this->getCacheFilePath() ), true );

        $this->date 			= date(FN_DATETIME_FORMAT, $json['timestamp']);
        $this->timestamp 	= @intval( $json['timestamp'] );

        if( $json['base'] == $this->origCurrency ){
            if( is_array( $json['rates'] ) and count( $json['rates'] ) and isset($json['rates'][$ccode]) ) return floatval($json['rates'][$ccode]);
        }

        return false;

    }

}