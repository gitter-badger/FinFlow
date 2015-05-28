<?php

namespace FinFlow;

use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;

class Importer{

    public $errors      = array();
	public $lineIndex   = 0;

	protected $linesMax = 65535;

    private $file     = NULL;
    private $fileType = 'csv';
	private $header   = array();

	protected $columnsMatch = array(
		'optype',
		'value',
		'ccode',
		'account',
		'user',
		'contact',
		'labels',
		'comments'
	);

	protected $interpreter = null;
	protected $config      = null;

    public function __construct($filepath){

	    if( file_exists($filepath) ){
		    $this->file = $filepath;
	    }
	    else
		    throw new \Exception("File {$filepath} not found!");
    }

	public function import($config=array()){

		$ext    = File::getExtension($this->file);
		$method = ( 'import_' . $ext );

		if( method_exists(__CLASS__, $method) ){
			$result = call_user_func(array(__CLASS__, $method), $config); return $result;
		}
		else
			throw new \Exception(__t('%s file import is not supported.', $ext));

	}

	public function get_file_header($config=array()){

		$fileResource = fopen($this->file, 'r');

		die($this->file);//TODO...

		if( $fileResource ){

			@fseek($fileResource, 0); $headerLine = @fgets($this->file);

			if( $headerLine ){
				$filename = ( FN_CACHE_FOLDER . '/temp-' . Util::random_string() ); @file_put_contents( $filename , $headerLine);
			}

			die( $filename ); //TODO...

			$config['ignore_header'] = false;

			$this->import($config);

		}

	}

    public function import_csv($config=array(), $columnMatch=array(), $lines=65535){

	    $config       = Util::clean_array($config);
	    $this->config = new LexerConfig();

	    $this->config->setToCharset('UTF-8');
	    $this->config->setDelimiter(',');
	    $this->config->setIgnoreHeaderLine(true);

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
		    $this->config->setIgnoreHeaderLine($config['ignore_header']);

	    if( isset($config['ignore_header']) )
		    $this->config->setIgnoreHeaderLine( boolval($config['ignore_header']) );

	    //--- apply user config ---//

	    $lexer = new Lexer($this->config);

	    $this->linesMax    = intval($lines);
	    $this->interpreter = new Interpreter( $lexer );
	    $this->columnsMatch= count($columnMatch) ? $columnMatch : $this->columnsMatch;

	    if( ! isset($config['strict']) )
		    $this->interpreter->unstrict();

	    $this->interpreter->addObserver(function(array $columns){
		    $this->lineIndex++; print_r($columns); die(); //TODO...
	    });

        //TODO add support for mysqldump
	    $lexer->parse($this->file, $this->interpreter);

    }

	public function import_tsv(){
		//TODO add support for tsv
	}

	public function import_xls(){
		//TODO add support for xls
	}

    public function cleanup(){
        @unlink( $this->filepath );
    }

}