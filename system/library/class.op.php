<?php

namespace FinFlow;

/**
 * Operations Main Class
 * Class fn_OP
 * @version 1.5
 * @author Adrian7 (adrian@finflow.org)
 */
class OP{

	const TYPE_IN  = 'in';
	const TYPE_OUT = 'out';

	public static $table      = 'fn_op';
	public static $table_meta = 'fn_op_meta';

    public static $uploads_dir = "/uploads";
    public static $attached    = 'attached';


    /**
     * Adds a single operation
     * @param $type
     * @param int $value
     * @param int $currency_id
     * @param string $comments
     * @param null $date
     * @return bool
     */
    public static function add($type, $value=1, $currency_id=1, $comments="", $date=null){

        global $fndb, $fnsql;

        $type         = $type == FN_OP_IN ? FN_OP_IN : FN_OP_OUT;
        $value        = floatval( $value );
        $date         = empty($date) ? date(FN_MYSQL_DATE) : date(FN_MYSQL_DATE, @strtotime($date));

        $preflow = array('currency_id'=>$currency_id, 'value'=>$value); $flow = fn_OP::consistent_flow($type, $value, $currency_id, $date);

        extract($flow);

        $comments = $fndb->escape( $comments );

        $fnsql->insert(self::$table, array('optype'=>$type, 'value'=>$value, 'currency_id'=>$currency_id, 'comments'=>$comments, 'sdate'=>$date) );

        if( $fndb->execute_query( $fnsql->get_query() ) ) {

            $trans_id = $fndb->last_insert_id;

            if( $preflow['currency_id'] != $currency_id ) {
                self::save_metadata($trans_id, 'initial_currency_id', $preflow['currency_id']);
                self::save_metadata($trans_id, 'initial_value', $preflow['value']);
            }

            return $trans_id;

        }

        return false;

    }
	
	public static function apply_filters($filters=array()){
		
		global $fndb, $fnsql; $fnsql->resetGroupIndex();
		
		if ( is_object($filters) ) $filters = @get_object_vars($filters);
		
		if ( count($filters) and is_array($filters)){
			
			if ( isset($filters['type']) and $filters['type'] ){
					
				if ( !in_array($filters['type'], array(FN_OP_IN, FN_OP_OUT), TRUE) ) return array();
					
				$fnsql->condition('optype', '=', strtolower($filters['type']));
			}
			
			if ( isset($filters['labels']) and $filters['labels'] and is_array( $filters['labels']) and count($filters['labels']) ){
				$llist = $fndb->escape(implode(",", $filters['labels'])); $fnsql->condition('label_id', "IN", "({$llist})", "AND", fn_Label::$table_assoc, FALSE);
			}

            if ( isset($filters['accounts']) and $filters['accounts'] and is_array( $filters['accounts']) and count($filters['accounts']) ){
                $alist = $fndb->escape(implode(",", $filters['accounts'])); $fnsql->condition('account_id', "IN", "({$alist})", "AND", self::$table, FALSE);
            }

            if( isset($filters['ignore_accounts']) and $filters['ignore_accounts'] ){

                $act_group_id = 0; $act_sql_junction = "AND";

                if( is_array( $filters['ignore_accounts']) and count($filters['ignore_accounts']) ){

                    $act_group_id = 1; $act_sql_junction = "OR";

                    $ialist = $fndb->escape(implode(",", $filters['ignore_accounts']));
                    $fnsql->condition('account_id', "NOT IN", "({$ialist})", "AND", self::$table, FALSE, $act_group_id);

                }

                $fnsql->condition('account_id', '=', '0', $act_sql_junction, self::$table, FALSE, $act_group_id); //AND|OR `account_id` = 0

            }
			
			if ( isset($filters['startdate']) and $filters['startdate'] ){
				$startdate = $fndb->escape($filters['startdate']); $fnsql->condition('sdate', '>=', $startdate);
			}
			
			if ( isset($filters['enddate']) and $filters['enddate'] ){
				$enddate = $fndb->escape($filters['enddate']); $fnsql->condition('sdate', '<=', $enddate);
			}
			
			if ( isset($filters['currency_id']) and $filters['currency_id'] ){
				$currency_id = $fndb->escape($filters['currency_id']); $fnsql->condition('currency_id', '=', $currency_id);
			}

            //--- search filter ---//
            if ( isset($filters['search']) ){ //TODO add search support
                $keyword = trim( urldecode($filters['search']) );
            }
            //--- search filter ---//

			if ( empty($llist) and empty($alist) and empty($ialist) ) //no external table condition
				$fnsql->conditions_ready();
			else 
				$fnsql->conditions_ready(TRUE);

            if( isset($filters['orderby']) ){ //order by field
                $order = isset($filters['order']) ? 'DESC' : $fndb->escape( strtoupper($filters['order']) ); $orderby = $fndb->escape($filters['orderby']);
                $fnsql->orderby($orderby, self::$table, $order);
            }
			
			if ( isset($filters['start']) and isset($filters['count'])){
				$start = intval($filters['start']); $count = intval($filters['count']); $fnsql->limit($start, $count);
			}
			
			return TRUE;
		}
		
		return FALSE;
		
	}


