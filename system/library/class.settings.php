<?php

class fn_Settings{
	
	public static $table = 'fn_settings';
	
	public static function get($key, $default=FALSE, $multiple=FALSE){
		
		global $fndb, $fnsql; if( ! $fndb or ! $fndb->connected ) return $default;

		$value = null;
        $key    = $fndb->escape($key);
		
		if( strlen($key) ){

			$fnsql->select('setting_val', self::$table, array('setting_key'=>trim($key)) );

            $row = $fndb->get_row( $fnsql->get_query() );

			if ( $row and isset($row->setting_val) ) $value = $multiple ? unserialize( $row->setting_val ) : $row->setting_val;

		}
		
		return empty($value) ? $default : $value;
	}
	
	public static function set($key, $value){
		
		global $fndb, $fnsql;
		
		$value = self::nicevalue($value);
		
		if( strlen($key) and strlen($value) ){
			
			$exists = self::get($key, FALSE);
			
			if ( $exists ){
				$fnsql->update(self::$table, array('setting_val'=>$value), array( 'setting_key'=>$key )); $fndb->execute_query( $fnsql->get_query() );
			}else 
				return self::add($key, $value);

		}
		else self::remove($key);
		
		return FALSE;
	}
	
	public static function add($key, $value="", $description=""){
		
		global $fndb, $fnsql;
		
		$value = self::nicevalue($value);
		
		if( strlen($key) and $value ){
			$fnsql->insert(self::$table, array('setting_key'=>$key, 'setting_val'=>$value, 'settting_desc'=>$description));  
			return $fndb->execute_query( $fnsql->get_query() );
		}
		
		return FALSE;
		
	}
	
	public static function remove($key){
		
		global $fndb, $fnsql;
		
		if( strlen($key) ){
			$fnsql->delete(self::$table, array('setting_key'=>$key)); return $fndb->execute_query( $fnsql->get_query() );
		}
		
		return FALSE;
	}
	
	public static function nicevalue( $value ){
		
		if ( is_object($value) )
			$value = @get_object_vars($value);
		
		if ( is_array($value) ){
			$value = @serialize($value);
		}
		
		return $value;
		
	}

    public static function get_export_array(){

        global $fndb, $fnsql;

        $Export = array();

        $fnsql->select('*', self::$table); $Rows = $fndb->get_rows( $fnsql->get_query() );

        if( $Rows and count($Rows) ) foreach($Rows as $row){
            $setting = array();

            $setting['key']             = $row->setting_key;
            $setting['value']           = $row->setting_val;
            $setting['description']   = $row->settting_desc;

            $Export[] = $setting;
        }

        return $Export;

    }


    /**
     * Imports data from the array into the settings table. Existing data won't be overwritten.
     * @param array $Settings an array of SimpleXMLElement objects
     * @return bool
     */
    public static function import_osmxml($Settings){

        $icount = 0;

        if( isset($Settings->setting) ) foreach($Settings->setting as $setting){
            $imported = self::add(strval($setting->key), strval($setting->value), strval($setting->description)); if( $imported ) $icount++;
        }

        return $icount;

    }

}