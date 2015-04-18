<?php
/**
 * FinFlow 1.0 - Base unit test class
 * @author Adrian S. (adrian@finflow.org)
 * @version 1.0
 */

include_once 'init-tests.php';

class UnitTestBase extends PHPUnit_Framework_TestCase{

    /**
     * Instructs the setUp and tearDown methods to set up and tear down the database for tests
     * @var bool
     */
    protected $requires_db = false;
    protected $dbtype         = 'empty';

    public static function error($msg){
        echo ("\n\033[1;37m\033[41m" . date('Y-m-d H:i:s') . " Error: " . $msg . "\033[0m\n"); die();
    }

    public static function note($msg){
        if( ! FN_TESTS_SUPPRESS_NOTES ) echo ("\n\033[1;37m\033[44m" . date('Y-m-d H:i:s') . " Info: " . $msg . "\033[0m\n");
    }

    public static function shellcmd($cmd){
        $output = null; exec(escapeshellcmd($cmd), $output); return $output;
    }

    public function hascmd($cmd){
        $output = null; exec('which ' . escapeshellarg($cmd), $output); return $output;
    }

    public static function assetPath($relative='/'){
        $asset_path = ( FNPATH . '/tests/assets/' . ltrim($relative, '/') ); return file_exists($asset_path) ? $asset_path : null;
    }

    public static function mockAPIURL($api){
        //TODO returns a mock api url...
    }

    public function getCurrencyValueInBaseUnits($value, $currency_id=1){
        //TODO .. fn_Currency::convert($value);
    }

    public function getLastTransaction(){
        $list = fn_OP::get_operations(array('orderby'=>'sdate', 'order'=>'DESC'), 0, 1); return isset($list[0]) ? $list[0] : null;
    }

    public function getLastPendingTransaction(){
        $list = fn_OP_Pending::get_all(array('orderby'=>'sdate', 'order'=>'DESC', 'start'=>0, 'count'=>1)); return isset($list[0]) ? $list[0] : null;
    }

    public function setupDatabase( $type='demo' ){

        global $fndb; $fndb->disconnect(); $fndb->connect();

        $dbfile = self::assetPath( '/database-' . $type . '.sql' ); if( $dbfile ){
            $queries = @file_get_contents($dbfile); return $fndb->execute_multi_query($queries);
        }
        else self::error("Could not find the requested asset file " . '/database-' . $type . '.sql');

    }

    public function setUp(){
        if( $this->requires_db ){
            self::note("Importing database database-{$this->dbtype}.sql"); $this->setupDatabase($this->dbtype);
        }
    }

    public function tearDown(){

        //--- clean the database ---//
        if( $this->requires_db ){
            $this->setupDatabase('empty');
        }
        //--- clean the database ---//
    }

    public function testMinPHPVersion(){
        $prepared = version_compare(phpversion(), 5.3, '>=');  $this->assertTrue($prepared, 'FinFlow '. FN_VERSION .  ' requires PHP version 5.3');
    }

    /**
     * @depends testMinPHPVersion
     */
    public function testRequiredExtensions(){

        $environement_ready = false;

        $required =  array(
            'mbstring'=>'missing',
            'sockets'=>'missing',
            'mysqli'=>'missing',
            'json'=>'missing',
            'calendar'=>'missing',
            'date'=>'missing',
            'session'=>'missing',
            'SimpleXML'=>'missing',
            'zip'=>'missing',
            'curl'=>'missing'
        );

        $k = 0;

        foreach ($required as $ext=>$status) if ( extension_loaded($ext) ){
            $required[$ext] = 'loaded'; $k++;
        }

        if ( $k == count($required) ) $environement_ready = true;

        $this->assertTrue($environement_ready, 'The environment is not ready yet. Not all extensions required by FinFlow are available in your php install.');

    }

    /**
     * @depends testRequiredExtensions
     */
    public function testDBConnection(){
        global $fndb; $this->assertTrue($fndb->connected, 'Oops! Could not connect to database on ' . FN_DB_HOST . '. Check database credentials in ' . fn_Util::cfg_file_path() );
    }

    /**
     * @depends testRequiredExtensions
     */
    public function testWritableFolders(){
        $folders = array(
            FN_CACHE_FOLDER,
            FN_UPLOADS_DIR,
        );

        fn_Installer::setup_cache_folder(FN_CACHE_FOLDER); $writable = 0;

        foreach ($folders as $dir) if( is_writable($dir) ) {
            $writable++;
        }

        $this->assertEquals(count($folders), $writable, 'Some of the folders are not writable by the application');

    }

}