    public static function get_total($filters){

        global $fndb, $fnsql;


        $fnsql->init(SQLStatement::$SELECT);
        $fnsql->table(self::$table);
        $fnsql->count('trans_id', 'total');
        $fnsql->from(self::$table);

        if ( isset($filters['labels']) )
            $fnsql->left_join(fn_Label::$table_assoc, 'trans_id', 'trans_id');

        $filters = array_merge($filters);

        self::apply_filters($filters);

        $Row = $fndb->get_row( $fnsql->get_query() );

        if( isset($Row->total) ) return $Row->total;

        return 0;

    }

	
	public static function get_operations($filters, $start=0, $count=125){
		
		global $fndb, $fnsql;
			

		$fnsql->init(SQLStatement::$SELECT);
		$fnsql->table(self::$table);
		$fnsql->fields('*');
		$fnsql->from(self::$table);

		if ( isset($filters['labels']) )
			$fnsql->left_join(fn_Label::$table_assoc, 'trans_id', 'trans_id');
		
		$filters = array_merge($filters, array('start'=>$start, 'count'=>$count));

        if( !isset($filters['orderby']) ) {
            $filters['orderby'] = array('sdate', 'trans_id');
            $filters['order']     = 'DESC';
        }

		self::apply_filters($filters);
		
		return $fndb->get_rows( $fnsql->get_query() );
		
	}

    /**
     * Returns transaction labels
     * @param $trans_id
     * @return array|null
     */
	public static function get_labels($trans_id){

		global $fndb, $fnsql;
		
		$trans_id = intval($trans_id);
		
		if ( $trans_id ){
		
			$fnsql->init(SQLStatement::$SELECT);
			$fnsql->table( fn_Label::$table_assoc);
			
			$fnsql->fields('*', fn_Label::$table_assoc);
			$fnsql->fields('*', fn_Label::$table);
			
			$fnsql->from();
			
			$fnsql->left_join(fn_Label::$table, 'label_id', 'label_id');
			$fnsql->condition('trans_id', '=', $trans_id);
			$fnsql->conditions_ready();

			return $fndb->get_rows( $fnsql->get_query() );

		}
		
	}

    public static function get_account($trans_id){
        global $fndb, $fnsql;

        $trans_id = intval($trans_id);

        if ( $trans_id ){

            $trans = self::get($trans_id);

            $fnsql->select('*', fn_Accounts::$table, array('account_id'=>$trans->account_id));

            return $fndb->get_row( $fnsql->get_query() );

        }

    }
	
	public static function remove($trans_id){
		global $fndb, $fnsql;
		
		$trans_id = intval($trans_id);
		
		if ( $trans_id ){
			
			//delete labels assoc
			$fnsql->delete(fn_Label::$table_assoc, array('trans_id'=>$trans_id));
			$fndb->execute_query( $fnsql->get_query() );

            //TODO! remove trans from account balance

            //delete attachments
            $attachments= fn_OP::get_metadata($trans_id, 'attachments');
            if( strlen($attachments) ){
                $attachments = @unserialize($attachments); foreach($attachments as $file) @unlink( rtrim(FNPATH, DIRECTORY_SEPARATOR) . fn_OP::$uploads_dir . DIRECTORY_SEPARATOR . $file );
            }

			//delete metadata
			$fnsql->delete(self::$table_meta, array('trans_id'=>$trans_id));
			$fndb->execute_query( $fnsql->get_query() );
			
			//delete trans
			$fnsql->delete(self::$table, array('trans_id'=>$trans_id)); 
			return $fndb->execute_query( $fnsql->get_query() );
			
		}
		
		return FALSE;
	}
	
