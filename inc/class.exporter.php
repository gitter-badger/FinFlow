<?php

include_once ('class.gcrypt.php');

class fn_Exporter{

    private $XML = NULL;

    private $filename = 'export';

    public function __construct($filename='finflow-export.fdata'){
        $this->XML = new SimpleXMLElement('<FinFlowExportXML/>');

        $this->XML->addAttribute('version', FN_VERSION);
        $this->XML->addAttribute('date', date('Y-m-d'));

        $this->filename = trim($filename);
    }

    public function save_file($encrypt=TRUE){

        if( $encrypt ){
            $xml = $this->XML->asXML(); $encrypted = fn_gCrypt::encrypt($xml); return @file_put_contents($this->filename, $encrypted);
        }

        return $this->XML->asXML( $this->filename );
    }

    public function add_section($data, $name='section', $ename='element', $asCdata=FALSE){

        $xml = $this->XML->addChild($name);

        if( is_array($data) ) foreach($data as $article){

            if( is_array($article) ){
                $element = $xml->addChild($ename); foreach($article as $key=>$value) $element->addChild($key, fn_Util::escape_xml( @strval($value) ));
            }else
                $xml->addChild($ename, fn_Util::escape_xml( @strval($article) ) );
        }
    }

    /**
     * Exports data from database to file
     * @param bool $encrypt encrypt the exported file
     * @return int|mixed
     */
    public function export_data($encrypt=TRUE){

        //settings
        $Settings        = fn_Settings::get_export_array(); $this->add_section($Settings, 'settings', 'setting');

        //currencies
        $Currencies     = fn_Currency::get_currencies_export_array(); $this->add_section($Currencies, 'currencies', 'currency');

        //currencies history
        $History          = fn_Currency::get_currencies_history_export_array(); $this->add_section($History, 'history', 'entry');

        //accounts
        $Accounts       = fn_Accounts::get_export_array(); $this->add_section($Accounts, 'accounts', 'account');

        //transactions
        $Transactions  = fn_OP::get_export_array(); $this->add_section($Transactions, 'transactions', 'transaction');

        //labels
        $Labels           = fn_Label::get_export_array(); $this->add_section($Labels, 'labels', 'label');

        return $this->save_file($encrypt);

    }


}