<?php

include_once ('class.gcrypt.php');

class fn_Importer{

    public $Errors = array();

    private $XML       = NULL;
    private $filepath = 'export.xml';

    /**
     * List of supported versions
     * @var array
     */
    public static $compatibility = array('0.7.2', '0.8.8', '0.9.2', FN_VERSION);


    public function __construct($filepath='finflow-export.data'){
        $this->filepath = trim($filepath);
    }

    /**
     * Imports data from file
     * @param string $type
     * @return bool|int
     */
    public function import($type='mysqldump'){ //TODO add mysqldump support

        $contents = @file_get_contents($this->filepath);

        if( $contents ){
            if( strrpos($contents, '<FinFlowExportXML') === FALSE) //it is an encrypted file
                $contentXML = fn_gCrypt::decrypt($contents);
            else
                $contentXML = $contents;

            return $this->import_xml($contentXML);
        }
        else $this->Errors[] = "Fisierul pentru import, nu contine date sau este inaccesibil.";

        return FALSE;
    }


    public function import_xml($xmlstring){

        libxml_use_internal_errors(TRUE);

        $this->XML = simplexml_load_string($xmlstring);

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

        if( in_array($version, self::$compatibility) ){
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
        else $this->Errors[] = "Versiunea fi&#351;ierului export nu este compatibil&#259; cu aceast&#259 versiune a FinFlow.";


        return false;
    }

    public function remove_file(){
        return @unlink($this->filepath);
    }

}