	public static function apply_timely_selection($span='monthly'){
		
		global $fndb, $fnsql;
		
		if ( $span == 'yearly' or $span == 'monthly' or $span == 'daily' )
			$fnsql->datepart('sdate', 'year', 'year', self::$table);
		
		if ( $span == 'monthly' or $span == 'daily' )
			$fnsql->datepart('sdate', 'month', 'month', self::$table);
		
		if ( $span == 'daily' )
			$fnsql->datepart('sdate', 'day', 'day', self::$table);
		
	}
	
	
	public static function apply_timely_groupping($span='monthly', $additional_fields=array()){
		
		global $fndb, $fnsql;
		
		$fields = array(); 
		
		if ( $span == 'yearly' or $span == 'monthly' or $span == 'daily' )
			$fields[] = 'year';
		
		if ( $span == 'monthly' or $span == 'daily' )
			$fields[] = 'month';
		
		if ( $span == 'daily' )
			$fields[] = 'day';
		
		$fields = array_merge($fields, $additional_fields);
		
		$fnsql->group_by($fields);
		
	}

	
	public static function get_timely_sum($filters, $span="monthly"){
		
		global $fndb, $fnsql;
		
		$Totals = array();
		
		if ( isset($filters['currency_id']) ){
			$currency_id = intval($filters['currency_id']);
		}
		else {
			$currency = fn_Currency::get_default(); $currency_id = is_object($currency) ? $currency->currency_id : 0;
		}
		
		//--- first selet sum in all diferent currencies available ---//
		$sumbycc = array();
		
		$fnsql->init(SQLStatement::$SELECT);
		$fnsql->table(self::$table);
		$fnsql->distinct('currency_id');
		$fnsql->from();
		
		if ( isset($filters['labels']) )
			$fnsql->left_join(fn_Label::$table_assoc, 'trans_id', 'trans_id');
		
		self::apply_filters($filters);
		
		$Rows = $fndb->get_rows( $fnsql->get_query() );
		//--- first selet sum in all diferent currencies available ---//
		
		if ( count($Rows) ) foreach ($Rows as $row){

			//--- now get the value of the transactions in this one currency, in a timely manner ---//
			$fnsql->init(SQLStatement::$SELECT);
			$fnsql->table(self::$table);
			$fnsql->sum('value', 'ctotal');
			
			//--- apply timely filters ---//
			self::apply_timely_selection($span);
			//--- apply timely filters ---//
			
			$fnsql->from();
				
			if ( isset($filters['labels']) )
				$fnsql->left_join(fn_Label::$table_assoc, 'trans_id', 'trans_id');
				
			self::apply_filters( array_merge($filters, array('currency_id'=>$row->currency_id)) );

			self::apply_timely_groupping($span);

            //--- order by date DESC ---//
            switch($span){

                case 'yearly': $fnsql->orderby(array('year'), "", 'ASC'); break;
                case 'daily': $fnsql->orderby(array('year', 'month', 'day'), "", 'ASC'); break;

               default: $fnsql->orderby(array('year', 'month'), "", 'ASC');

            }
            //--- order by date DESC ---//

			$Groups = $fndb->get_rows( $fnsql->get_query() );
			
			if ( count($Groups) ) foreach ($Groups as $group){

                $group->year = isset($group->year) ? $group->year : '';
                $group->month = isset($group->month) ? $group->month : '';
                $group->day = isset($group->day) ? $group->day : '';

				$sum = floatval($group->ctotal);
				
				if ( ( $sum > 0 ) and ( $currency_id !=  $row->currency_id) ) //convert it
					$sum = fn_Currency::convert($sum, $row->currency_id, $currency_id, 'id');
				
				$total = array('sum'=>$sum, 'year'=>$group->year);
				$hash = ( $group->year . fn_Util::add_leading_zeros($group->month, 2, 1) . fn_Util::add_leading_zeros($group->day, 2, 1) );
				
				if ( isset($group->month) and strlen($group->month) )
					$total['month'] = $group->month;
				
				if ( isset($group->day) and strlen($group->day))
					$total['day'] = $group->day;
				
				if ( strlen($hash) and isset($Totals[$hash]) )
					$Totals[$hash]['sum']+= $total['sum'];
				else 
					$Totals[$hash] = $total;
				
			}
			
		}
		
		ksort($Totals, SORT_DESC);
		
		return $Totals;
		
	}
	
	
	public static function get_sum($filters){

        global $fndb, $fnsql;

        $Total = 0;

        //--- first select sums in all different currencies available ---//

        if ( isset($filters['currency_id']) ){
            $currency_id = intval($filters['currency_id']);
        }
        else {
            $currency = Currency::get_default(); $currency_id = is_object( $currency ) ? $currency->currency_id : 0;
        }

        $fnsql->init(SQLStatement::$SELECT);
        $fnsql->table(self::$table);
        $fnsql->distinct('currency_id');
        $fnsql->from();

        if ( isset($filters['labels']) or isset($filters['ignore_labels']) )
            $fnsql->left_join(Label::$table_assoc, 'trans_id', 'trans_id');

        self::apply_filters($filters);

        $Rows = $fndb->get_rows( $fnsql->get_query() );

        if ( count($Rows) ) foreach ($Rows as $row){

            //--- now get the value of the transactions in this one currency ---//
            $fnsql->init(SQLStatement::$SELECT);
            $fnsql->table(self::$table);
            $fnsql->sum('value', 'ctotal');
            $fnsql->from();

            if ( isset($filters['labels']) )
                $fnsql->left_join(Label::$table_assoc, 'trans_id', 'trans_id');

            self::apply_filters( array_merge($filters, array('currency_id'=>$row->currency_id)) );

            $sum = $fndb->get_row( $fnsql->get_query() );
            $sum = floatval($sum->ctotal);

            if ( ( $sum > 0 ) and ( $currency_id !=  $row->currency_id) ) //convert it
                $sum = Currency::convert($sum, $row->currency_id, $currency_id, 'id');

            $Total+= $sum;

            //--- now get the value of the transactions in this one currency ---//
        }

        //--- first select sums in all different currencies available ---//

        return $Total;

	}

