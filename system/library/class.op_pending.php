<?php
/**
 * FinFlow 1.0 - Pending Operations Class
 * @author Adrian S. (http://adrian.silimon.eu/)
 * @version 1.0
 */

namespace FinFlow;

class OP_Pending{

	const TYPE_IN  = OP::TYPE_IN;
	const TYPE_OUT = OP::TYPE_OUT;

    public static $table  = 'fn_op_pending';

    /**
     * The SQL Query template used to build the compound sum select query
     * @var string
     */
    public static $PeriodSelectSQL = "

            SELECT

            `trans_id`,
            `root_id`,
            `recurring`,
            `value`,
            `optype`,
            `currency_id`,
            `sdate`,
            `fdate`,
            `metadata`,

            `c_days` AS `period_days`,

            `instances` AS `period_instances`,

            (`instances` * `value`) AS `period_total`

            FROM (

                SELECT

                    *,

                    ( `day_count_till_datepay` - ABS(`day_count_till_enable`) + `day_count_topay` ) AS `c_days`,

                    (
                      CASE
                        WHEN `recurring` = 'no'      	THEN 1
                        WHEN `recurring` = 'daily'      THEN ( FLOOR( ( `day_count_till_datepay` - ABS(`day_count_till_enable`) + `day_count_topay` ) / 1 ) )
                        WHEN `recurring` = 'monthly' THEN ( FLOOR( ( `day_count_till_datepay` - ABS(`day_count_till_enable`) + `day_count_topay` ) / 30 ) )
                        WHEN `recurring` = 'yearly'    THEN ( FLOOR( ( `day_count_till_datepay` - ABS(`day_count_till_enable`) + `day_count_topay` ) / 365 ) )
                      END
                    )

                    AS `instances`

                    FROM (

                        SELECT

                        `trans_id`,
                        `root_id`,
                        `value`,
                        `optype`,
                        `currency_id`,
                        `sdate`,
                        `fdate`,
                        `metadata`,
                        `recurring`,

                        DATEDIFF( `fdate` , '{datestart}' ) AS `day_count_till_datepay` ,

                        DATEDIFF( '{dateend}' , `fdate` ) AS `day_count_topay` ,

                        (
                            CASE
                                WHEN `sdate` <= '{datestart}' THEN 0
                                WHEN `sdate` >  '{datestart}' THEN DATEDIFF( `sdate` , '{datestart}' )
                            END
                        )

                        AS `day_count_till_enable`

                        FROM `{table}`

                        {conditions}

                        {ordering}

                        {limit}

                ) AS Q

        ) AS R";

    /**
     * Add a single pending operation
     * @param $type
     * @param int $value
     * @param int $currency_id
     * @param string $recurring
     * @param array $metadata
     * @param string $date
     * @return int|bool
     */
    public static function add($type, $value=1, $currency_id=1, $recurring='no', $metadata=array(), $date=NULL, $user_id=0, $contact_id=0){

        global $fndb, $fnsql;

        $type = ( $type == self::TYPE_IN ) ? self::TYPE_IN : self::TYPE_OUT;
        $value= floatval( $value );

	    $user_id = $user_id ? intval( $user_id ) : User::get_current_user_id();

        $timestamp = empty($date) ? time() : @strtotime($date);

        $sdate = date('Y-m-d 00:00:01', self::get_past_recurring_time($timestamp, $recurring));
        $fdate = date('Y-m-d 00:00:01', $timestamp);

        $metadata  = $fndb->escape( @serialize($metadata) );
        $recurring = $fndb->escape( strtolower( $recurring ) );

        $data = array(
	        'optype'        => $type,
	        'user_id'       => $user_id,
	        'value'         => $value,
	        'currency_id'   => $currency_id,
	        'recurring'     => $recurring,
	        'metadata'      => $metadata,
	        'sdate'         => $sdate,
	        'fdate'         => $fdate
        );

        $fnsql->insert(self::$table,  $data);

        if( $fndb->execute_query( $fnsql->get_query() ) ) return $fndb->last_insert_id;

        return FALSE;

    }

