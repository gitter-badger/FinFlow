<?php
/**
 * FinFlow 1.0 - [file description]
 * @author Adrian S. (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow\Parsers;

use FinFlow\Util;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;

class CSVImporter extends AbstractImporter{

	private $file  = NULL;
	private $header= array();

	protected $errors = array();
	protected $columns= array();

	protected $interpreter = null;
	protected $lexer       = null;
	protected $config      = null;

	protected $cfg = array();

	const LINES_MAX = 65535;      //number of lines supported
	const SIZE_MAX  = 2000000000; //in bytes

	public function __construct($file, $config=array()){

		$this->config = new LexerConfig();

		$this->config->setToCharset('UTF-8');
		$this->config->setDelimiter(',');
		$this->config->setIgnoreHeaderLine(true);

		if( @file_exists($file) )
			$this->file = realpath( $file );
		else
			throw new \Exception( __t('Could not find file %s', $file) );

		if( is_browser() and ( @filesize($this->file) > self::SIZE_MAX ) )
			throw new \Exception( __t('File %1s is too large to be imported. Maximum file size accepted is %2s.', $file, Util::fmt_filesize(self::SIZE_MAX)) );

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


		$this->lexer       = new Lexer($this->config);
		$this->interpreter = new Interpreter($this->lexer);

		if(  ! isset($config['strict']) or ! boolval($config['strict']) )
			$this->interpreter->unstrict();

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

		if( count($this->columns) )
			return $this->columns;

		$fileResource = fopen($this->file, 'r');

		if( $fileResource ){

			@fseek($fileResource, 0);

			$headerLine = @fgets($fileResource);

			if( $headerLine and ( $tmp_file = ( FN_CACHE_FOLDER . '/temp-' . Util::random_string() ) )){

				if( @file_put_contents( $tmp_file , $headerLine) ){

					$previous = $this->config->getIgnoreHeaderLine();

					$this->config->setIgnoreHeaderLine(false);

					$this->import(function($columns){
						$this->columns = is_array($columns) ? array_values($columns) : @explode($this->config->getDelimiter(), strval($columns));
					}, $tmp_file);

					//remove temp file
					@unlink($tmp_file);

					//restore header line setting
					$this->config->setIgnoreHeaderLine($previous);

					return $this->columns;

				} else{
					throw new \Exception(__t('Could not save temporary file %s.', $tmp_file));
				}

			}
			else{
				throw new \Exception(__t('Could not open file %s.', $this->file));
			}
		}
		else{
			throw new \Exception(__t('Could not open file %s.', $this->file));
		}

		return false;

	}

	public function import($observer, $file=false){

		$file = $file ?: $this->file;

		if( ! is_callable($observer) )
			throw new \InvalidArgumentException('Please pass in a callable!');

		try{
			$this->interpreter->addObserver($observer);
			$this->lexer->parse($file, $this->interpreter);
		}
		catch(\Exception $e){
			$this->addError( $e->getMessage() );
		}

	}

	public function addError($msg){
		$this->errors[] = $msg;
	}

	public function getErrors(){
		return $this->errors;
	}


	/**
	 * Destructor, removes the file
	 */
	public function __destruct(){
		//TODO @unlink($this->file);
	}
}