    public static function get_balance($filters=NULL, $include_accounts=TRUE){

        if( empty($filters) )
            $filters = array('startdate'=>0, 'enddate'=>0);

        if( $include_accounts ){
            $filters['ignore_accounts'] = true;
        }


        $Income		= self::get_sum(array_merge($filters, array('type'=>FN_OP_IN)));
        $Outcome	= self::get_sum(array_merge($filters, array('type'=>FN_OP_OUT)));

        if( $include_accounts ){
            //--- include accounts deposits in calculations ---//

            if( isset($filters['currency_id']) )
                $currency_id = $filters['currency_id'];
            else{
                $Currency = Currency::get_default(); $currency_id = is_object($Currency) ? $Currency->currency_id : 0;
            }

            $Income+= Accounts::get_total_balance($currency_id);

            //--- include accounts deposits in calculations ---//
        }

        return floatval($Income - $Outcome);

    }
	
	public static function consistent_flow($type, $value, $currency_id, $date=FALSE){
		
		$defaultCC = Currency::get_default();

		if ( $currency_id == $defaultCC->currency_id )
			return array('value'=>$value, 'currency_id'=>$currency_id);
		
		if( $type == FN_OP_IN ){
		
			//--- convert to accepted in currencies  ---//
			$currencies_in = Settings::get('op_currencies_in');
			$currencies_in = @explode(",", $currencies_in);
		
			if ( is_array($currencies_in)  and count($currencies_in) ){
				if ( !in_array($currency_id, $currencies_in) ){  //convert in default currency
					$value 			= Currency::historically_convert($value, $currency_id, $defaultCC->currency_id, $date, 'id');
					$currency_id	= $defaultCC->currency_id;
				}
			}
			else{ //no currencies in defined, convert to default currency
				$value 			= Currency::historically_convert($value, $currency_id, $defaultCC->currency_id, $date, 'id');
				$currency_id	= $defaultCC->currency_id;
			}
			//--- convert to accepted in currencies  ---//

		}
		else{
			$value 			= Currency::historically_convert($value, $currency_id, $defaultCC->currency_id, $date, 'id');
			$currency_id	= $defaultCC->currency_id;
		}

		return array('value'=>$value, 'currency_id'=>$currency_id);
		
	}

    /**
     * Consistent flow by account
     * @param $account_id the account's ID
     * @param $value the value of the transaction
     * @param $currency_id transaction's currency id
     * @param mixed $date [optional] either null/false or a string representing a date
     * @return array
     */
    public static function consistent_flow_by_account($account_id, $value, $currency_id, $date=FALSE){

        $account   = Accounts::get( $account_id );
        $defaultCC = Currency::get_default();

        if( $account ) {

            if( $currency_id != $account->account_currency_id ){
                 //--- we will convert it to the currency accepted by the account ---//
                 $value 		= Currency::historically_convert($value, $currency_id, $account->account_currency_id, $date, 'id');
                 $currency_id	= $account->account_currency_id;
                 //--- we will convert it to the currency accepted by the account ---//
             }

            return array('account_id'=>$account->account_id, 'value'=>$value, 'currency_id'=>$currency_id);

        }

        return array('value'=>$value, 'currency_id'=>$currency_id);

    }


