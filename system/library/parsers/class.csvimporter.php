<?php
/**
 * FinFlow 1.0 - [file description]
 * @author Adrian S. (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow\Parsers;

use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;

class CSVImporter extends AbstractImporter{

	private $file  = NULL;
	private $header= array();

	protected $errors = array();

	protected $interpreter = null;
	protected $config      = null;

	protected $cfg = array();

	public function __construct($file, $config=array()){

		$this->config = new LexerConfig();

		$this->config->setToCharset('UTF-8');
		$this->config->setDelimiter(',');
		$this->config->setIgnoreHeaderLine(true);

		$this->init($config);

	}

	public function init($config=array()){

		//--- apply user config ---//
		if( isset($config['delimiter']) )
			$this->config->setDelimiter($config['delimiter']);

		if( isset($config['enclosure']) )
			$this->config->setEnclosure($config['enclosure']);

		if( isset($config['escape']) )
			$this->config->setEscape($config['escape']);

		if( isset($config['charset']) )
			$this->config->setFromCharset($config['charset']);

		if( isset($config['ignore_header']) )
			$this->config->setIgnoreHeaderLine( boolval($config['ignore_header']) );

		//--- apply user config ---//

		//update local cfg
		$this->cfg = array_merge($this->cfg, $config);

	}

	public function config($config){
		$this->init($config);
	}

	public function set($key, $value=1){
		$this->init(array($key=>$value));
	}

	public function get($key){
		return isset($this->cfg[$key]) ? $this->cfg[$key] : null;
	}

	public function getColumns(){
		//TODO...
	}

	public function import(){
		//TODO...
	}

	public function addError($msg){
		$this->errors[] = $msg;
	}

	public function getErrors(){
		return $this->errors;
	}
}