<?php

namespace FinFlow;

class TaskAssistant{

	const TABLE = 'fn_tasks';

	const OPTION_PREFIX = 'task_';

	const STATE_RUNNING = 'running';
	const STATE_STOPPED = 'stopped';
	const STATE_FINISHED= 'finished';
	const STATE_UNKNOWN = 'unknown';

	public static function may_task_run($name){

		global $fndb, $fnsql;

		$name = $fndb->escape( strval($name) );

		$fnsql->select('task_id, status, active', self::TABLE, array('task_name'=>$name));

		$row = $fndb->get_row( $fnsql->get_query() );

		if( $row and isset($row->task_id) and $row->task_id ){

			if( $row->active == 'yes')
				return in_array($row->status, array(self::STATE_FINISHED, self::STATE_STOPPED));
			else
				self::task_error('The task <em>%1s</em> is deactivated. The task needs to be activated prior to running it.', array($name));
		}
		else
			self::task_error('The task <em>%1s</em> is not registered. A task needs to registered and active prior to running it.', array($name));
		

		return false;

	}

    public static function is_browser(){
        return ( php_sapi_name() != 'cli' ) and strlen($_SERVER['REMOTE_ADDR']);
    }

    public static function is_cli(){
        return ( php_sapi_name() == 'cli' ) and empty($_SERVER['REMOTE_ADDR']);
    }
	
	public static function ukey($cron, $prefix=""){
		
		$skey = md5($cron); 
		$skey  = ($prefix . $skey);
		
		return "__task_{$skey}";
	}
	
	public static function set_cron_state($name, $state='finished'){

		global $fndb, $fnsql;

		$name = $fndb->escape($name);

		if( ! in_array($state, array(self::STATE_FINISHED, self::STATE_RUNNING)) )
			die(
				__t("Invalid state <em>%1s</em> . Available states are <em>%2s</em> or <em>%3s</em>.", $state, self::STATE_RUNNING, self::STATE_FINISHED)
			);
		
		$fnsql->update(self::TABLE, array('status'=>$state), array('task_name'=>$name));

		return $fndb->execute_query( $fnsql->get_query() );
		
	}
	
	public static function get_cron_state($name){

		global $fndb, $fnsql;

		$name = $fndb->escape($name);

		$fnsql->select('status', self::TABLE, array('task_name'=>$name));

		$task = $fndb->get_row( $fnsql->get_query() );

		if( $task and isset($task->status) )
			return $task->status;

		return self::STATE_UNKNOWN;

	}
	
	public static function set_lastrun($name, $lastrun=FALSE){

		global $fndb, $fnsql;

		if ( empty($lastrun) )
			$lastrun = date(FN_MYSQL_DATE);

		$name = $fndb->escape($name);

		$fnsql->update(self::TABLE, array('last_activity'=>$lastrun), array('task_name'=>$name));

		return $fndb->execute_query( $fnsql->get_query() );
		
	}
	
	public static function get_lastrun($name){

		global $fndb, $fnsql;

		$name = $fndb->escape($name);

		$fnsql->select('last_activity', self::TABLE, array('task_name'=>$name));

		$task = $fndb->get_row( $fnsql->get_query() );

		if( $task and isset($task->last_activity) )
			return $task->last_activity;

		return null;

	}
	
	public static function get_lock($task_name, $stop=TRUE){

		if ( self::may_task_run($task_name) ){
			
			self::set_cron_state($task_name, self::STATE_RUNNING);
			self::set_lastrun($task_name);
			
			return TRUE;
			
		}elseif ($stop){
            if( self::is_browser() )
                UI::fatal_error(
	                __t('Task-ul ' . $task_name . ' a fost blocat. Se pare ca o alta instanta a aceluiasi fisier ruleaza in acest moment.')
                );
            else
			    die( date(FN_MYSQL_DATE) . " Eroare: Cronul " . $task_name . " a fost blocat. Se pare ca o alta instanta a aceluiasi fisier ruleaza in acest moment." );
        }
		
		return FALSE;
		
	}
	
	public static function release_lock($task_name){
		self::set_cron_state($task_name, self::STATE_FINISHED);
	}

    public static function task_error($msg, $args=array(), $fatal=true){

        if( self::is_browser() ){
	        $msg = __t($msg, $args); UI::fatal_error($msg, $fatal);
        }
        else{

	        $msg = ( __t("Error: ") . __t( $msg, $args) );

	        //most servers will send an email with the message
	        if($fatal)
		        die( $msg );
	        else
		        echo $msg;

        }
    }

	public static function get_task_file_path($task, $basepath=null, $prefix='cron.'){

		$basepath = empty($basepath) ? ( FNPATH . '/system/tasks/' ) : $basepath;
		$filename = ( $basepath . $prefix . $task . '.php' );

		if( is_dir($basepath) and file_exists($filename ) )
			return $filename;

		return false;

	}

	public static function task_run_browser_header(){
		//TODO...
	}

	public static function task_run_browser_footer(){
		//TODO...
	}

	public static function task_run_prepare(){
		//TODO...
	}

