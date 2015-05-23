<?php

namespace FinFlow;

/**
 * SQLStatement is an SQL statement builder made mainly for MySQL, but also compatible with any other *SQL databases
 * @author Adrian7 (http://adrian.silimon.eu/)
 * @version 1.3.1
 */
class SQLStatement{
	
	public $query;
	public $queryStack;
	
	public $queryType;
	public $queryStackPlug;
	
	public $table;
	
	protected $previousJunction = NULL;
	
	protected $groupJunction	   = NULL;
	protected $groupIndex		   = 0;
	
	static $SELECT  = 'SELECT';
	static $INSERT  = 'INSERT';
	static $UPDATE = 'UPDATE';
	static $DELETE = 'DELETE';
	static $CREATE = 'CREATE';
	
	static $OR 	= 'OR';
	static $AND= 'AND';
	
	static $TABLE_CONTEXT_CREATE = 'CREATE';
	static $TABLE_CONTEXT_ALTER	= 'ALTER';
    static $TABLE_CONTEXT_DROP	    = 'DROP';
	
	static $NOT_NULL 			 = 'NOT NULL';
	static $NULL 					 = 'NULL';
	static $AUTO_INCREMENT = 'AUTO_INCREMENT';
	
	static $unsigned	 			= 'unsigned';
	
	static $PRIMARY_KEY 	= 'PRIMARY';
	static $UNIQUE_KEY 	= 'UNIQUE';
	
	
	public function __construct($statementType = 'SELECT'){
		$this->init($statementType);
	}
	
	public function init($statementType = 'SELECT'){
		if ( $statementType == SQLStatement::$SELECT ) 	$this->query = "SELECT ";
		if ( $statementType == SQLStatement::$INSERT ) 		$this->query = "INSERT ";
		if ( $statementType == SQLStatement::$UPDATE ) 	$this->query = "UPDATE ";
		if ( $statementType == SQLStatement::$DELETE ) 	$this->query = "DELETE ";
		
		$this->previousJunction = "";
		
		$this->queryType = $statementType;
	}
	
	public function reset($indexes=false){
		$this->query = "";

        if( $indexes ){
            //--- reset indexes ---//
            $this->resetGroupIndex();
            //--- reset indexes ---//
        }
	}

    public function resetGroupIndex(){
        $this->groupIndex = 0;
    }
	
	public function create_table($table=NULL, $if_not_exists=TRUE){
		
		$this->query = 'CREATE TABLE ';
		
		if ( empty($table) ) $table = $this->table;
		
		if ( $if_not_exists )
			$this->query.= 'IF NOT EXISTS ';
		
		$this->query.= "`{$table}` ";
	}
	
	public function create_table_properties($engine='MyISAM', $charset='utf8', $auto_increment=1){
		
		$this->query = trim($this->query);
		$this->query = trim($this->query, ",");
		
		$this->conditions_ready(TRUE);
		
		$this->query.= " ENGINE={$engine} DEFAULT CHARSET={$charset}";
		
		if ( $auto_increment )
			$this->query.= " AUTO_INCREMENT={$auto_increment}";
		
	}
	
	public function add_table_field($field, $type='int', $length=11, $properties=array(), $default=FALSE, $context='CREATE'){
		
			if ( $context == self::$TABLE_CONTEXT_CREATE ){
				if ( strpos($this->query, "(") === FALSE) $this->query.="( ";
			}else {
				if ( strpos($this->query, "ADD") === FALSE) $this->query.="ADD ";
			}
			
			$this->query.= "`{$field}` {$type}";
			
			if ( $length ){
				
				if ( is_array($length) ) //maybe an enum field
					$length = "('" . implode("', '", $length) . "')";
				else 
					$length = "({$length})";
				
				$this->query.= "{$length}";
			}
			
			if ( count($properties) )
				$properties = implode(" ", $properties);
			else 
				$properties = "";
			
			if($default)
				$default = "DEFAULT '{$default}'";
			else 
				$default = "";
			
			$this->query.= trim("{$properties} {$default}");
			
			if ( $context == self::$TABLE_CONTEXT_CREATE ) $this->query.=", ";
		
	}
	