	public static function extract_op_vars( $string, $separator=" ", $labels_sep=","){
		
		//in 100 usd PayPal
		//out 70.12 EUR ACME INC,Hosting LLC,newsite.com
        //28/7/2013 in 270 GBP HostingProvider INC,Hosting LLC,newsite.com,newsite2.com
        //19/10/2013 in 190 GBP #PayPal HostingProvider INC,Hosting LLC,newsite.com,newsite2.com
        //out 10 EUR #GoogleWallet

		$string = trim($string);
        $labels = "";
		
		$extracted = array(
            'time'    => NULL,                           //optional time of the transaction
			'optype'	=> FALSE,                         //mandatory
			'value'	=> FALSE,	                       //mandatory
			'ccode'	=> FALSE,	                       //currency code mandatory
			'labels'	=> "",                                //optional
            'account'=> ""                                //optional
		);
		
		$vars = @explode($separator, $string); $k=0;
		$ioff = $voff = 0;

		if ( count($vars)  and is_array($vars)) foreach ($vars as $var){ $k++;

            //--- check if we have a date specified ---//
            if( ( $k == 1 ) and ( strpos($var, "/") > 0 ) ){

                $date = trim($var, "/");
                $date = explode("/", $date);

                $timestamp = strtotime("{$date[2]}-{$date[1]}-{$date[0]}");

                if( $timestamp > 0){
                    $extracted['time'] = $timestamp; $ioff++;
                }

            }
            //--- check if we have a date specified ---//

            //--- check if we have an account specified ---//
            if( ( $k > ( 3 + $ioff ) ) and empty($extracted['account'] ) ) if( strpos($var, '#') === FALSE ); else{
                $extracted['account'] = str_replace('#', "", $var); $voff++;
            }
            //--- check if we have an account specified ---//

			if ( $k >  ( 3 + $ioff + $voff ) ){ //labels zone
				$labels.=" {$var}"; continue;
			}
			
			if ( $k == ( 1+ $ioff ) ) $extracted['optype'] = in_array(strtolower($var), array(FN_OP_IN, FN_OP_OUT)) ? strtolower($var) : FALSE;
			if ( $k == ( 2+ $ioff ) ) $extracted['value']  = floatval($var);
			if ( $k == ( 3+ $ioff ) ) $extracted['ccode']  = strtoupper($var);
			
		}

		if ( strlen($labels) ) $extracted['labels'] = @explode($labels_sep, trim($labels));
		
		if ( $extracted['optype'] and $extracted['value'] and $extracted['ccode'] ){
			return $extracted;
		}
		
		return FALSE;
		
	}
	