	public static function task_run_cleanup(){
		//TODO...
	}

	/**
	 * Saves a single task option
	 * @param $name
	 * @param $option
	 * @param int $value
	 *
	 * @return bool
	 */
	public static function save_task_option($name, $option, $value=1){
		return Settings::set( ( self::OPTION_PREFIX . $name . '_' . $option) , $value);
	}

	/**
	 * Saves task options
	 * @param $name
	 * @param array $options
	 *
	 * @return bool
	 */
	public static function save_task_options($name, $options=array()){

		if( is_array($options) and count($options) ) foreach ($options as $option=>$default){

			$value = in($option, true);
			$value = empty($value) ? $default : $value;

			if( strpos($option, 'password') === false ); else{
				if( strlen($value) ) $value = Util::encrypt($value, FN_CRYPT_SALT);
			}

			Settings::set( ( self::OPTION_PREFIX . $name . '_' . $option) , $value);

		}

	}

	/**
	 * Retrieves a single task option from the database settings table
	 * @param string name name of the task
	 * @param string $option name of the option
	 * @param mixed $default  default value
	 *
	 * @return bool|mixed|null
	 */
	public static function get_task_option($name, $option, $default=null){
		return Settings::get( ( self::OPTION_PREFIX . $name . '_' . $option) , $default);
	}

	public static function get_task_options($name, $defaults=array()){

		global $fndb, $fnsql;

		$settings= array();
		$prefix  = ( self::OPTION_PREFIX . $name . '_' );
		$keyname = ( $prefix . '%');

		$fnsql->select('setting_key, setting_val', Settings::$table);
		$fnsql->condition('setting_key', 'LIKE', $keyname);
		$fnsql->conditions_ready();

		$Rows = $fndb->get_rows( $fnsql->get_query() );

		if( $Rows and count($Rows) ) foreach($Rows as $row){

			$key    = str_replace($prefix, '', $row->setting_key);
			$value  = $row->setting_val;

			$settings[$key] = $value;

		}

		return array_merge($defaults, $settings);

	}

	/**
	 * Retrieves the current task params
	 * @param $name
	 * @param array $keys
	 * @param bool $db_only
	 * @param int $argv_index
	 * @param bool $xss_clean
	 *
	 * @return array
	 */
	public static function get_task_params($name, $keys=array(), $db_only=false, $argv_index=2, $xss_clean=true){

		$settings   = is_object($keys) ? get_object_vars($keys) : (array) $keys;
		$cli_args   = array();
		$argv_index = intval($argv_index);
		$db_params  = self::get_task_options($name, $keys);

		if( ! $db_only and self::is_cli() )
			parse_str( arg($argv_index), $cli_args );

		if( count($keys) ) foreach ( $keys as $key=>$default ) {

			if( $db_only ){ //use database only to extract key values
				$settings[$key] = $db_params[$key];
			}
			else{           //check input and db too

				if( self::is_browser() ){
					$settings[$key] = ( $value = in($key, $xss_clean) ) ? $value : $db_params[$key];
				}
				else{
					$settings[$key] = isset($cli_args[$key]) ? $cli_args[$key] : $db_params[$key];
				}

			}
		}


		return $settings;

	}

	public static function register_task($name){

		global $fndb, $fnsql;

		$name = $fndb->escape( strval($name) );

		//--- check if the task is already registered ---//
		$fnsql->select('task_id', self::TABLE, array('task_name'=>$name));

		$row = $fndb->get_row( $fnsql->get_query() );

		if( $row and isset($row->task_id) and $row->task_id )
			return $row->task_id;
		//--- check if the task is already registered ---//

		//--- register the new task ---//

		$data = array(
			'task_name'     => $name,
			'status'        => self::STATE_STOPPED,
			'active'        => 'yes',
			'last_activity' => date(FN_MYSQL_DATE),
		);

		$fnsql->insert(self::TABLE, $data);

		if( $fndb->execute_query( $fnsql->get_query() ) )
			return $fndb->last_insert_id;

		//--- register the new task ---//

		return false;

	}

	public static function deregister_task($name){

		global $fndb, $fnsql;

		$name = $fndb->escape( strval($name) );

		$fnsql->delete(self::TABLE, array('task_name'=>$name));

		return $fndb->execute_query( $fnsql->get_query() );

	}

	public static function get_registered_tasks($offset=0, $limit=25){

		global $fndb, $fnsql;

		$offset = intval($offset);
		$limit  = intval($limit);

		$fnsql->select('*', self::TABLE);
		$fnsql->limit($offset, $limit);

		$Rows = $fndb->get_rows( $fnsql->get_query() );

		if( $Rows and count($Rows) )
			return $Rows;

		return array();

	}

	public static function is_task_active($name){

		global $fndb, $fnsql;

		$name = $fndb->escape( strval($name) );

		$fnsql->select('task_id, status, active', self::TABLE, array('task_name'=>$name));

		$row = $fndb->get_row( $fnsql->get_query() );

		if( $row and isset($row->task_id) and $row->task_id ) {
			return $row->active == 'yes';
		}

		return false;

	}

}