<?php

namespace FinFlow;

class Label{
	
	public static $table 	    = 'fn_labels';
	public static $table_assoc 	= 'fn_assoc';
	
	public static function get($slug){
		
		global $fndb, $fnsql; 
		
		$slug = $fndb->escape($slug);
		
		$fnsql->select('*', self::$table, array('slug'=>$slug)); 
		
		return $fndb->get_row( $fnsql->get_query() );
	}

    public static function get_parents(){

        global $fndb, $fnsql;

        $fnsql->select('*', self::$table);
        $fnsql->condition('parent_id', '=', '0');
        $fnsql->conditions_ready();
        $fnsql->orderby('slug', self::$table, "ASC");

        return $fndb->get_rows( $fnsql->get_query() );

    }

    public static function get_children($label_id){

        global $fndb, $fnsql; $label_id = intval($label_id);

        $fnsql->select('*', self::$table, array('parent_id'=>$label_id));
        $fnsql->orderby('slug', self::$table, "ASC");

        return $fndb->get_rows( $fnsql->get_query() );

    }
	
	public static function get_all($start=0, $count=25){
		
		global $fndb, $fnsql;
		
		$fnsql->select('*', self::$table);
        $fnsql->orderby('slug', self::$table, "ASC");
		$fnsql->limit($start, $count);
		
		return $fndb->get_rows( $fnsql->get_query() );
		
	}
	
	public static function get_total(){
		
		global $fndb, $fnsql;
		
		$fnsql->init(SQLStatement::$SELECT);
		$fnsql->table(self::$table);
		$fnsql->count('label_id', 'total');
		$fnsql->from();
		
		$Row = $fndb->get_row( $fnsql->get_query() );
		
		if( isset($Row->total) ) return $Row->total;
		
		return 0;
		
	}

    public static function get_by_id($label_id){
        return self::get_by($label_id, 'id');
    }
	
	public static function get_by($input, $by='slug'){
		
		global $fndb, $fnsql;
		
		$input = $fndb->escape($input);
		
		if ( $by == 'slug' )
			$fnsql->select('*', self::$table, array('slug'=>$input));
		
		if ( $by == 'title' )
			$fnsql->select('*', self::$table, array('title'=>$input));
		
		if ( $by == 'id' )
			$fnsql->select('*', self::$table, array('label_id'=>$input));
		
		if( $fnsql->get_query() ) return $fndb->get_row( $fnsql->get_query() );
		
		return NULL;
		
	}
	
	public static function add($title, $longdesc='', $parent_id=0, $customslug=''){
		
		global $fndb, $fnsql; 
		
		$slug      = empty($customslug) ? self::get_slug($title) : trim($customslug);
		$parent_id = intval($parent_id);

		$longdesc 	= $fndb->escape($longdesc);
		$title		= $fndb->escape($title);
		
		$fnsql->insert(self::$table, array('parent_id'=>$parent_id, 'slug'=>$slug, 'title'=>$title, 'description'=>$longdesc));
		
		return ( $fndb->execute_query( $fnsql->get_query() ) === false ) ? false : $fndb->last_insert_id;

	}
	
	public static function update($label_id, $title, $longdesc=""){
		global $fndb, $fnsql; 
		
		$longdesc 	= $fndb->escape($longdesc);
		$title			= $fndb->escape($title);
		
		$fnsql->update(self::$table, array('title'=>$title, 'longdesc'=>$longdesc), array('label_id'=>intval($label_id)));
		
		return $fndb->execute_query( $fnsql->get_query() );
	}
	
	public static function get_slug($title, $transliterate=true){

        if( $transliterate )
	        return Util::transliterate(strtolower($title), "-");

		$slug = strtolower(str_replace(" ", '-', $title));
		$slug = preg_replace("/[^A-Za-z0-9 ]/", '', $slug);
		
		return $slug;
	}
	
	public static function get_ops_count($label_id){
		global $fndb, $fnsql;
		
		$label_id = intval($label_id);
		
		$fnsql->table(self::$table_assoc);
		$fnsql->init(SQLStatement::$SELECT);
		$fnsql->count('trans_id', 'ops_count');
		$fnsql->from();
		$fnsql->condition('label_id', '=', $label_id);
		$fnsql->conditions_ready();
		
		$Row = $fndb->get_row($fnsql->get_query());
		
		if ( $Row and isset($Row->ops_count) )
			return $Row->ops_count;
		
		return 0;
		
	}
	
	public static function remove($label_id){

		global $fndb, $fnsql;
		
		$label_id = intval($label_id);
		
		if ( $label_id ){

            //--- remove label assocs ---//
			$fnsql->delete(self::$table_assoc, array('label_id'=>$label_id));
            $fndb->execute_query( $fnsql->get_query() );
            //--- remove label assocs ---//

            //--- update children to parent '0' ---//
            $fnsql->update(self::$table, array('parent_id'=>0), array('parent_id'=>$label_id));
            $fndb->execute_query( $fnsql->get_query() );
            //--- update children to parent '0' ---//

			//--- remove label ---//
	        $fnsql->delete(self::$table, array('label_id'=>$label_id));
			//--- remove label ---//

		    return  $fndb->execute_query( $fnsql->get_query() );

		}
		
		return FALSE;
		
	}


    public static function get_export_array(){

        global $fndb, $fnsql;

        $Export = array();

        $fnsql->select('*', self::$table); $Rows = $fndb->get_rows( $fnsql->get_query() );

        if( $Rows and count($Rows) ) foreach($Rows as $row){
            $label = array();

            $label['ID']              = $row->label_id;//keep the trans id for reference
            $label['slug']           = $row->slug;
            $label['title']          = $row->title;
            $label['description']= $row->description;

            $Export[] = $label;
        }

        return $Export;

    }


    /**
     * Imports data from the array into the labels table. Existing data won't be overwritten.
     * @param array $Labels an array of SimpleXMLElement objects
     * @return bool
     */
    public static function import_osmxml($Labels){

        $LabelsIDs = array();

        if( isset($Labels->label) ) foreach($Labels->label as $label){

            $id    = intval($label->ID); //keep the ID for reference
            $title = fn_Util::unescape_xml( strval($label->title) );
            $slug  = strval( $label->slug );
            $desc = fn_Util::unescape_xml( strval($label->description) );

            $exists = self::get_by( $slug, 'slug' );

            if( $exists and isset($exists->label_id) ) {
                $LabelsIDs[$id] = $exists->label_id; continue;
            }

            $LabelsIDs[$id] = self::add( $title, $desc, $slug );
        }

        return $LabelsIDs;

    }
	
}