	public static function associate_label($trans_id, $label_id){

		global $fndb, $fnsql; 
		
		$trans_id = intval($trans_id);
		$label_id = intval($label_id);
		
		if ( $label_id and $trans_id ){

            //--- if the label has a parent also associate the parent label ---//

            $label = fn_Label::get_by_id($label_id); if( $label and isset($label->parent_id) and ( $label->parent_id > 0 ) ) {

                $fnsql->insert(fn_Label::$table_assoc, array('label_id'=>$label->parent_id, 'trans_id'=>$trans_id));
                $fndb->execute_query( $fnsql->get_query() );

            }

            //--- if the label has a parent also associate the parent label ---//

			$fnsql->insert(fn_Label::$table_assoc, array('label_id'=>$label_id, 'trans_id'=>$trans_id));

			return $fndb->execute_query( $fnsql->get_query() );

		}
		
	}
	
	
	public static function save_metadata($trans_id, $key, $value, $multiple=FALSE, $overwrite=TRUE){
		
		global $fndb, $fnsql;
		
		$trans_id 	= intval($trans_id);
		$key	  		= $fndb->escape($key);
		$value	 	= $fndb->escape($value);
		
		$fnsql->select('meta_id, meta_value', self::$table_meta, array('meta_key'=>$key, 'trans_id'=>$trans_id));
		
		$exists      = $fndb->get_row( $fnsql->get_query() );
        $mvalue    = FALSE;

        if( $multiple ){  //value will be saved as a serialized array
            $mvalue = array(); $mvalue[]= $value; $mvalue = @serialize($mvalue);
        }

		if( count($exists) and isset($exists->meta_id) ){

            if($mvalue ) {
                if( !$overwrite ) {
                    $previous = @unserialize($exists->meta_value); if( is_array($previous)){ $previous[] = $value; $value=@serialize($previous); } else $value = $mvalue;
                }
                else $value = $mvalue;
            }

			$fnsql->update(self::$table_meta, array('meta_value'=>$value), array('meta_key'=>$key, 'trans_id'=>$trans_id));

        }else{
			if($mvalue) $value = $mvalue; $fnsql->insert(self::$table_meta, array('meta_key'=>$key, 'trans_id'=>$trans_id, 'meta_value'=>$value));
        }
		
		return $fndb->execute_query( $fnsql->get_query() );
		
	}
	
	
	public static function get_metadata($trans_id, $key, $default=NULL){
		
		global $fndb, $fnsql;
		
		$trans_id 	= intval($trans_id);
		$key	  		= $fndb->escape($key);
		
		$fnsql->select('meta_value', self::$table_meta, array('meta_key'=>$key, 'trans_id'=>$trans_id));
		
		$Meta = $fndb->get_row( $fnsql->get_query() );
		
		if( count($Meta) and isset($Meta->meta_value) )
			return $Meta->meta_value;
		
		return $default;
		
	}

    /**
     * Creates a new transaction from the parameters specified
     * @param $type IN or OUT
     * @param $value
     * @param $ccode
     * @param $account
     * @param array $labels
     * @param null $timestamp
     * @param array $metadata
     * @return bool
     */
    public static function create($type, $value, $ccode, $account, $labels=array(), $timestamp=NULL, $metadata=array()){
		
		global $fndb, $fnsql;
		
		$type = strtolower($type);
		
		$currency      = fn_Currency::get_by_code($ccode);
		$value	       = floatval($value);
		$type           = in_array($type, array(FN_OP_IN, FN_OP_OUT)) ? $type : FALSE;
        $date           = empty($timestamp) ? date(FN_MYSQL_DATE) : date(FN_MYSQL_DATE, $timestamp);

        $currency_id = isset($currency->currency_id) ? $currency->currency_id : 0;

		if ( $currency and isset($currency->currency_id) and $type and $value){

            $preflow = array('currency_id'=>$currency_id, 'value'=>$value);
            $flow      = self::consistent_flow($type, $value, $currency->currency_id, $date);
			
			extract($flow); //import vars
			
			$fnsql->insert(self::$table, array('optype'=>$type, 'value'=>$value, 'currency_id'=>$currency_id, 'sdate'=>$date) );

			if ( $fndb->execute_query( $fnsql->get_query() ) ){
				
				$trans_id = $fndb->last_insert_id;
				$labelsIds= array();

                //--- set original currency id ---//
                if( $preflow['currency_id'] != $currency_id ) {
                    self::save_metadata($trans_id, 'initial_currency_id', $preflow['currency_id']);
                    self::save_metadata($trans_id, 'initial_value', $preflow['value']);
                }
                //--- set original currency id ---//

				//--- add labels and set associations ---//
				if ( count($labels) ) foreach ($labels as $label){
					
					$slug		 = fn_Label::get_slug($label);
					$dblabel = fn_Label::get($slug);
					$label_id	= 0;
					
					if ( count($dblabel) and isset( $dblabel->label_id ) ) //label already in db
						$label_id = $dblabel->label_id;
				
					elseif ( fn_Label::add($label) ) 							//create the new label
						 $label_id = $fndb->last_insert_id;
					
					if ( $label_id ){

                        self::associate_label($trans_id, $label_id); //associate the label

                        $labelsIds[] = $label_id;

                    }
					
				}

                //--- in an account has been specified, add transaction to the account ---//
                if($account and strlen($account) ){

                    $slug       = fn_Util::make_slug($account);
                    $Account = fn_Accounts::get_by_slug($slug);

                    $account_id = isset($Account->account_id) ? $Account->account_id : 0;

                    if( empty($account_id) )
                        $account_id = fn_Accounts::add(array('holder_name'=>$account, 'account_currency_id'=>$currency_id));

                    if( $account_id )
                        fn_Accounts::add_trans($account_id, $trans_id);

                }
                //--- in an account has been specified, add transaction to the account ---//

                //--- add metadata ---//
				if ( $trans_id and count($metadata) ) foreach ($metadata as $meta=>$value) self::save_metadata($trans_id, $meta, $value);
                //--- add metadata ---//
				
			}
			
			return $trans_id;
			
		}
		
		return FALSE;
		
	}
	
	
	public static function get($trans_id){
		
		global $fndb, $fnsql;
		
		$trans_id = intval($trans_id);
		
		if($trans_id){
			$fnsql->select('*', self::$table, array('trans_id'=>$trans_id));
			
			return $fndb->get_row( $fnsql->get_query() );
		}
		
		
		return null;
	}

