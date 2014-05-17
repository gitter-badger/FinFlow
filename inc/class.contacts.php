<?php
/**
 * FinFlow 1.0 - Contacts Class
 * @author Adrian S. (http://www.gridwave.co.uk/)
 * @version 1.0
 */

class fn_Contacts{

    public static $table  = 'cash_contacts';

    public static function add($data){
        //TODO
    }

    public static function get($contact_id){
        //TODO
    }


    public static function update($contact_id, $data=array()){
        //TODO
    }

    public static function delete($contact_id){
        //TODO
    }

    public static function get_all($offset=0, $limit=25){
        global $fndb, $fnsql;

        $offset = intval($offset);
        $limit   = intval($limit);

        $fnsql->select('*', self::$table);
        $fnsql->limit($offset, $limit);

        return $fndb->get_rows( $fnsql->get_query() );

    }

    public static function assoc_trans($contact_id, $trans_id){

        $contact_id = intval( $contact_id );
        $trans_id     = intval( $trans_id );

        return fn_OP::update($trans_id, array('contact_id'=>$contact_id));

    }

}