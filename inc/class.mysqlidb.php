<?php

define( 'OBJECT'	, 'OBJECT', TRUE );
define( 'ARRAY_A'	, 'ARRAY_A' );
define( 'ARRAY_N'   , 'ARRAY_N' );

/**
 * MySQL(i) Database PHP API Wrapper. Offers the option to manage one or more connections as objects. Requires PHP 5
 * @author Adrian Silimon (http://adrian.silimon.eu/)
 * @version 0.7
 * @license GPL v2
 */
class MySQLiDB{
	
	public 	  $host;
	protected $port;
	protected $user;
	
	private $password;
	private $name;
	
	/**
	 * The connection status
	 * @var bool
	 */
	public $connected;
	
	public $char_set;
	public $collation;
	
	public $query;
	public $result;
	public $error;
	
	public $queryLog;
	public $errorLog;

    public $logfile;
    public $logfile_append;
	
	public $last_insert_id;
	public $affected_rows;
	public $vresult;
	
	public $connectionId;
	
	public function __construct($host, $user, $pasword, $name=FALSE, $char_set="utf8", $collation='utf8_general_ci', $logfile=FALSE, $logfile_append=FALSE){
		
		$this->host			= $host;
		$this->user 	    = $user;
		$this->password 	= $pasword;
		$this->name 		= empty($name) ? $this->user : $name;
		
		$this->collation = $collation;
		$this->char_set = $char_set;

        $this->logfile             = $logfile;
        $this->logfile_append = $logfile_append;

		$this->connect();
		
	}
	
	public function connect(){
		
		$this->connectionId = @mysqli_connect($this->host, $this->user, $this->password);
		
		if( @mysqli_select_db($this->connectionId, $this->name) ){
			
			$this->set_charset($this->char_set, $this->collation);
		
			$this->connected = TRUE;
		}
		
		else {
			$this->connected = FALSE;
			$this->error		= @mysqli_error($this->connectionId);
		}
		
		return $this->connected;
		
	}

    public function disconnect(){
        return @mysqli_close($this->connectionId);
    }
	
	public function set_charset($charset, $collation ){
	
		if (function_exists('mysqli_set_charset'))
			@mysqli_set_charset( $this->connectionId, $charset );
		else 
			if( strlen($collation) ) $this->execute_query("SET NAMES {$charset} COLLATE {$collation}");
	
	}
	
	
	public function escape($mixed, $valuesonly=FALSE){
		
		if ( is_array( $mixed ) ) if ( count($mixed) ) {
			
			$escaped = array();
			
			foreach ($mixed as $key=>$val){

				$ekey = mysqli_real_escape_string($this->connectionId, $key);

                if(is_array($val) or is_object($val))
                    $eval = $this->escape( is_object($val) ? @get_object_vars($val) : $val );
                else
				    $eval	 = mysqli_real_escape_string($this->connectionId, $val);
				
				if ( $valuesonly )
                    $escaped[$key] = $eval;
				else
                    $escaped[$ekey]= $eval;
			}
			
			return $escaped;
		}
		
		if ( is_object( $mixed ) ) 
			return $this->escape( get_object_vars($mixed), $valuesonly );

        $mixed = @strval($mixed);

		return mysqli_real_escape_string($this->connectionId, $mixed);

	}
	
	/**
	 * executes the query, this can be specified by string or taken by default as $this->query
	 * @param string $string[optional]
	 * @return bool
	 */
	public function execute_query( $string ){
		
		$this->query = $string;
	
		if (strlen($this->queryLog) > 1000) $this->clear_logs($this->logfile, $this->logfile_append); //automatically clear logs in case these are too long
	
		$this->queryLog.= ( $this->query . "\n" );
	
		if ($this->result = @mysqli_query( $this->connectionId, $this->query ) ){
			$this->error = ""; 
			
			if ( strpos($this->query, "UPDATE") === FALSE ); else $this->affected_rows  = @mysqli_affected_rows($this->connectionId);
			if ( strpos($this->query, "DELETE") === FALSE );  else $this->affected_rows  = @mysqli_affected_rows($this->connectionId);
			if ( strpos($this->query, "INSERT") === FALSE );  else $this->last_insert_id    = @mysqli_insert_id($this->connectionId);
			
			return  TRUE;
		}
		else {
			
			$this->error	   = @mysqli_error($this->connectionId);
			$this->errorLog.= ("\n". $this->error);
				
			return FALSE;
		}

	}


