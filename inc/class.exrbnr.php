<?php

class exchangeRatesBNR{
	
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
     * @param $url
     */
    function __construct($url){
		$this->xmlDocument = @file_get_contents($url); if( strlen($this->xmlDocument) ) $this->parseXMLDocument();
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

        $this->date 			= $xml->Header->PublishingDate;
        $this->timestamp 	= @strtotime($this->date);
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
		foreach($this->currency as $line){
			if( $line["name"] == $currency ) return $line["value"];
		}

		return 0; //Cod incorect
	}
}