    public static function update($trans_id, $data){
        global $fndb, $fnsql;

        $trans_id = intval($trans_id);
        $data      = $fndb->escape($data);

        if( $trans_id and count($data) ){
            $fnsql->update(self::$table, $data, array('trans_id'=>$trans_id)); return $fndb->execute_query( $fnsql->get_query() );
        }

        return FALSE;

    }

    public static function add_attachment($trans_id, $File){

        $folder     = FN_UPLOADS_DIR;
        $filename = ( 'attachment-' . $trans_id . '-' . fn_Util::get_rand_string(7) );
        $uploaded = fn_Util::file_upload($File, $folder, $filename);

        if( is_array($uploaded) and ( $uploaded['success'] ) ){

            //--- save attachments and their original names ---//
            self::save_metadata($trans_id, 'attachments', basename($uploaded['msg']), TRUE, FALSE);
            self::save_metadata($trans_id, 'attachments_names', $File['name'], TRUE, FALSE);
            //--- save attachments and their original names ---//

            return self::$attached;
        }

        return $uploaded['msg'];

    }

    public static function get_attachment_link($attachment){
        return ( rtrim(FN_URL, "/") . self::$uploads_dir . "/" . trim($attachment, "/") );
    }

    public static function get_attachment_path($attachment){
        return ( rtrim(FNPATH, "/") . self::$uploads_dir . "/" . trim($attachment, "/") );
    }

    public static function get_filter_readable_string($vars, $before='', $after='', $date_format='j F Y'){

        if( empty($vars) ) $vars = $_GET;

        $readable = "";
        $period    = "";

        if( empty($vars['enddate']) or ( strtotime($vars['enddate']) > time() ) ){
            unset($vars['startdate']);
            unset($vars['enddate']);
        }else{

            $sd = date($date_format, @strtotime($vars['startdate']));
            $ed= date($date_format, @strtotime($vars['enddate']));

            $sd = ('<span class="date sdate">' . fn_UI::translate_date($sd) . '</span>' );
            $ed= ('<span class="date edate">' . fn_UI::translate_date($ed) . '</span>');

            $period = " pe perioada {$sd} - {$ed}";
        }

        if( count($vars) ){

            if( isset($vars['type']) ){
                $readable.= $vars['type'] == FN_OP_IN ? '<span class="type">venit</span>' : '<span class="type">cheltuieli</span>';
            }

            if( isset($vars['currency_id']) ){
                $currency = fn_Currency::get( $vars['currency_id'] );
                $lcurrency= ('<span class="currency">' . $currency->ccode . '</span>');

                $readable.= " &#238;n moneda {$lcurrency} ";
            }

            if( isset($vars['accounts']) ){

                $readable.= count($vars['accounts']) > 1 ? " din conturile: " : "  din contul ";

                if( count($vars['accounts']) ) foreach($vars['accounts'] as $id) $accounts[] = fn_Accounts::get($id);

                if( count($accounts) ) foreach($accounts as $account){
                    $daccount  = ( '<span class="llabel">' . fn_UI::esc_html( $account->holder_name ) . '</span>' ); $readable.= ($daccount . ", ");
                }

                $readable = trim(trim($readable), ",");
                $cnt         = substr_count($readable, ", ");

                if( $cnt ) $readable = str_replace(", {$daccount}", " si {$daccount}", $readable);

                $readable.="; ";
            }

            $readable.= $period;

            if( isset($vars['labels']) ){

                $readable.= count($vars['labels']) > 1 ? " cu etichetele: " : "  cu eticheta: ";

                if( count($vars['labels']) ) foreach($vars['labels'] as $id) $labels[] = fn_Label::get_by($id, 'id');

                if( count($labels) ) foreach($labels as $label){
                    $llabel     = ( '<span class="llabel">' . fn_UI::esc_html( $label->title ) . '</span>' ); $readable.= ($llabel . ", ");
                }

                $readable = trim(trim($readable), ",");
                $cnt         = substr_count($readable, ", ");

                if( $cnt ) $readable = str_replace(", {$llabel}", " sau {$llabel}", $readable);

                $readable.=";";

            }

        }

        return strlen($readable) ? ( $before . trim($readable) . $after ) : "";

    }

