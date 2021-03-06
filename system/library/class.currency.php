<?php

namespace FinFlow;

class Currency{
	
	public static $table 	     = 'fn_currency';
	public static $table_history = 'fn_currency_history';


    public static function add($name, $symbol='lei', $code='RON', $rate=1){
        global $fndb, $fnsql;

        $name 	= $fndb->escape($name);
        $symbol= $fndb->escape($symbol);
        $code	= $fndb->escape(strtoupper($code));

        $rate = floatval($rate);

        $fnsql->insert(self::$table, array('csymbol'=>$symbol, 'cname'=>trim($name), 'ccode'=>$code, 'cexchange'=>$rate));

        if( $fndb->execute_query( $fnsql->get_query() ) ) {
            $currency_id = $fndb->last_insert_id; self::record_historic_rate($currency_id, $rate); return $currency_id;
        }

        return FALSE;

    }

    public static function record_historic_rate($currency_id, $rate, $date=false){

        global $fnsql, $fndb;

        $currency_id = intval($currency_id);

        if( empty($date) )
            $date = date(FN_MYSQL_DATE);
        else
            $date = date(FN_MYSQL_DATE, @strtotime($date));

        $rate = floatval($rate);

        if( $currency_id and $rate ) {
            $fnsql->insert(self::$table_history, array('currency_id'=>$currency_id, 'regdate'=>$date, 'cexchange'=>$rate));
            return $fndb->execute_query( $fnsql->get_query() );
        }

        return false;

    }


	public static function convert($value, $from, $to, $field="code"){
		
		if ( empty($value) ) return 0;

        if( $from == $to ) return $value;

		if ( $field == 'code' ){
			$from = self::get_by_code($from);
			$to	  = self::get_by_code($to);
		}
		
		if ( $field == 'id' ){
			$from = self::get($from);
			$to	  = self::get($to);
		}
		
		if ( empty($from) or empty($to) )
			return 0;

        self::log_exchange($from, $to, array(__CLASS__, __FUNCTION__));

		return  ( $value * floatval($from->cexchange) ) / floatval($to->cexchange);
		
	}

    public static function convert_to_default($value, $from){

        if ( intval($from) )
            $from = self::get($from);
        else
            $from = self::get_by_code($from);

        $to = self::get_default();

        if( $from->currency_id == $to->currency_id ) return $value;

        if ( empty($from) or empty($to) ) return 0;

        self::log_exchange($from, $to, array(__CLASS__, __FUNCTION__));

        return  ( $value * floatval($from->cexchange) ) / floatval($to->cexchange);

    }

    public static function historically_convert($value, $from, $to, $date=NULL, $field="code"){

        if ( empty($value) ) return 0;

        if( empty($date) ) return self::convert($value, $from, $to, $field);

        if ( $field == 'code' ){
            $from = self::get_by_code($from, 'currency_id');
            $to	 = self::get_by_code($to, 'currency_id');
        }

        if ( $field == 'id' ){
            $from = self::get($from);
            $to	 = self::get($to);
        }

        if ( isset($from->currency_id) and isset($to->currency_id) ); else return 0;

        //--- get the most closest value to the specified date ---//

        $defaultCC = self::get_default();

        if( $from->currency_id != $defaultCC->currency_id )
            $from = self::get_closest_historic_record($from->currency_id, $date);
        else
            $from = $defaultCC;

        if( $to->currency_id != $defaultCC->currency_id )
            $to = self::get_closest_historic_record($to->currency_id, $date);
        else
            $to = $defaultCC;

        //--- get the most closest value to the specified date ---//

        self::log_exchange($from, $to, array(__CLASS__, __FUNCTION__));

        return  ( $value * floatval($from->cexchange) ) / floatval($to->cexchange);


    }
	
	public static function get_all($excludeDefault=false){
		
		global $fnsql, $fndb;
		
		$fnsql->select('*', self::$table);
		
		if ( $excludeDefault ){
			$fnsql->condition('cexchange', '!=', 1);
			$fnsql->conditions_ready();
		}
		
		$fnsql->orderby(array('ccode'));
		
		return $fndb->get_rows( $fnsql->get_query() );
		
	}
	
	public static function get_default(){
		
		global $fnsql, $fndb;
		
		$fnsql->select('*', self::$table, array('cexchange'=>1)); 
		
		return $fndb->get_row( $fnsql->get_query() );
		
	}

	/**
	 * Get default currency code
	 * @return bool
	 */
    public static function get_default_cc(){
        $d = self::get_default(); return isset($d->ccode) ? $d->ccode : false;
    }
	
	public static function get_by_symbol($symbol, $fields="*"){
		
		global $fndb, $fnsql;
		
		$fnsql->select($fields, self::$table, array('csymbol'=>$symbol)); return $fndb->get_row( $fnsql->get_query() );
	}
	