	public function add_table_key($field, $type="PRIMARY", $context='CREATE'){
		
		if ( $context == self::$TABLE_CONTEXT_CREATE ){
			if ( strpos($this->query, "(") === FALSE) $this->query.="( ";
		}else{
			if ( strpos($this->query, "ADD") === FALSE) $this->query.="ADD ";
		}
		
		$this->query.= "{$type} KEY (`$field`) ";
		
		if ( $context == self::$TABLE_CONTEXT_CREATE ) $this->query.=",";
		
	}
	
	public function alter_table($table=NULL){
		if ( empty($table) ) $table = $this->table;
		
		$this->query.= "ALTER `{$table}` ";
	}
	
	public function after($field){
		$this->query.="AFTER  `{$field}`";
	}

    public function drop($tables=array(), $if_exists=false){
        $tables = empty($tables) ? "`{$this->table}`" : ('`' . @implode('`, `', $tables) . '`'  );

        $this->query.= 'DROP TABLE ';

        if( $if_exists ) $this->query.= 'IF EXISTS ';

        $this->query.= ( $tables . ' ' );
    }
	
	public function fields( $fields = array(), $table="", $comma=false ){
		
		if ( empty($table) )
			$table = $this->table;
		
		$commasep = ( $table == $this->table ? ( $comma ? ',' : '' ) : ',' );
		
		if ($fields == '*') {
			$this->query.= "{$commasep} `{$table}`.*"; return ;
		}
		
		if( ! is_array($fields) ){
			
			if (strrpos($fields, ",")){
				$fields = trim(trim($fields), ",");
				$fields = explode(",", $fields);
			}
			else
				$fields = array( trim($fields) );
		} 
		
		if ( is_array($fields) ) {
			
			if ( $this->queryType != SQLStatement::$UPDATE ){
				//trim the fields
				$tmpfields = array();

				foreach ($fields as $field)
					$tmpfields[] = trim($field);

				$fields = $tmpfields;

			}
			
			if ( $this->queryType == SQLStatement::$SELECT )
				$this->query.= ( "{$commasep} `{$table}`.`" . trim( trim(implode("`, `{$table}`.`", $fields), ",") ) . "`" );
			
			if ( $this->queryType == SQLStatement::$INSERT )
				$this->query.= ( " `{$table}`(`" . trim( trim(implode("`, `", $fields), ",") ) . "`)" );
			
			if ( $this->queryType == SQLStatement::$UPDATE ){  
				
				$this->query.=  " SET";
				$tmpstring	 = "";
				
				foreach ($fields as $field=>$value)
					$tmpstring.= ( ", `{$table}`.`{$field}`='{$value}' " );
				
				$this->query.= trim($tmpstring, ",");
				
			}
			
			//TODO add create statement
			
		}
		
	}
	
	public function distinct($field=null, $table=NULL){
		if ($table)
			$this->query .= ( " DISTINCT `{$table}`" . ( $field ? ".`{$field}`" : '' ) );
		else 
			$this->query .= ( " DISTINCT " . ( $field ? "`{$field}`" : '' ) );
	}
	
	public function table($table, $append=FALSE){
		$this->table = trim($table);
		
		if ($append ) $this->query.= " `{$this->table}`";
	}

	/**
	 * Adds the FROM condition to query string
	 * @param $table
	 * @param string $alias
	 */
	public function from($table='', $alias=''){
		
		if( strpos($table, self::$SELECT) === false ){ //is a regular table/view

			if ( ! empty($table) )
				$this->table($table);

			$this->query.= " FROM `{$this->table}`";

		}
		else{                                          //is a query
			$this->query.= ( ' FROM ( ' . trim($table) . ' ) ' );
		}

		if( ! empty($alias) )
			$this->query.= " `{$alias}`";

	}
	
