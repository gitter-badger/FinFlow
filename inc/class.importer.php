<?php

include_once ('class.gcrypt.php');

class fn_Importer{

    public $Errors = array();

    private $XML       = NULL;
    private $filepath = 'export.xml';


    public function __construct($filepath='finflow-export.data'){
        $this->filepath = trim($filepath);

        //--- try to guess the file type based on file extension ---//
        //--- try to guess the file type based on file extension ---//
    }

    public function determine_import_type(){
        //TODO ...
    }

    /**
     * Imports data from file
     * @param string $type
     * @return bool|int
     */
    public function import($type=false){ //TODO add mysqldump support

        switch($type){

            case 'xml': return $this->import_xml();
            case 'mysql': return $this->import_mysqldump();
            case 'csv': return $this->import_csv();

        }

        return false; //no compatible file found

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

    public function get_archived_file_extension(){
        //TODO...
    }

    public function import_mysqldump(){
        //TODO add support for mysqldump
    }

    public function import_csv(){
        //TODO add support for mysqldump
    }

    public function guess_csv_fields(){
        //TODO...
    }

    public function remove_file(){
        return @unlink($this->filepath);
    }

}