	public static function get_by_code($code, $fields="*"){
		
		global $fndb, $fnsql;
		
		$code = strtoupper($code);
		
		$fnsql->select($fields, self::$table, array('ccode'=>$code)); 
		
		return $fndb->get_row( $fnsql->get_query() );
		
	}
	
	public static function get_growth($currency_id, $value=NULL, $timespan=86400){
		
		global $fndb, $fnsql;
		
		$value = floatval($value);
		
		if ( empty($value) ){
			$Currency = self::get($currency_id);
			$value	  = floatval($Currency->cexchange);
		}
		
		//--- get the most recent record from history, later then $timespan ---//
		$past 	= time() - $timespan;
		
		$fnsql->select('*', self::$table_history);
		$fnsql->condition('currency_id', '=', $currency_id);
		$fnsql->condition('regdate', '<=', date(FN_MYSQL_DATE, $past));
		$fnsql->conditions_ready();
		$fnsql->orderby('regdate', self::$table_history, 'DESC');
		$fnsql->limit(0, 1);
		
		$Record = $fndb->get_row( $fnsql->get_query() );
		
		
		if ( count($Record) )
			return $value - floatval($Record->cexchange);
		
		return 0;
		
	}
	
	public static function get($currency_id){
		
		global $fndb, $fnsql;
		
		$fnsql->select('*', self::$table, array('currency_id'=>intval($currency_id)));
		
		return $fndb->get_row( $fnsql->get_query() );
		
	}

    public static function remove($currency_id){
        global $fndb, $fnsql;

        $currency_id = intval($currency_id);

        //--- delete currency history ---//
        $fnsql->delete(self::$table_history, array('currency_id'=>$currency_id));
        $fndb->execute_query( $fnsql->get_query() );
        //--- delete currency history ---//

        $fnsql->delete(self::$table, array('currency_id'=>$currency_id));

        return $fndb->execute_query( $fnsql->get_query() );
    }

    /**
     * Retunrs an array of historical records for a currency
     * @param $currency_id
     * @param null $startdate
     * @param null $enddate
     * @return array|null
     */
	public static function get_currency_history($currency_id, $startdate=NULL, $enddate=NULL){
		
		global $fndb, $fnsql;
		
		$currency_id = intval($currency_id);
		
		if( empty($startdate) )
			$startdate = Util::get_relative_time(0, 1); //from one month ago
		
		if ( empty($enddate) )
			$enddate = date(FN_MYSQL_DATE);
		
		$startdate  = $fndb->escape($startdate);
		$enddate	= $fndb->escape($enddate);
		
		$fnsql->init(SQLStatement::$SELECT);
		$fnsql->table(self::$table_history);
		$fnsql->fieldas('cexchange', 'rate', "", FALSE);
		$fnsql->fieldas('regdate', 'date');
		$fnsql->from();
		
		$fnsql->condition('currency_id', '=', $currency_id);
		$fnsql->condition('regdate', '>=', $startdate);
		$fnsql->condition('regdate', '<=', $enddate);
		
		$fnsql->conditions_ready();
		
		return $fndb->get_rows( $fnsql->get_query() );
		
	}

    /**
     * Finds the closest exchange rate to the specified date for the currency_id
     * @param $currency_id
     * @param $date
     * @param int $rlevel
     * @return null
     */
    public static function get_closest_historic_record($currency_id, $date, $rlevel=0){

        global $fndb, $fnsql;

        $rlevel          = intval($rlevel);
        $currency_id = intval($currency_id);
        $date           = $fndb->escape($date);

        $fnsql->init(SQLStatement::$SELECT);
        $fnsql->resetGroupIndex();

        $fnsql->table(self::$table_history);
        $fnsql->fields('currency_id, cexchange, regdate');
        $fnsql->from();

        $fnsql->condition('currency_id', '=', $currency_id);

        if( $rlevel ){
            $fnsql->condition('regdate', '>=', $date);
            $fnsql->conditions_ready();
            $fnsql->orderby('regdate', self::$table_history, 'ASC');
        }
        else {
            $fnsql->condition('regdate', '<=', $date);
            $fnsql->conditions_ready();
            $fnsql->orderby('regdate', self::$table_history, 'DESC');
        }

        $fnsql->limit(0, 1);

        $Row = $fndb->get_row( $fnsql->get_query() );

        if( $Row and isset($Row->cexchange) )
            return $Row;
        else if( $rlevel > 0 ){ //no recorded data for the currency found
            Log::info("Nu exista inregistrare in istoric pentru moneda " . $currency_id, "Alerta:"); return self::get($currency_id);
        }

        return self::get_closest_historic_record($currency_id, $date, ++$rlevel);

    }

    /**
     * Checks if there are any transactions with the specified currency id
     * @param $currency_id
     * @return bool
     */
    public static function has_transactions($currency_id){
        global $fndb, $fnsql;

        $currency_id = intval($currency_id);

        $fnsql->select('trans_id,currency_id', OP::$table, array('currency_id'=>$currency_id));
        $fnsql->limit(0, 1);

        $Row = $fndb->get_row( $fnsql->get_query() );

        if( $Row and isset($Row->trans_id) ) return TRUE;

        return FALSE;

    }