	public function condition($field, $operator, $value, $junction='AND', $table="", $quoted=TRUE, $groupindex=FALSE){
		
		if ( empty($table) ) $table = $this->table;
		
		if ( empty($this->previousJunction) ) $this->query	.= " WHERE (";
		else if ( $junction != 'WHERE' ) {
			
			if ( $groupindex )
				$this->query.=  $this->group_junction($junction, $groupindex);
			else {
				if ( $junction != $this->previousJunction ) 
					$this->query.= ") {$junction} ("; 
				else 
					$this->query.= " {$junction} ";
			}
		}
		
		$this->previousJunction = $junction;

		if ($quoted)
			$this->query.= "`{$table}`.`{$field}` {$operator} '{$value}'";
		else 
			$this->query.= "`{$table}`.`{$field}` {$operator} {$value}";

	}
	
	public function group_junction($junction, $index=0){
		
		$str = "";
		
		if( $index ){

			if ($index != $this->groupIndex) //it's a new group
				$str = ") {$junction} (";
			else 
				$str = " {$junction} "; //apply the group's junction
			
			$this->groupJunction = $junction;
			$this->groupIndex	 = $index;
	
		}
		else{ 
			$this->groupJunction = NULL;
			$this->groupIndex	 = 0;
			
			//groups ended
			$str = ") {$junction} (";
		}

		return $str;
		
	}
	
	public function conditions($conditions=array(), $operator='=', $junction="AND"){
		
		if ( is_array($conditions) and count($conditions) ) foreach ($conditions as $field=>$value) $this->condition($field, $operator, $value, $junction);
		
	}
	
	public function values( $values ){
		
		$this->query.= " VALUES";
		
		if ( !is_array($values) ) {
			
			if ( strrpos($values, ",") ) 
				$values = explode(",", trim($values, ","));
			else 
				$values = array(trim($values));
		}
		
		if( is_array($values) ) $this->query.=" ('" . implode("', '", $values) . "') ";
		
	}
	
	public function where($field, $operator='=', $value=1){
		if ( strpos($this->query, 'WHERE') === FALSE ) $this->condition($field, $operator, $value);
	}
	
	public function conditions_ready($force=FALSE){
		if ( $force )
			$this->query.=")";
		else 
			if ( strrpos($this->query, "(") > strrpos($this->query, ")") ) $this->query.=")";
	}


	public function orderby($fields, $table="", $order="ASC"){
		
		if ( empty($table) ) $table = "";
		
		$order = strtoupper($order);

        if( !is_array($fields) ) $fields = array($fields);

		if ( count($fields) ){
			$this->query.= ( strpos($this->query, "ORDER") === FALSE ) ? " ORDER BY " : " ";
			$ordstring	  = "";
			
			foreach ($fields as $field) $ordstring.= ", {$field} {$order}";
			
			if ( (  strpos($this->query, "ASC") === FALSE ) and (  strpos($this->query, "DESC") === FALSE ) )
				//no fields order has been set so far
				$this->query.= trim( trim($ordstring, ",") );
			else 
				$this->query.= $ordstring;
			
		}
		
	}
	
	public function print_query($before='<pre><code>', $after='</code></pre>'){
		echo ($before . $this->query . $after);
	}
	
	public function get_query($index=0){
		if ( $index )
			return isset($this->queryStack[$index]) ? $this->queryStack[$index] : null;
		else 
			return $this->query;
	}
	
	public function store_query($query=NULL){
		
		if ( empty($query) ) $query = $this->query;
		
		$this->queryStack[] = $query;

        return ( count($this->queryStack) -1 );
	}
	
