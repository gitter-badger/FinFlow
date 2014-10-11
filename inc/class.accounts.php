<?php


class fn_Accounts{

    public static $table = 'fn_accounts';

    public static function  get_all($offset=0, $limit=25){

        global $fndb, $fnsql;

        $offset = intval($offset);
        $limit   = intval($limit);

        $fnsql->select('*', self::$table);
        $fnsql->limit($offset, $limit);

        return $fndb->get_rows( $fnsql->get_query() );

    }

    public static function get_by_slug($slug){

        global $fndb, $fnsql;

        $slug = $fndb->escape( strtolower($slug) );

        $fnsql->select('*', self::$table, array('account_slug'=>$slug));

        return $fndb->get_row( $fnsql->get_query() );

    }

    public static function get_total(){

        global $fndb, $fnsql;

        $fnsql->init(SQLStatement::$SELECT);
        $fnsql->table(self::$table);
        $fnsql->count('account_id', 'total');
        $fnsql->from();

        $Row = $fndb->get_row( $fnsql->get_query() );

        if( isset($Row->total) ) return $Row->total;

        return 0;

    }

    public static function get_balance($account_id, $currency_id=1){

        $Account = self::get($account_id);

        if( count($Account) and isset($Account->account_id) ) {
            $real_balance = ($Account->balance + $Account->balance_won) - $Account->balance_spent;
            return fn_Currency::convert($real_balance, $Account->account_currency_id, $currency_id, 'id');
        }

        return 0;
    }


    public static function get_total_balance($currency_id){

        global $fndb, $fnsql;

        $Currency = fn_Currency::get($currency_id);

        if( count($Currency) and isset($Currency->currency_id) ){

            $total = 0; $currency_id = $Currency->currency_id;

            //--- get amount(s) of accounts that have a different currency then the one specified ---//
            $fnsql->init(SQLStatement::$SELECT);
            $fnsql->table(self::$table);
            $fnsql->fields('account_id');
            $fnsql->from();

            $fnsql->condition('account_currency_id', '!=', $currency_id);

            $fnsql->conditions_ready();

            $Accounts = $fndb->get_rows( $fnsql->get_query() );

            if( count($Accounts) ) foreach($Accounts as $account) $total+= self::get_balance($account->account_id, $currency_id);

            //--- get amount(s) of accounts that have a different currency then the one specified ---//

            //--- get amount of all other accounts ---//
            $fnsql->init(SQLStatement::$SELECT);
            $fnsql->table(self::$table);

            $fnsql->sum('balance'           , 'initial');
            $fnsql->sum('balance_won'   , 'total_won');
            $fnsql->sum('balance_spent' , 'total_spent');

            $fnsql->from();

            $fnsql->condition('account_currency_id', '=', $currency_id);

            $fnsql->conditions_ready();

            $Accounts = $fndb->get_row( $fnsql->get_query() );

            $real_balance = ( floatval($Accounts->initial) + floatval($Accounts->total_won) ) - floatval($Accounts->total_spent);

            $total+= $real_balance;
            //--- get amount of all other accounts ---//

            return $total;

        }

        return 0;

    }

    public static function add($data){
        global $fndb, $fnsql;

        if( empty($data['account_slug']) ) $data['account_slug'] = fn_Util::make_slug( $data['holder_name'] );

        $data  = $fndb->escape( $data );

        $fnsql->insert(self::$table, $data);

       if( $fndb->execute_query( $fnsql->get_query() ) ) return $fndb->last_insert_id;


        return false;

    }

    public static function update($account_id, $data){
        global $fndb, $fnsql;

        $data          = $fndb->escape( $data );
        $account_id = intval($account_id);

        $fnsql->update(self::$table, $data, array('account_id'=>$account_id));

        return $fndb->execute_query( $fnsql->get_query() );

    }

    public static function remove($account_id){

        global $fndb, $fnsql;

        $account_id = intval($account_id);

        //--- remove associated transactions ---//
        $fnsql->update(fn_OP::$table, array('account_id'=>0), array('account_id'=>$account_id));
        $fndb->execute_query( $fnsql->get_query() );
        //--- remove associated transactions ---//

        $fnsql->delete(self::$table, array('account_id'=>$account_id));

        return $fndb->execute_query( $fnsql->get_query() );
    }


    public static function get($account_id){

        global $fndb, $fnsql;

        $fnsql->select('*', self::$table, array('account_id'=>intval($account_id)));

        $Row = $fndb->get_row( $fnsql->get_query() );

        if( isset( $Row->account_id ) ) return $Row;

        return NULL;

    }