    public static function add_file($trans_id, $File){

        $folder     = FN_UPLOADS_DIR;
        $filename = ( 'attachment-pending-' . $trans_id . '-' . Util::get_rand_string(7) );
        $uploaded = Util::file_upload($File, $folder, $filename);

        if( is_array($uploaded) and ( $uploaded['success'] ) )
	        return ('@' . $uploaded['filename']);

        return $uploaded['msg'];

    }

    public static function add_children($root_id, $data=null){

        global $fndb, $fnsql; $today = date('Y-m-d 00:00:01');

        if( empty($data) ) {
            $data = self::get($root_id); $data = @get_object_vars($data);
        }

        if( is_array($data) and count($data) ){

            unset( $data['trans_id'] );

            //--- add recurring metadata ---//
            $metadata = @unserialize( $data['metadata'] );

            $metadata['x_recurring'] = $data['recurring'];
            //--- add recurring metadata ---//

            $data['root_id']    = intval($root_id);
            $data['sdate']       = date(FN_MYSQL_DATE);
            $data['recurring']  = 'no';

            $data['metadata'] = @serialize( $metadata );

            $fnsql->insert(self::$table,  $data); if( $fndb->execute_query( $fnsql->get_query() ) ) return $fndb->last_insert_id;

        }

        return false;
    }

    public static function get($trans){

        global $fndb, $fnsql;

        if( is_object( $trans ) and isset( $trans->trans_id) ) return $trans;

        $trans_id = intval($trans);

        if($trans_id){
            $fnsql->select('*', self::$table, array('trans_id'=>$trans_id));

            return $fndb->get_row( $fnsql->get_query() );
        }


        return NULL;
    }

