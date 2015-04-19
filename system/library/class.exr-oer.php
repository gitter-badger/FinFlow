<?php

/**
 * Implements the Open exchange rates API
 * Class OER_ExchangeRateParser
 */
class OER_ExchangeRateParser extends fn_ExchangeRatesParserBase implements fn_ExchangeRatesParser{

    const name                      = 'OpenExchangeRates.org';
    const id                           = 'oer';
    const EndpointURL          = 'https://openexchangerates.org/api/latest.json';
    const websiteURL            = 'https://openexchangerates.org/';
    const defaultCurrency     = 'USD';

    /**
     * OpenExchangeRates.org Application ID (Key)
     * @var null
     */
    var $appID = null;

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
     * @param null $app_id
     * @param bool $base
     */
    function __construct( $app_id, $base=false ){

        if( $app_id ){

		    $this->appID            = $app_id;
            $this->origCurrency = $base ? $base : self::defaultCurrency;

            $this->refresh();

        }
        else trigger_error(__CLASS__ . ' Error: OpenExchangeRates.org Application ID is required.', E_USER_WARNING);

	}

    function getEndpointURL(){
        return (self::EndpointURL . '?app_id=' . $this->appID . '&base=' . strtoupper( $this->origCurrency ) );
    }

    public function refresh(){

        $this->jsonDocument = $this->isCacheValid() ? @file_get_contents( $this->getCacheFilePath() ) : @file_get_contents( $this->getEndpointURL() );

        if( strlen( $this->jsonDocument ) ){
            $this->parseJSONDocument(); return true;
        }else {

            fn_Log::to_file('Fisierul  de la URL-ul ' . $this->getEndpointURL() . ' nu poate fi parsat ca si json.' );
            trigger_error(__CLASS__ . ' Error: Could not parse the json file from ' . $this->getEndpointURL() , E_USER_WARNING );

            return false;

        }
    }

	/**
	 * parseJSONDocument method
	 *
	 * @access        public
	 * @return         void
	 */
	public function parseJSONDocument(){

        $this->currency = array();

        $json = json_decode( $this->jsonDocument, true );

        if( isset($json['error']) ){
            fn_Log::to_file('Fisierul nu poate fi parsat ca si json. Eroare: ' . $json['description']); trigger_error(__CLASS__ . ' Error: ' . $json['description'], E_USER_WARNING ); return false;
        }

        $this->date 			= date(FN_DATETIME_FORMAT, $json['timestamp']);
        $this->timestamp 	= @intval( $json['timestamp'] );

        $this->origCurrency	= strtoupper( $json['base'] );

        if( is_array( $json['rates'] ) and count( $json['rates'] ) ) foreach($json['rates'] as $currency=>$rate){
            $this->currency[] = array('name'=>trim($currency), 'value'=>floatval(1/$rate), 'multiplier'=>1); //the RATE has to be divided to 1, as 1 BC = ? CC => 1 CC = 1/? BC
        }
	}

    public function getAvailableCurrencies(){

        $json = json_decode( $this->jsonDocument, true );

        if( isset($json['error']) ){
            fn_Log::to_file('Fisierul nu poate fi parsat ca si json. Eroare: ' . $json['description']); return false;
        }

        $Currencies = array();

        $this->date 			= date(FN_DATETIME_FORMAT, $json['timestamp']);
        $this->timestamp 	= @intval( $json['timestamp'] );

        $this->origCurrency	= strtoupper( $json['base'] );

        if( is_array( $json['rates'] ) and count( $json['rates'] ) ) foreach($json['rates'] as $currency=>$rate){
            $Currencies[] = trim($currency);
        }

        return $Currencies;

    }

    public function getBaseCurrency(){
        return $this->origCurrency;
    }

    public function setBaseCurrency( $ccode ){
        $this->origCurrency = $ccode; return $this->refresh();
    }

	/**
	 * get current exchange rate: example getExchangeRate("USD")
	 * @access  public
     * @param $ccode
	 * @return float
	 */
    public function getExchangeRate( $ccode ){
		foreach($this->currency as $line){
			if( $line['name'] == $ccode ) return floatval( $line['value'] );
		}

		return 0; //Incorrect Currency Code
	}

    public function isServiceAvailable(){
        return strlen( $this->jsonDocument );
    }

    public function getTimestamp(){
        return $this->timestamp;
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

        $json = $this->jsonDocument;

        $this->setBaseCurrency($origCurrency); //reset original currency

        $path = $this->getCacheFilePath();

        if( @file_put_contents($path, $json) )
            return $path;
        else
            return false;

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

        $this->jsonDocument = @file_get_contents( $this->getCacheFilePath() ); $json = @json_decode( $this->jsonDocument, true );

        if( strlen( $this->jsonDocument ) and ( $json['base'] == $this->origCurrency ) ){
            $this->parseJSONDocument(); return $this->getExchangeRate($ccode);
        }

        return false;

    }

    public function buildCacheInWindow( $base=null  ){
        if( $this->buildCache($base) )
            return 'ended';
        else
            return false;
    }

}