    public static function has_accounts(){

        global $fndb, $fnsql;

        $fnsql->select('account_id', self::$table);

        $fnsql->limit(0, 1);

        $Rows = $fndb->get_rows( $fnsql->get_query() );

        return count($Rows) > 0;

    }

    public static function get_account_trans($account_id){

        global $fndb, $fnsql;

        $account_id = intval($account_id);

        $fnsql->select('*', fn_OP::$table, array('account_id'=>$account_id));

        return $fndb->get_rows( $fnsql->get_query() );

    }

    public static function get_sum(){

        global $fndb, $fnsql;

        $fnsql->init(SQLStatement::$SELECT);
        $fnsql->table(self::$table);
        $fnsql->sum('amount', 'total');
        $fnsql->from();

        $Row = $fndb->get_row( $fnsql->get_query() );

        if( isset($Row->total) ) return floatval($Row->total);

        return 0;

    }

    public static function find_by_slug($slug){
        global $fndb, $fnsql;

        $slug = $fndb->escape($slug);

        $fnsql->select('account_id', self::$table, array('account_slug'=>$slug));

        $Row = $fndb->get_row( $fnsql->get_query() );

        if( $Row and isset($Row->account_id) ) return $Row->account_id;

        return false;

    }

    public static function assoc_trans($account_id, $trans_id){

        $account_id = intval( $account_id );
        $trans_id     = intval( $trans_id );

        return fn_OP::update($trans_id, array('account_id'=>$account_id));

    }

    public static function add_trans($account_id, $trans_id){
        global $fndb, $fnsql;

        $account_id = intval($account_id); $account = self::get($account_id);
        $trans_id     = intval($trans_id);     $trans      = fn_OP::get( $trans_id );

        if( $account and $trans){

            $field      = ( $trans->optype == FN_OP_IN ) ? 'balance_won' : 'balance_spent';
            $previous= ( $trans->optype == FN_OP_IN ) ? floatval($account->balance_won) : floatval($account->balance_spent);

            if( $account->account_currency_id != $trans->currency_id )
                $cvalue = fn_Currency::historically_convert($trans->value, $trans->currency_id, $account->account_currency_id, $trans->sdate, 'id');
            else
                $cvalue = floatval( $trans->value );

            if( $cvalue > 0 ) {

                $value = $previous + $cvalue;

                $fnsql->update(self::$table, array($field=>$value), array('account_id'=>$account_id));

                if( $fndb->execute_query( $fnsql->get_query() ) ){
                    self::assoc_trans($account_id, $trans_id); return fn_OP::update($trans_id, array('value'=>$cvalue, 'currency_id'=>$account->account_currency_id));
                }

            }

        }

        return false;

    }


    public static function get_export_array(){

        global $fndb, $fnsql;

        $Export = array();

        $fnsql->select('*', self::$table); $Rows = $fndb->get_rows( $fnsql->get_query() );

        if( $Rows and count($Rows) ) foreach($Rows as $row){

            $account = array();

            $account['ID']           = $row->account_id;               //keep the trans id for reference
            $account['cid']         = $row->account_currency_id; //keep the currency id for reference
            $account['iban']       = $row->account_iban;
            $account['slug']        = $row->account_slug;
            $account['holder']    = $row->holder_name;
            $account['swift']      = $row->holder_swift;
            //$account['won']       = $row->balance_won; //ignored as dependent of associated transactions
            //$account['spent']     = $row->balance_spent;
            $account['balance']  = $row->balance;

            $Export[] = $account;
        }

        return $Export;

    }

    /**
     * Imports data from the array into the accounts table. Existing data won't be overwritten.
     * @param array $Accounts an array of SimpleXMLElement objects
     * @param array $CurrenciesIDs
     * @return array
     */
    public static function import_osmxml($Accounts, $CurrenciesIDs=array()){

        $AccountsIDs = array();

        if( isset($Accounts->account) ) foreach($Accounts->account as $account){

            $id    = intval($account->ID); //keep the ID for reference

            $cid = intval($account->cid); $currency_id = isset($CurrenciesIDs[$cid]) ? intval( $CurrenciesIDs[$cid] ) : 0;

            if( empty($currency_id) ) continue; //entries without a currency ID are invalid

            $data = array(
                'account_iban'              => strval( $account->iban ),
                'account_currency_id'   => $currency_id,
                'account_slug'               => strval( $account->slug ),
                'holder_name'              => strval( $account->holder ),
                'holder_swift'              => strval( $account->swift ),
                'balance'                     => floatval( $account->balance )
            );

            $AccountsIDs[$id] = self::add($data);

        }

        return $AccountsIDs;

    }

}