    public static function confirm( $transaction, $instances=1 ){

        global $fndb, $fnsql; $trans = self::get($transaction);

        if( $trans and isset($trans->value) ){

            if( $trans->recurring != 'no' ){

                $t_units    = self::get_recurring_time_unit( $trans->recurring, $instances );
                $t_singular= self::get_recurring_time_unit($trans->recurring, 1);

                $fr_timestamp = @strtotime("+{$instances} {$t_units}", @strtotime($trans->fdate));
                $sr_timestamp = @strtotime("-1 {$t_singular}", $fr_timestamp);

                $fdate            = date(FN_MYSQL_DATE, $fr_timestamp); //next date of transaction instance overdue
                $sdate            = date(FN_MYSQL_DATE, $sr_timestamp); //previous date of instance overdue

                if( self::update($trans->trans_id, array('fdate'=>$fdate, 'sdate'=>$sdate)) ); else return false;

            }
            else{
                //--- remove the pending transaction ---//
                $fnsql->delete(self::$table, array('trans_id'=>$trans->trans_id)); $fndb->execute_query( $fnsql->get_query() );
                //--- remove the pending transaction ---//
            }

            //----- remove all children/instances up until the date ---//
            $fnsql->delete(self::$table);

            $fnsql->condition('root_id', '=', $trans->trans_id);
            //$fnsql->condition('recurring', '=', 'no'); //We don't need this
            $fnsql->condition('fdate', '<', $fdate);

            $fnsql->conditions_ready();

            $fndb->execute_query( $fnsql->get_query() );

            //----- remove all children/instances up until the date ---//

            //--- add the transaction to live transactions ---//

            $value = floatval($trans->value * $instances);

            $comments     = self::get_metdata($trans, 'comments', null);
            $labels           = self::get_metdata($trans, 'labels', array());
            $account_id    = self::get_metdata($trans, 'account_id', 0);
            $contact_id    = self::get_metdata($trans, 'contact_id', 0);

            $attachments= fn_OP_Pending::get_metdata($trans, 'attachments');

            if( $op_trans_id = fn_OP::add($trans->optype, $value, $trans->currency_id, $comments) ){

                if( count($labels) ) foreach($labels as $label_id)
                    if( fn_Label::get_by($label_id, 'id') ) fn_OP::associate_label($op_trans_id, $label_id);

                if( $account_id and fn_Accounts::get($account_id))
                    fn_Accounts::add_trans($account_id, $op_trans_id);

                if( $contact_id )
                    fn_Contacts::assoc_trans($contact_id, $op_trans_id);

                if( is_array($attachments) and count($attachments) ){

                    $attachmentsNames = self::get_metdata($trans, 'attachments_names', $attachments);

                    fn_OP::save_metadata($op_trans_id, 'attachments', @serialize($attachments));
                    fn_OP::save_metadata($op_trans_id, 'attachments_names', @serialize($attachmentsNames));

                }

                //--- add the transaction to live transactions ---//

                return $op_trans_id;

            }

        }

        return false;

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

    /**
     * Clears children/instances of a transaction
     * @param $transaction
     * @param int $instances
     * @return bool|void
     */
    public static function clear($transaction, $instances=1){

        global $fndb, $fnsql; $trans = self::get($transaction);

        if( $trans and isset($trans->value) ){

            if( $trans->recurring != 'no' ){

                $t_units = self::get_recurring_time_unit( $trans->recurring, $instances );

                $fr_timestamp = @strtotime("+{$instances} {$t_units}", @strtotime($trans->fdate));
                $fdate            = date(FN_MYSQL_DATE, $fr_timestamp); //calculate next future date of transaction

                if( self::update($trans->trans_id, array('fdate'=>$fdate)) ); else return false;

            }
            else return self::remove($trans);

            //----- remove all children/instances up until the date ---//
            $fnsql->delete(self::$table);

            $fnsql->condition('root_id', '=', $trans->trans_id);
            //$fnsql->condition('recurring', '=', 'no'); //Don't depend on recurring or not
            $fnsql->condition('fdate', '<', $fdate);

            $fnsql->conditions_ready();

            return $fndb->execute_query( $fnsql->get_query() );

            //----- remove all children/instances up until the date ---//
        }

        return false;

    }

    public static function remove($transaction){

        global $fndb, $fnsql; $trans = self::get($transaction);

        if( $trans and isset($trans->value) ){

            //--- remove all children ---//
            $fnsql->delete(self::$table, array('root_id'=>$trans->trans_id)); $fndb->execute_query( $fnsql->get_query() );
            //--- remove all children ---//

            $fnsql->delete(self::$table, array('trans_id'=>$trans->trans_id));

            return $fndb->execute_query( $fnsql->get_query() );

        }

        return false;
    }

    public static function get_labels( $transaction ){

        $transaction = self::get($transaction); $labels = array();

        $metadata = @unserialize($transaction->metadata); if( isset($metadata['labels']) and count($metadata['labels']) ){
            foreach ($metadata['labels'] as $label_id) $labels[] = fn_Label::get_by($label_id, 'id');
        }

        return $labels;
    }

    public static function get_account( $transaction ){

        $transaction = self::get($transaction); $metadata = @unserialize($transaction->metadata);

        if( isset( $metadata['account_id'] ) )
            return fn_Accounts::get( $metadata['account_id'] );

        return null;
    }

    /**
     * Extracts meta data for a transaction
     * @param $transaction
     * @param $key
     * @param null $default
     * @return array|null
     */
    public static function get_metdata( $transaction, $key, $default=null ){

        $transaction = self::get($transaction); $metadata = @unserialize( $transaction->metadata );

        $value = isset( $metadata[$key] ) ? $metadata[$key] : null;

        if( $key == 'attachments' )
            $value = @array_values( $metadata['files'] );

        if( $key == 'attachments_names' )
            $value = @array_keys( $metadata['files'] );

        return empty($value) ? $default : $value;

    }

    public static function get_roots($offset=0, $limit=125){

        global $fndb, $fnsql;

        $offset = intval($offset);
        $limit   = intval($limit);

        $fnsql->select('*', self::$table, array('root_id'=>0));
        $fnsql->orderby('sdate', 'ASC');
        $fnsql->limit($offset, $limit);

        return $fndb->get_rows( $fnsql->get_query() );

    }

    /**
     * @param $filters
     * @return float|int
     * @deprecated use get_sum() instead
     */
    public static function get_children_sum( $filters ){ //TODO deprecate

        global $fndb, $fnsql;

        $Total = 0;

        //--- first select sums in all different currencies available ---//

        if ( isset($filters['currency_id']) ){
            $currency_id = intval($filters['currency_id']);
        }
        else {
            $currency = fn_Currency::get_default(); $currency_id = $currency->currency_id;
        }

        $sumbycc = array();

        $fnsql->init(SQLStatement::$SELECT);
        $fnsql->table(self::$table);
        $fnsql->distinct('currency_id');
        $fnsql->from();

        self::apply_filters($filters);

        $Rows = $fndb->get_rows( $fnsql->get_query() );

        if ( count($Rows) ) foreach ($Rows as $row){

            //--- now get the value of the transactions in this one currency ---//
            $fnsql->init(SQLStatement::$SELECT);
            $fnsql->table(self::$table);
            $fnsql->sum('value', 'ctotal');
            $fnsql->from();

            self::apply_filters( array_merge($filters, array('currency_id'=>$row->currency_id)) );

            $sum = $fndb->get_row( $fnsql->get_query() );
            $sum = floatval($sum->ctotal);

            if ( ( $sum > 0 ) and ( $currency_id !=  $row->currency_id) ) //convert it
                $sum = fn_Currency::convert($sum, $row->currency_id, $currency_id, 'id');

            $Total+= $sum;

            //--- now get the value of the transactions in this one currency ---//
        }

        //--- first select sums in all different currencies available ---//

        return $Total;

    }

    public static function apply_filters($filters){

        global $fndb, $fnsql; $fnsql->resetGroupIndex();

        if ( is_object($filters) ) $filters = @get_object_vars($filters);

        if ( count($filters) and is_array($filters)){

            if ( isset($filters['type']) and $filters['type'] ){
                if ( !in_array($filters['type'], OP::getTypesArray(), TRUE) ) return array(); $fnsql->condition('optype', '=', strtolower($filters['type']));
            }

            if( isset($filters['root_id']) ){
                $fnsql->condition('root_id', '=', intval($filters['root_id']));
            }

            if( isset($filters['recurring']) and $filters['recurring'] ){

                if( !is_array($filters['recurring'] ) ) $filters['recurring']  = array($filters['recurring'] );//force to array

                $rlist = $fndb->escape(implode("','", $filters['recurring'])); $fnsql->condition('recurring', "IN", "('{$rlist}')", "AND", self::$table, false);

            }

            if( isset($filters['currency_id']) and $filters['currency_id'] ){
                $fnsql->condition('currency_id', '=', $filters['currency_id']);
            }

            if ( isset($filters['startdate']) and $filters['startdate'] ){
                $startdate = $fndb->escape($filters['startdate']); $fnsql->condition('fdate', '>=', $startdate);
            }

            if ( isset($filters['enddate']) and $filters['enddate'] ){
                $enddate = $fndb->escape($filters['enddate']); $fnsql->condition('fdate', '<=', $enddate);
            }

            if( isset($filters['recurring'] ) and $filters['recurring'] )
                $fnsql->conditions_ready(true); //Conditions ready
            else
                $fnsql->conditions_ready();

            if( isset($filters['orderby']) ){ //order by field
                $order = isset($filters['order']) ? $fndb->escape( strtoupper($filters['order']) ) :  'DESC' ; $orderby = $fndb->escape($filters['orderby']);
                $fnsql->orderby($orderby, self::$table, $order);
            }

            if ( isset($filters['start']) and isset($filters['count'])){
                $start = intval($filters['start']); $count = intval($filters['count']); $fnsql->limit($start, $count);
            }

            return true;

        }

        return false;

    }

    public static function apply_period_filters($filters, $SQLTemplate=null){

        global $fndb;

        $SQLTemplate = empty($SQLTemplate) ? self::$PeriodSelectSQL : $SQLTemplate; $filters_sql = "";

        if ( is_object($filters) ) $filters = @get_object_vars($filters);

        //--- require start date and end date ---//
        $filters['startdate'] = empty($filters['startdate']) ? date(FN_MYSQL_DATE) : $filters['startdate'];
        $filters['enddate']   = empty($filters['enddate']) ? date(FN_MYSQL_DATE, @strtotime('+30 days')) : $filters['enddate'];

        if ( count($filters) and is_array($filters)){

            if ( isset($filters['type']) and $filters['type'] ){

                if ( !in_array($filters['type'], OP::getTypesArray(), TRUE) )
	                return false; $filters['type'] = $fndb->escape($filters['type']);

	            $filters_sql[] = " AND `optype` = '{$filters['type']}' ";

            }

            if( isset($filters['root_id']) )
                $filters_sql[] = (" AND `root_id`= " .  intval($filters['root_id']) );

            if( isset($filters['recurring']) and $filters['recurring'] ){

                if( !is_array($filters['recurring'] ) ) $filters['recurring']  = array($filters['recurring'] );//force to array

                $rlist = $fndb->escape( @implode("','", $filters['recurring']) );  $filters_sql[]= (" AND `recurring` IN ('{$rlist}') ");

            }

            if( isset($filters['currency_id']) and $filters['currency_id'] ){
                $filters_sql[] = (" AND `currency_id`= " .  intval($filters['currency_id']) );
            }

            if ( isset($filters['startdate']) and $filters['startdate'] ){
                $filters['startdate'] = $fndb->escape($filters['startdate']); $filters_sql[] = (" AND `fdate`>= '{$filters['startdate']}'" );

                $SQLTemplate = str_replace('{datestart}', $filters['startdate'], $SQLTemplate);
            }

            if ( isset($filters['enddate']) and $filters['enddate'] ){
                $filters['enddate'] = $fndb->escape($filters['enddate']); $filters_sql[] = (" AND `fdate`<= '{$filters['enddate']}'"  );
                $SQLTemplate = str_replace('{dateend}', $filters['enddate'], $SQLTemplate);
            }

            //--- replace conditions in template ---//

            $conditions = ""; if( count($filters_sql) ) foreach($filters_sql as $filter){
                $filter = trim($filter); $conditions.= ( " " . $filter );
            }

            $conditions = trim( preg_replace('/AND/', 'WHERE', $conditions, 1) );

            $SQLTemplate = str_replace('{conditions}', $conditions, $SQLTemplate);
            //--- replace conditions in template ---//

            if( isset($filters['orderby']) ){ //order by field
                $order    = isset($filters['order']) ? $fndb->escape( strtoupper($filters['order']) ) :  'DESC' ;
                $orderby = $fndb->escape($filters['orderby']);

                $order = "ORDER BY `{$orderby}` {$order}";
            }

            if ( isset($filters['start']) and isset($filters['count'])){
                $start  = intval($filters['start']);
                $count = intval($filters['count']);

                $limit = " LIMIT {$start}, {$count}";
            }

            $SQLTemplate = isset($order) ? str_replace('{ordering}', $order, $SQLTemplate) : str_replace('{ordering}', '', $SQLTemplate);

            $SQLTemplate = isset($limit) ? str_replace('{limit}', $limit, $SQLTemplate) : str_replace('{limit}', '', $SQLTemplate) ;


            $SQLTemplate = str_replace('{table}', self::$table, $SQLTemplate);

            return $SQLTemplate;

        }

        return false;

    }

    public static function get_overdue($filters=array(), $offset=0, $limit=125){

        global $fndb, $fnsql; $today = date(FN_MYSQL_DATE);

        $offset = intval($offset);
        $limit   = intval($limit);

        $filters['startdate'] = false;
        $filters['enddate']  = $today;
        $filters['recurring'] = 'no';
        $filters['start']       = $offset;
        $filters['count']      = $limit;

        $fnsql->select('*', self::$table); self::apply_filters($filters);

        return $fndb->get_rows( $fnsql->get_query() );

    }

    public static function get_overdue_sum($filters=array()){

        global $fndb, $fnsql; $today = date(FN_MYSQL_DATE); $Currencies = array(); $total = 0;

        $filters['startdate'] = false;
        $filters['enddate']  = $today;
        $filters['recurring'] = 'no';

        if( isset($filters['start']) ) unset($filters['start']);
        if( isset($filters['count']) ) unset($filters['count']);

        if( isset($filters['order']) ) unset($filters['order']);
        if( isset($filters['orderby']) ) unset($filters['orderby']);

        if( $filters['currency_id'] ) {
            if( is_array($filters['currency_id']) ) $Currencies = $filters['currency_id']; else $Currencies[] = intval($filters['currency_id']);
        }
        else {

            $defaultCurrency = fn_Currency::get_default(); $currency_id = $defaultCurrency->currency_id;

            //--- select all distinct currencies ---//
            $fnsql->init(SQLStatement::$SELECT);
            $fnsql->table(self::$table);
            $fnsql->distinct('currency_id');
            $fnsql->from();

            self::apply_filters($filters);

            $Rows = $fndb->get_rows( $fnsql->get_query() );

            if($Rows and count($Rows)) foreach($Rows as $row) $Currencies[] = $row->currency_id;

        }

        if( count($Currencies) ) foreach($Currencies as $cc_id){

            //--- select sum in the current currency ---//

            $fnsql->init(SQLStatement::$SELECT);
            $fnsql->table(self::$table);
            $fnsql->sum('value', 'ctotal');

            $fnsql->from();

            self::apply_filters($filters);

            $sum = $fndb->get_row( $fnsql->get_query() );
            $sum = floatval($sum->ctotal);

            if ( ( $sum > 0 ) and ( $cc_id !=  $currency_id) ) //convert it
                $sum = fn_Currency::convert($sum, $cc_id, $currency_id, 'id');

            $total+= $sum;

        }

        return $total;

    }

    public static function get_period($filters){

        global $fnsql, $fndb; $today = date(FN_MYSQL_DATE);

        if( empty($filters['startdate']) ) $filters['startdate'] = $today;
        if( empty($filters['enddate']) )  $filters['enddate']  = date(FN_MYSQL_DATE, @strtotime('+30 days'));

        $filters['root_id'] = intval( $filters['root_id'] );

        if( empty( $filters['root_id'] ) ) $filters['root_id'] = '0'; //get only roots

        $PeriodSQL = self::apply_period_filters($filters);

        return $fndb->get_rows( $PeriodSQL );

    }

    public static function get_all($filters=array()){
        global $fndb, $fnsql; $fnsql->select('*', self::$table); self::apply_filters($filters); return $fndb->get_rows( $fnsql->get_query() );
    }

    /**
     * Gets compound sum for the period converted to the default currency
     * @param array $filters
     * @return float|int
     */
    public static function get_period_sum( $filters=array() ){

        $total = 0; $today = date(FN_MYSQL_DATE); global $fnsql, $fndb;

        if( isset($filters['start']) ) unset($filters['start']);
        if( isset($filters['count']) ) unset($filters['count']);

        if( isset($filters['order']) ) unset($filters['order']);
        if( isset($filters['orderby']) ) unset($filters['orderby']);

        if( empty($filters['startdate']) ) $filters['startdate'] = $today;
        if( empty($filters['enddate']) )  $filters['enddate']  = date(FN_MYSQL_DATE, @strtotime('+30 days'));

        $filters['root_id'] = intval( $filters['root_id'] );

        $days   = Util::date_diff($filters['startdate'], $filters['enddate'], 'days');    //number of days in the time span
        $months = Util::date_diff($filters['startdate'], $filters['enddate'], 'months');  //number of months in the time span
        $years  = Util::date_diff($filters['startdate'], $filters['enddate'], 'years');   //number of years in the time span

        $days   = empty($days) ? 1 : $days;
        $months = empty($months) ? 1 : $months;
        $years  = empty($years) ? 1 : $years;

        if( empty( $filters['root_id'] ) )
            $filters['root_id'] = '0'; //only roots
        else{

            $filters['root_id'] = intval($filters['root_id']);

            //--- only for a single root ---//
           $trans = self::get($filters['root_id']);

            switch( $trans->recurring ){
                case 'daily':
	                return ( Currency::convert_to_default($trans->value, $trans->currency_id) * $days ); break;

                case 'monthly':
	                return ( Currency::convert_to_default($trans->value, $trans->currency_id) * $months ); break;

                case 'yearly':
	                return ( Currency::convert_to_default($trans->value, $trans->currency_id) * $years ); break;
            }

            return Currency::convert_to_default($trans->value, $trans->currency_id);

            //--- only for a single root ---//
        }

        //$once_sum = self::get_sum(array_merge($filters, array('recurring'=>'no'))); $total+= $once_sum;

        //--- now move forward to calculate compound sum for recurring transactions ---//
        $CompoundSQL = self::apply_period_filters($filters);

        if( $CompoundSQL ){

            $currencies = self::get_distinct_currencies($filters);

            if( count($currencies) ) foreach ($currencies as $currency_id){

                $CompoundSQL = self::apply_period_filters( array_merge($filters, array('currency_id'=>$currency_id)) );

                $fnsql->init(SQLStatement::$SELECT);
                $fnsql->sum('period_total', 'total');
                $fnsql->query_append(" FROM ($CompoundSQL) AS Z  ");

                $Row = $fndb->get_row( $fnsql->get_query() );

                if( $Row and isset( $Row->total ) )
	                $total+= Currency::convert_to_default($Row->total, $currency_id);

            }

        }

        return $total;

    }

    /**
     * Extracts the sum of all transactions in the table based on filters. Note: if the filter currency_id is present it will return only the sum of transactions in that currency_id, if not present it will return the sum of the transactions in all currencies converted to default currency
     * @param array $filters
     * @return float|int
     */
    public static function get_sum( $filters=array() ){

        global $fndb, $fnsql; $Currencies = array(); $total = 0;

        if( isset($filters['start']) ) unset($filters['start']);
        if( isset($filters['count']) ) unset($filters['count']);

        if( isset($filters['order']) ) unset($filters['order']);
        if( isset($filters['orderby']) ) unset($filters['orderby']);

        if( $filters['currency_id'] ) {
            if( is_array($filters['currency_id']) ) $Currencies = $filters['currency_id']; else $Currencies[] = intval($filters['currency_id']);
        }
        else {

            $defaultCurrency = fn_Currency::get_default(); $currency_id = $defaultCurrency->currency_id;

            //--- select all distinct currencies ---//
            $fnsql->init(SQLStatement::$SELECT);
            $fnsql->table(self::$table);
            $fnsql->distinct('currency_id');
            $fnsql->from();

            self::apply_filters($filters);

            $Rows = $fndb->get_rows( $fnsql->get_query() );

            if($Rows and count($Rows)) foreach($Rows as $row) $Currencies[] = $row->currency_id;
            //--- select all distinct currencies ---//

        }

        if( count($Currencies) ) foreach($Currencies as $cc_id){

            //--- select sum in the current currency ---//

            $fnsql->init(SQLStatement::$SELECT);
            $fnsql->table(self::$table);
            $fnsql->sum('value', 'ctotal');

            $fnsql->from();

            self::apply_filters($filters);

            $sum = $fndb->get_row( $fnsql->get_query() );
            $sum = floatval($sum->ctotal);

            if ( ( $sum > 0 ) and ( $cc_id !=  $currency_id) ) //convert it
                $sum = fn_Currency::convert($sum, $cc_id, $currency_id, 'id');

            $total+= $sum;

        }

        return $total;
    }

    public static function get_all_root_children($offset=0, $limit=125){

        global $fndb, $fnsql;

        $offset = intval($offset);
        $limit   = intval($limit);

        $fnsql->select('*', self::$table);
        $fnsql->limit($offset, $limit);

        return $fndb->get_rows( $fnsql->get_query() );

    }

    public static function get_min_date($filters){

        global $fndb, $fnsql;

        if( isset($filters['start']) ) unset($filters['start']);
        if( isset($filters['count']) ) unset($filters['count']);

        if( isset($filters['order']) ) unset($filters['order']);
        if( isset($filters['orderby']) ) unset($filters['orderby']);

        if( isset($filters['startdate']) ) unset($filters['startdate']);

        //--- get min date ---//
        $fnsql->init(SQLStatement::$SELECT);
        $fnsql->table(self::$table);
        $fnsql->min('sdate', 'mindate');
        $fnsql->from();

        self::apply_filters($filters);

        $Row = $fndb->get_row( $fnsql->get_query() );

        if( isset($Row->mindate) ) return $Row->mindate;

        //--- get min date ---//

        return date(FN_MYSQL_DATE);

    }

    public static function get_distinct_currencies($filters){

        global $fndb, $fnsql; $currencies = array();

        $fnsql->init(SQLStatement::$SELECT);
        $fnsql->table(self::$table);
        $fnsql->distinct('currency_id');
        $fnsql->from();

        self::apply_filters($filters);

        $Rows = $fndb->get_rows( $fnsql->get_query() );

        if($Rows and count($Rows)) foreach($Rows as $row) $currencies[] = $row->currency_id;

        return $currencies;
    }

    public static function get_future_recurring_time($timestamp, $recurring ){

        $day = date('j', $timestamp); if( $day > 27 ) $day = 27; //reset day of month to a safe value

        $recs = array(
            'no'             => $timestamp,
            'daily'         => strtotime('+1 day', strtotime( date("Y-m-$day 00:00:01", $timestamp) )),
            'monthly'    => strtotime('+1 month', strtotime(date("Y-m-$day 00:00:01", $timestamp) )),
            'yearly'       => strtotime('+1 year', strtotime(date("Y-m-$day 00:00:01", $timestamp) ))
        );

        return intval( $recs[$recurring] );

    }

    public static function get_past_recurring_time($timestamp, $recurring ){

        $recs = array(
            'no'             => $timestamp,
            'daily'         => strtotime('-1 day', strtotime( date("Y-m-d 00:00:01", $timestamp) )),
            'monthly'    => strtotime('-1 month', strtotime(date("Y-m-d 00:00:01", $timestamp) )),
            'yearly'       => strtotime('-1 year', strtotime(date("Y-m-d 00:00:01", $timestamp) ))
        );

        return intval( $recs[$recurring] );

    }

    public static function get_interval_instances($recurring, $from, $to=null){

        $to = empty($to) ? date(FN_MYSQL_DATE) : $to;

        switch($recurring){
            case 'daily': return fn_Util::date_diff($from, $to, 'days'); break;
            case 'monthly': return fn_Util::date_diff($from, $to, 'months'); break;
            case 'yearly': return fn_Util::date_diff($from, $to, 'years'); break;

            default: return 0;
        }

    }

    public static function get_recurring_time_unit($recurring, $instances=1){
        switch($recurring){
            case 'daily': return ( $instances != 1 ? 'days' : 'day' ); break;
            case 'weekly': return ( $instances != 1 ? 'weeks' : 'week' ); break;
            case 'monthly': return ( $instances != 1 ? 'months' : 'month' ); break;
            case 'yearly': return ( $instances != 1 ? 'years' : 'year' ); break;

            default: $recurring;
        }
    }

    public static function recurring_multiplier_display($recurring, $value, $days=1, $months=1, $years=1){

        $days   = empty($days) ? '1' : $days;
        $months = empty($months) ? '1' : $months;
        $years  = empty($years) ? '1' : $years;

        switch($recurring){
            case 'daily': return ( $days . ' x ' . $value); break;
            case 'monthly': return ( $months . ' x ' . $value); break;
            case 'yearly': return ( $years . ' x ' . $value); break;

            default: return ( '1 x ' . $value);
        }

    }

}