	public function join($table, $field_source, $field_dest, $type="LEFT"){
		$this->query.= " {$type} JOIN `{$table}` ON `{$this->table}`.`{$field_source}`=`{$table}`.`{$field_dest}`";
	}
	
	
	public function left_join($table, $field_s, $field_d){
		$this->join($table, $field_s, $field_d, "LEFT");
	}
	
	public function right_join($table, $field_s, $field_d){
		$this->join($table, $field_s, $field_d, "RIGHT");
	}
	
	public function inner_join($table, $field_s, $field_d){
		$this->join($table, $field_s, $field_d, "INNER");
	}
	
	public function count($field, $as=NULL, $distinct=false){

		$distinct = $distinct ? ' DISTINCT ' : '';


		if ( empty($as) )
			$this->query.= " COUNT({$distinct}`{$this->table}`.`{$field}`)";
		else 
			$this->query.= " COUNT({$distinct}`{$this->table}`.`{$field}`) AS `{$as}`";
	}
	
	public function limit($offset=0, $count=25){
		$this->query.= " LIMIT {$offset}, $count";
	}
	
	public function fieldas($field="", $as = "", $table="", $comma=TRUE){
		
		if ( $comma ) 
			$comma = ",";
		else 
			$comma = "";
		
		if ( empty($field) and strlen($as) ) 	$this->query.= " AS `{$as}`";
		if ( empty($as) and strlen($field) ) 	$this->query.= strlen($table) > 0 ? "{$comma} `{$table}`.`{$field}` AS" : "{$comma} `{$field}` AS";
		if ( empty($field) and empty($as) ) $this->query.= "{$comma} AS";
		if ( strlen($field) and strlen($as) )  $this->query.= strlen($table) > 0 ? "{$comma} `{$table}`.`{$field}` AS `{$as}`" : "{$comma} `{$field}` AS `{$as}`";
		
	}
	
	public function last_insert_id($as='last_id'){
	
		if ( strlen($as) ) 
			$this->query.= " LAST_INSERT_ID() AS `{$as}`";
		else 
			$this->query.= " LAST_INSERT_ID()";
	}
	
	public function into($table = ""){
		
		if ( empty($table) ) 
			$this->query.=" INTO";
		else
			$this->query.=" INTO `{$table}`";
	}
	
	public function sum($field, $as=""){
		
		//add a comma if needed
		if ( strlen($this->query) > (strrpos($this->query, "SELECT") + 8) ) $this->query.=",";
		
		$this->query.=" SUM(`{$field}`)";
		
		if ( strlen($as) ) $this->fieldas("", $as);
		
	}

    public function min($field, $as=""){
        //add a comma if needed
        if ( strlen($this->query) > (strrpos($this->query, "SELECT") + 8) ) $this->query.=",";

        $this->query.=" MIN(`{$field}`)";

        if ( strlen($as) ) $this->fieldas("", $as);
    }

    public function max($field, $as=""){
        //add a comma if needed
        if ( strlen($this->query) > (strrpos($this->query, "SELECT") + 8) ) $this->query.=",";

        $this->query.=" MAX(`{$field}`)";

        if ( strlen($as) ) $this->fieldas("", $as);
    }

	public function datepart($field, $part="YEAR", $as=FALSE, $table=NULL){
		
		if ( empty($table) ) $table = $this->table;
		
		$part = strtoupper($part);
		
		//add a coma if it is missing
		if ( strlen($this->query) > (strrpos($this->query, "SELECT") + 8) ) $this->query.=",";
		
		$this->query.=" EXTRACT({$part} FROM `{$table}`.`{$field}`)";
		
		if ($as) $this->fieldas(FALSE, $as);
	}

	public function extract_datepart($field, $part="YEAR", $as=FALSE, $table=NULL){
		 $this->datepart($field, $part, $as, $table);
	}
	
