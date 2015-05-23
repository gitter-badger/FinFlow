<?php

namespace FinFlow;

use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;

class Importer{

    public $errors = array();

    private $file     = NULL;
    private $fileType = 'csv';

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
		    $this->file = $filepath; $this->init();
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
			throw new \Exception("{$ext} import is not supported.");

	}

	public function get_file_header($config=array()){
		//TODO...
	}


    /**
     * Imports data from a FinFlow export xml file
     * @return bool|int
     * @deprecated
     */
    public function import_xml(){

        $contents = @file_get_contents( $this->filepath );

        if( $contents ){

            if( strrpos($contents, '<FinFlowExportXML') === false ) //it is an encrypted file
                $contentXML = fn_gCrypt::decrypt($contents);
            else
                $contentXML = $contents;

        }
        else{
            $this->Errors[] = "Fisierul pentru import, nu contine date sau este inaccesibil."; return false;
        }


        libxml_use_internal_errors(true);

        $this->XML = simplexml_load_string($contentXML);

        if( empty($this->XML) ){

            $xmlErrors = libxml_get_errors();

            if( count($xmlErrors) ) foreach($xmlErrors as $error)
                $this->Errors[] = "Eroare xml la linia {$error->line} coloana {$error->column} cu mesajul: {$error->message} .";
            else
                $this->Errors[] = "Fisierul ales pentru import nu este un fisier export valid FinFlow.";

            libxml_clear_errors();

            return false;

        }

        $version = @$this->XML->attributes()->version;

        //--- import data ---//

        $data_count = 0;

        //settings
        $scount = fn_Settings::import_osmxml( $this->XML->settings ); $data_count+= $scount;

        //currencies
        $SyncCurrencies = fn_Currency::import_osmxml( $this->XML->currencies ); $data_count+= count($SyncCurrencies);

        //currencies history
        $hcount = fn_Currency::import_osmxml_history($this->XML->history, $SyncCurrencies); $data_count+= $hcount;

        //labels
        $SyncLabels = fn_Label::import_osmxml($this->XML->labels); $data_count+= count($SyncLabels);

        //accounts
        $SyncAccounts = fn_Accounts::import_osmxml($this->XML->accounts, $SyncCurrencies); $data_count+= count($SyncAccounts);

        //transactions
        $tcount = fn_OP::import_osmxml($this->XML->transactions, $SyncCurrencies, $SyncLabels, $SyncAccounts); $data_count+= $tcount;

        return $data_count; //how many records have been imported

        //--- import data ---//

    }

    public function import_csv($config=array(), $columnMatch=array()){

	    $config       = Util::clean_array($config);
	    $this->config = new LexerConfig();

	    $this->config->setToCharset('UTF-8');
	    $this->config->setDelimiter(',');
	    $this->config->getIgnoreHeaderLine();

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

	    //--- apply user config ---//

	    $lexer = new Lexer($this->config);

	    $this->interpreter = new Interpreter( $lexer );
	    $this->columnsMatch= count($columnMatch) ? $columnMatch : $this->columnsMatch;

	    $this->interpreter->addObserver(function(array $columns) use ($this){
			print_r($columns); die();
	    });

        //TODO add support for mysqldump
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