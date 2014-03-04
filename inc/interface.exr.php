<?php
/**
 * FinFlow 1.0 - Interface for Exchange rates parsers
 * @author Adrian S. (http://adrian.silimon.eu/)
 * @version 1.0
 */

interface fn_ExchangeRatesParser{

    public function isServiceAvailable();

    public function getTimestamp();

    public function setBaseCurrency($ccode);

    public function getBaseCurrency();

    public function getAvailableCurrencies();

    public function getExchangeRate($ccode);

}

class fn_ExchangeRatesParserBase{

    const name                      = 'Generic Exchange Rate Parser ';
    const EndpointURL          = 'http://example.com';
    const websiteURL            = 'http://example.com';
    const defaultCurrency     = 'EUR';

}

class fn_ExchangeRatesParserFactory{
    //TODO implement using factory
}