    /**
     * Executes multiple SQL queries separated by semicolons.
     * @param $string the queries
     * @return bool|int the number of successful queries, or false upon failure
     */
    public function execute_multi_query( $string ){

        if( @mysqli_multi_query($this->connectionId, $string) ){

            $this->error = "";

            $i = 0;

            do $i++; while ( @mysqli_next_result( $this->connectionId ) );

            return $i; //return the number of successful queries

        }
        else {
            $this->error	   = mysqli_error($this->connectionId);
            $this->errorLog.= ("\n". $this->error);
        }

        if( @mysqli_errno($this->connectionId) ){
            $this->error	   = mysqli_error($this->connectionId);
            $this->errorLog.= ("\n". $this->error);
        }

        return FALSE;

    }

	
	public function parse_object($raw_result){
		
		$Rows = array(); while ( $row = @mysqli_fetch_object($raw_result) ) $Rows[] = $row;
		
		return $Rows;
	}
	
	public function parse_array_a($raw_result){
		
		$Rows = array(); while ( $row = @mysqli_fetch_array($raw_result, MYSQL_ASSOC) ) $Rows[] = $row;
		
		return $Rows;
	}
	
	public function parse_array_n($raw_result){
		$Rows = array(); while ( $row = @mysqli_fetch_array($raw_result, MYSQL_NUM) ) $Rows[] = $row;
		
		return $Rows;
	}
	
	
	public function free(){
		@mysqli_free_result( $this->result );
	}
	
	public function get_row($query, $index=0, $result_type=NULL){
		
		if ( empty($result_type) ) $result_type = OBJECT;
		
		$this->execute_query($query);
		
		if ( $this->result ) {
			
			if ($result_type == OBJECT) 
				$this->vresult = $this->parse_object( $this->result );
			
			if ( $result_type == ARRAY_A )
				$this->vresult = $this->parse_array_a( $this->result );
			
			if ( $result_type == ARRAY_N )
				$this->vresult = $this->parse_array_n( $this->result );
			
			return @$this->vresult[$index] ? $this->vresult[$index] : NULL;
			
		}
		
		return NULL;
	}
	
	public function get_rows($query, $result_type=NULL){
	
		if ( empty($result_type) ) $result_type = OBJECT;
	
		$this->execute_query($query);
	
		if ( $this->result ) {
				
			if ($result_type == OBJECT)
				$this->vresult = $this->parse_object( $this->result );
				
			if ( $result_type == ARRAY_A )
				$this->vresult = $this->parse_array_a( $this->result );
				
			if ( $result_type == ARRAY_N )
				$this->vresult = $this->parse_array_n( $this->result );
				
			return count($this->vresult) ? $this->vresult : NULL;
				
		}
	
		return NULL;
	}

    /**
     * Clears the errorsLog and the queryLog, call this function whenever you think one of those strings is too big to keep them in memory optionally you can save them to a file (make sure that the file you supply is not read-only!)
     * @param bool $store_to_file the path to the file to store logs, default false
     * @param bool $append append or clear the file, default true
     */
    public function clear_logs($store_to_file=FALSE, $append=TRUE){
	
		if ($store_to_file){
			
			$before = ( "//-----" . __CLASS__ . " Log at " . date("Y-m-d H:i:s"). " ------// \n\n");
            $before.="--------- errors --------\n\n";
			$content= ( $before . $this->errorLog );
            $before = "-------- queries --------\n\n";
            $content.= ( $before . $this->queryLog );

			if ($append)
				@file_put_contents($store_to_file, $content, FILE_APPEND);
			else 
				@file_put_contents($store_to_file, $content);
		}
	
		$this->errorLog = "";
		$this->queryLog = "";
		
	}
	
}