    public static function get_export_array(){

        global $fndb, $fnsql;

        $Export = array();

        $fnsql->select('*', self::$table); $Rows = $fndb->get_rows( $fnsql->get_query() );

        if( $Rows and count($Rows) ) foreach($Rows as $row){
            $trans = array();

            //--- prepare metadata ---//
            $metadata = array();

            $metadata['details']          = self::get_metadata($row->trans_id, 'details' , "");
            $metadata['attachments'] = self::get_metadata($row->trans_id, 'attachments', "");
            $metadata['attachments_names'] = self::get_metadata($row->trans_id, 'attachments_names', "");

            $metadata = fn_Util::clean_array($metadata);

            if( count($metadata) ) {
                $encodedmeta = array(); foreach($metadata as $key=>$value) $encodedmeta[$key] = base64_encode($value);
            }
            else $encodedmeta = array();

            $metadata = @serialize($encodedmeta);

            //--- prepare metadata ---//

            //--- prepare associated labels ---//
            $fnsql->select('label_id', fn_Label::$table_assoc, array('trans_id'=>$row->trans_id));
            $labels = $fndb->get_rows( $fnsql->get_query() );

            $labelsList = array(); if( $labels and count($labels) ) foreach($labels as $label) $labelsList[] = $label->label_id;

            $labelsIDs = count($labelsList) ? implode(",", $labelsList) : "";
            //--- prepare associated labels ---//

            $trans['ID']                = $row->trans_id;      //keep the trans id for reference
            $trans['cid']              = $row->currency_id;//keep the currency id for reference
            $trans['type']            = $row->optype;
            $trans['value']          = $row->value;
            $trans['date']           = $row->sdate;
            $trans['comments']   = $row->comments;
            $trans['metadata']    = $metadata;
            $trans['labels']         = $labelsIDs;
            $trans['account']      = $row->account_id;

            $Export[] = $trans;
        }

        return $Export;

    }


    /**
     * @param $Transactions
     * @param $currenciesIDs
     * @param $labelsIDs
     * @param array $accountsIDs
     * @return int number of transactions imported
     */
    public static function import_osmxml($Transactions, $currenciesIDs, $labelsIDs, $accountsIDs=array()){

        $imported=0;

        if( isset($Transactions->transaction) ) foreach($Transactions->transaction as $op){

            $id = intval($op->ID); $cid = intval($op->cid);

            $currency_id = isset( $currenciesIDs[$cid] ) ? intval( $currenciesIDs[$cid] ) : 0;

            if( empty($currency_id) ) continue; //entries without a currency ID are invalid

            $type         = $op->type == FN_OP_IN ? FN_OP_IN : FN_OP_OUT;
            $value        = floatval( $op->value );
            $comments = strval($op->comments);
            $date         = $op->date;

            $labels     = strval( $op->labels );
            $labels     = strlen( $labels ) ? @explode(",", $labels) : array();

            $ea_id = intval( $op->account ); //account id

            $metadata = strlen($op->metadata) ? @unserialize($op->metadata) : array();

            $trans_id = self::add($type, $value, $currency_id, $comments, $date);

            if( $trans_id ){

                //associate labels
                if( count($labels) ) foreach($labels as $eid){ $eid = intval($eid);
                    $label_id =  isset( $labelsIDs[$eid] ) ? intval( $labelsIDs[$eid] ) : 0; if( $label_id ) self::associate_label($trans_id, $label_id);
                }

                //add trans to account
                if($ea_id and count($accountsIDs)) {
                    $account_id = isset($accountsIDs[$ea_id]) ? $accountsIDs[$ea_id] : 0; if( $account_id ) fn_Accounts::add_trans($account_id, $trans_id);
                }

                //save metadata
                if( count($metadata) ) foreach($metadata as $key=>$serialized){
                    $value = base64_decode($serialized); if(strlen($value)) self::save_metadata($trans_id, $key, $value);
                }

                $imported++;
            }

        }

        return $imported;

    }
	
}