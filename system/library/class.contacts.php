<?php
/**
 * FinFlow 1.0 - Contacts Class
 * @author Adrian S. (http://www.gridwave.co.uk/)
 * @version 1.0
 */

namespace FinFlow;

class Contacts{

    const TABLE = 'fn_contacts';

    public static function table(){
        return ( FN_DB_PREFIX . self::TABLE );
    }

    public static function add($data){
        //TODO
    }

    public static function get($contact_id){
        //TODO ....
    }

	public static function get_by($var, $key='email'){
		//TODO...

		return null;
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

        $fnsql->select('*', self::TABLE);
        $fnsql->limit($offset, $limit);

        return $fndb->get_rows( $fnsql->get_query() );

    }

    public static function assoc_trans($contact_id, $trans_id){

        $contact_id = intval( $contact_id );
        $trans_id     = intval( $trans_id );

        return OP::update($trans_id, array('contact_id'=>$contact_id));

    }

}