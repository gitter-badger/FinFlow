<?php
/**
 * FinFlow 1.0 - File cache implementation
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow\Drivers;

class FileCache extends AbstractCache{

	protected $cfg = array();

	public function __construct(){
		//TODO
	}

	public function init($config=array()){
		$this->cfg = parse_args($config);
	}

	public function set($key, $value=1){
		//TODO
	}

	public function get($key){
		//TODO
	}

	public function remove($key){
		//TODO
	}

	public function clean(){
		//TODO
	}

	public function stats(){
		//TODO
	}
}