    /**
     * Finds details about a given currency code, based on a given JSON file. Sample JSON file: https://gist.github.com/adrian7/5841262/b0d1722b04b0a737aade2ce6e055263625a0b435
     * @param $ccode
     * @param string $json_data_file
     * @return array|bool
     */
    public static function get_currency_details( $ccode, $json_data_file='currencies.json' ){
        $contents = @file_get_contents($json_data_file); if( $contents ){
            $all = @json_decode($contents, true); if( isset($all[$ccode]) ) return $all[$ccode];
        }

        return false;
    }

    /**
     * Logs an exchange operation
     */
    public static function log_exchange($from, $to, $func=null){

        $func = empty($func) ? ( __CLASS__ . '::' . __FUNCTION__ ) : ( is_array($func) ? implode('::', $func) : $func );

        if( ! isset($from->ccode) ){
            $from = is_object($from) ? self::get($from->currency_id) : ( empty($from) ? self::get_default() : self::get($from) );
        }

        if( ! isset($to->ccode) ){
            $to = is_object($to) ? self::get($to->currency_id) : ( empty($to) ? self::get_default() : self::get($to) );
        }

        if( Util::is_unittest_environment() ){
            Log::debug( "{$func} Converting {$from->ccode} to {$to->ccode} at {$from->cexchange} / {$to->cexchange}");
        }
        else {
            Log::debug("{$func}  Converting {$from->ccode} to {$to->ccode} at {$from->cexchange} / {$to->cexchange}");
        }

    }

    /**
     * @deprecated
     * @return array
     */
    public static function get_currencies_export_array(){

        global $fndb, $fnsql;

        $Export = array();

        $fnsql->select('*', self::$table); $Rows = $fndb->get_rows( $fnsql->get_query() );

        if( $Rows and count($Rows) ) foreach($Rows as $row){
            $currency = array();

            $currency['ID']         = $row->currency_id; //keep the id for reference
            $currency['name']    = $row->cname;
            $currency['symbol']  = $row->csymbol;
            $currency['code']     = $row->ccode;
            $currency['rate']      = $row->cexchange;

            $Export[] = $currency;
        }

        return $Export;

    }

    /**
     * @deprecated
     * @return array
     */
    public static function get_currencies_history_export_array(){

        global $fndb, $fnsql;

        $Export = array();

        $fnsql->select('*', self::$table_history); $Rows = $fndb->get_rows( $fnsql->get_query() );

        if( $Rows and count($Rows) ) foreach($Rows as $row){
            $currency = array();

            $currency['cid']     = $row->currency_id; //keep the id for reference
            $currency['date']   = $row->regdate;
            $currency['rate']    = $row->cexchange;

            $Export[] = $currency;
        }

        return $Export;

    }


    /**
     * Imports data from the array into the currencies table. Existing data won't be overwritten.
     * @param array $Currencies an array of SimpleXMLElement objects
     * @deprecated
     * @return bool
     */
    public static function import_osmxml($Currencies){

        $currenciesIDs = array();

        if( isset($Currencies->currency) ) foreach($Currencies->currency as $currency){

            $id     = intval($currency->ID); //keep the ID for reference
            $code   = strval($currency->code);
            $symbol = Util::unescape_xml( strval($currency->symbol) );

            $exists = self::get_by_code( $code, 'currency_id' );

            if( $exists and isset($exists->currency_id) ) {
                $currenciesIDs[$id] = $exists->currency_id; continue;
            }

            $currenciesIDs[$id] = self::add( strval($currency->name), $symbol, $code, $currency->rate );
        }

        return $currenciesIDs;

    }


    /**
     * Imports data from the array into the currencies history table. Existing data won't be overwritten.
     * @param array $History an array of SimpleXMLElement objects
     * @param array $currenciesIDs  a list of synchronized currencies ID from a previous currency import
     * @deprecated
     * @return bool
     */
    public static function import_osmxml_history($History, $currenciesIDs=array()){

        global $fnsql, $fndb;

        $icount=0; $imported = FALSE;

        if( isset($History->entry) ) foreach($History->entry as $entry){

            $cid = intval($entry->cid); $currency_id = isset($currenciesIDs[$cid]) ? intval( $currenciesIDs[$cid] ) : 0;

            if( empty($currency_id) ) continue; //entries without a currency ID are invalid

            $date = $fndb->escape( strval($entry->date) );
            $rate = floatval($entry->rate);

            //--- insert rate in history table ---//
            $fnsql->insert(self::$table_history, array('currency_id'=>$currency_id, 'regdate'=>$date, 'cexchange'=>$rate));

            $imported = $fndb->execute_query( $fnsql->get_query() );

            if( $imported ) $icount++;
            //--- insert rate in history table ---//
        }

        return $icount;

    }


}