	public function group_by($fields, $table=""){
		
		$this->query.= " GROUP BY";
		$commasep = "";
		
		if ( is_array($fields) ){ 
			foreach ($fields as $field){
				$this->query.= strlen($table) ? "{$commasep} `{$table}`.`{$field}`" : "{$commasep} `{$field}`"; 
				$commasep = ",";
			}
		}else 
			$this->query.= strlen($table) ? "{$commasep} `{$table}`.`{$fields}`" : "{$commasep} `{$fields}`";
	}
	
	public function insert($table,  $fieldsvalues=array()){
		
		$this->init(SQLStatement::$INSERT);
		
		$this->table($table);
		$this->into();
		
		$fields = array();
		$values= array();
		
		if ( count($fieldsvalues) ) foreach ($fieldsvalues as $field=>$value){
			$fields[] = trim($field);
			$values[]= $value; 
		}
		
		$this->fields($fields);
		$this->values($values);
	}
	
	public function select($fields, $table, $conditions=NULL, $operator='=', $junction="AND", $autoready=TRUE){
		
		$this->init(SQLStatement::$SELECT);
		
		$this->table($table);
		
		$this->fields($fields);
		
		$this->from($table);
		
		if ( empty($junction) ) $junction = SQLStatement::$AND;
		
		if ( is_array($conditions) ) {
			foreach ($conditions as $field=>$value) $this->condition($field, $operator, $value, $junction);
			
			if ( $autoready ) $this->conditions_ready();
		}

	}
	
	public function update($table, $fieldsvalues=array(), $conditions=array(), $operator='=', $junction="AND", $autoready=TRUE){
		
		$this->init( SQLStatement::$UPDATE );
		
		$this->table($table, TRUE);
		
		$this->fields($fieldsvalues);
		
		$this->conditions($conditions, $operator, $junction);
		
		if ($autoready) $this->conditions_ready();
		
	}
	
	/**
	 * Generates a DELETE SQL query based on paramaters
	 * @param string $table
	 * @param array $conditions fields and their desired values to compare <code>array('field'=>'value')</code>
	 * @param string $operator the operator to use to compare fields and values (e.g.: =, >, <, !=, LIKE)
	 * @param string $junction OR/AND 
	 * @param bool $autoready the condition should be marked as ready
	 */
	public function delete($table, $conditions=array(), $operator='=', $junction="AND", $autoready=TRUE){
		
		$this->init(SQLStatement::$DELETE );
		
		$this->from($table);
		
		$this->conditions($conditions, $operator, $junction);
		
		if ($autoready) $this->conditions_ready();
		
	}

	/**
	 * Prepends the string to the current query string
	 * @param $string
	 */
	public function query_prepend($string){
		if( strlen($string) )
			$this->query = ( trim($string) . ' ' . $this->query );
	}


	/**
     * Appends the string to the current query string
     * @param $string
     */
    public function query_append($string){
        if( strlen($string) )
	        $this->query.= ( ' ' . trim($string) );
    }

	/**
	 * Wraps the current query between the string before and string after
	 * @param string $before
	 * @param string $after
	 *
	 * @return string
	 */
	public function query_wrap($before, $after=''){
		$this->query = (trim($before) . ' ' . $this->query . ' ' . trim($after)); return $this->query;
	}

	/**
	 * Formats a query string based on args. Accepts variable number of arguments.
	 * Example: 
	 * <code>
	 * 		$Statement=new SQLStatement(); 
	 * 		$Statement->prepare("SELECT `%s`, `%s` FROM `%s` WHERE `id`=%d",  'item_type', 'item_name', 'items', 999);
	 * </code>
	 * @param string $string the query string
	 * @param mixed $args
	 * @return string the formatted query string
	 */
	public function prepare($string, $args){
		
		$numargs = func_num_args();
		$argsarray= array();
		
		if ($numargs > 1) for($i=1; $i<$numargs; $i++) $argsarray[] = func_get_arg($i);

		$this->query = vsprintf($string, $argsarray);
		
		return $this->query;
	}
	
}