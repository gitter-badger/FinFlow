<?php

class BNR_ExchangeRateParser extends fn_ExchangeRatesParserBase implements fn_ExchangeRatesParser{

    const name                      = 'Curs valutar BNR';
    const id                           = 'bnr';
    const EndpointURL          = 'http://www.bnr.ro/nbrfxrates.xml';
    const websiteURL            = 'http://www.bnr.ro/';
    const defaultCurrency     = 'RON';

	/**
	 * xml document
	 * @var string
	 */
	var $xmlDocument = "";
	 
	 
	/**
	 * exchange date
	 * BNR date format is Y-m-d
	 * @var string
	 */
	var $date = "";
	 
	 
	/**
	 * currency
	 * @var associative array
	 */
	var $currency = array();
	 
	var $origCurrency = NULL;
	var $timestamp 	= 0;

    /**
     * Constructor
     */
    function __construct(){
		$this->xmlDocument = $this->isCacheValid() ? @file_get_contents( $this->getCacheFilePath() ) : @file_get_contents( self::EndpointURL ); if( strlen($this->xmlDocument) ) $this->parseXMLDocument();
	}
	 
	/**
	 * parseXMLDocument method
	 *
	 * @access        public
	 * @return         void
	 */
	public function parseXMLDocument(){

        $this->currency = array();

        try{
            $xml = new SimpleXMLElement($this->xmlDocument);

            $this->date 			= $xml->Header->PublishingDate;
            $this->timestamp 	= @strtotime($this->date);
            $this->origCurrency	= strtoupper($xml->Body->OrigCurrency);

            foreach($xml->Body->Cube->Rate as $line){
                $this->currency[] = array('name'=>trim($line['currency']), 'value'=>floatval($line), 'multiplier'=>floatval($line['multiplier']));
            }
        }
        catch(Exception $e){
           fn_Log::to_file('Fisierul nu poate fi parsat ca si xml. Eroare: ' . $e->getMessage()); return false;
        }
	}

    public function getCurrenciesList(){

        $xml = new SimpleXMLElement($this->xmlDocument);

        $this->date 			    = $xml->Header->PublishingDate;
        $this->timestamp 	    = @strtotime($this->date);
        $this->origCurrency	= strtoupper($xml->Body->OrigCurrency);

        $Currencies = array();

        if( count($this->currency) ) foreach($this->currency as $currency)
            $Currencies[] = trim($currency['name']);

        return $Currencies;
    }

	/**
	 * get current exchange rate: example getCurs("USD")
	 * @access  public
     * @param $currency
	 * @return float
	 */
    public function getCurs($currency){
		if( count($this->currency) ) foreach($this->currency as $line){
			if( $line['name'] == $currency ) return ( $line['value'] / ( $line['multiplier'] > 0 ? $line['multiplier'] : 1  ) ) ;
		}

		return 0; //Cod incorect
	}


    public function isServiceAvailable(){
        return $this->timestamp > 0;
    }

    public function getTimestamp(){
         return $this->timestamp;
    }

    public function setBaseCurrency( $ccode ){
        if( $ccode != self::defaultCurrency ){
            trigger_error(__CLASS__ . ' Eroare: Cursul valutar BNR este disponibil doar pentru RON, asadar schimbarea monedei de baza nu este permisa.', E_USER_WARNING); return false;//operatiunea nu este suportata
        }
    }

    public function getBaseCurrency(){
        return $this->origCurrency;
    }

    public function getAvailableCurrencies(){
        return $this->getCurrenciesList();
    }

    public function getExchangeRate($ccode){
        return ($ccode == $this->origCurrency) ? 1 : $this->getCurs( $ccode );
    }

    /**
     * Saves a cache file with all supported currencies
     */
    public function buildCache(){

        $xml = @file_get_contents( self::EndpointURL ); $path = $this->getCacheFilePath();

        if( @file_put_contents($path, $xml) )
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
        return ( rtrim(fn_Util::get_cache_folder_path(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'currency-rates-' . strtolower($this->origCurrency) . '.xml' );
    }

    public function getExchangeRateFromCache($ccode){

        if( !$this->isCacheValid() ) return false;

        $this->xmlDocument = @file_get_contents( $this->getCacheFilePath() );

        if( strlen( $this->xmlDocument ) ){
            $this->parseXMLDocument(); return $this->getExchangeRate($ccode);
        }

        return false;

    }

    public function buildCacheInWindow(){
        if( $this->buildCache() )
            return 'ended';
        else
            return false;
    }

}