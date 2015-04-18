<?php
/**
 * FinFlow 1.0 - Cache Implementation
 * @author Adrian S. (http://adrian.silimon.eu/)
 * @version 1.0
 */

class fn_Cache{

    public static $file_engine = 'file';

    private static $engines = array('file');

    private $engine = 'file';
    private $params = array();

    private $initialized = false;

    public function __construct($engine='file', $params=array()){
        return $this->initialize($engine, $params);
    }

    public function initialize($engine='file', $cfg=array()){
        if( in_array($engine, self::$engines) ) {
            $this->engine = $engine; $this->params = $cfg;
        }


        switch( $engine ){
            case self::$file_engine : { //initialize file engine

                $dir = $this->get_engine('folder');

                if($dir and !is_dir($dir) )
                    $this->initialized = ( @mkdir($dir, 0775, true) and @file_put_contents($dir . DIRECTORY_SEPARATOR . 'time.cache', time()) );
                else
                    $this->initialized = is_writable($dir);

            }
        }
    }

    public function is_initialized(){
        return $this->initialized;
    }

    public function get_engine($config=null){
        return $config ? $this->params[$config] : $this->engine;
    }

    public function get_engine_cfg($key){
        return $this->get_engine($key);
    }

    public function store($key, $data=0, $ttl=3600){

        switch ( $this->engine ){

            case self::$file_engine: return $this->file_store_add($key, $data, $ttl);

            default: trigger_error(__CLASS__ . ' Error: Engine not defined!', E_USER_WARNING);
        }

    }

    public function get($key){
        switch ( $this->engine ){

            case self::$file_engine: return $this->file_store_get($key);

            default: trigger_error(__CLASS__ . ' Error: Engine not defined!', E_USER_WARNING);
        }

    }

    public function clean($minutes=25){
        switch ( $this->engine ){

            case self::$file_engine: return $this->file_store_clean($minutes);

            default: trigger_error(__CLASS__ . ' Error: Engine not defined!', E_USER_WARNING);

        }
    }

    public function file_store_add($key, $data=0, $ttl=3600){

        $expire = date('YmdHis', ( time() + intval($ttl) ) );  $filename = ( md5($key) . '.' .$expire );

        if(  is_object($data) or is_array($data)  ){
            $filename.= ( '.scache' ); $data = @serialize($data);
        }
        else{
            $filename.= ( '.cache' );
        }

        $dir = ( rtrim($this->get_engine('folder'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);

        //--- remove previous appearances ---//
        $selector = md5($key);
        $selector = ( $dir . "{$selector}*cache"); //something we can feed the glob function

        $matches = glob($selector); if( count($matches) ) foreach($matches as $path) @unlink($path);
        //--- remove previous appearances ---//

        return @file_put_contents( $dir . $filename, $data );

    }

    public function file_store_get($key){

        $dir = ( rtrim($this->get_engine('folder'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);

        $selector = md5($key);
        $selector = ( $dir . "{$selector}*cache"); //something we can feed the glob function

        $matches = glob($selector); if( count($matches) ) foreach($matches as $path){

            $parts = @explode('.', basename($path)); $expire = $parts[1];  //yyyymmddhhiiss

            $value = $parts[2] == 'scache' ? @unserialize( @file_get_contents($path) ) : @file_get_contents($path);

            if( $expire < date('YmdHis') ) @unlink($path);

            return $value;

        }

        return null;

    }

    public function file_store_clean($minutes=10){

        $ts_from = @strtotime("-{$minutes} minutes");
        $ts_to    = time();

        $dir = ( rtrim($this->get_engine('folder'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);

        $steps = fn_Util::date_diff(date(FN_MYSQL_DATE, $ts_from), date(FN_MYSQL_DATE, $ts_to), 'minutes');

        for( $i=1; $i<=$steps; $i++ ){

            $selector = $i > 1 ? date('YmdHi', @strtotime("+$i minutes", $ts_from)) : date('YmdHi', @strtotime("+$i minute", $ts_from));
            $selector = ( $dir . "*.{$selector}*cache"); //something we can feed the glob function

            $files = glob($selector); if( count($files) ) foreach($files as $path) @unlink($path);

        }

    }

    public function memcache_store_add($key, $data=0, $ttl=3600){
        //TODO
    }

}