<?php
/**
 * Class fn_Installer handles product installs
 */

class fn_Installer{

    public static function create_cfg_file($vars){

        //--- create default vars ---//
        if( empty($vars['secret']) ) $vars['secret'] = fn_Util::get_rand_string(24);
        //--- create default vars ---//

        $template = file_get_contents( FNPATH . '/setup/assets/config.sample.txt' );

        if( $template ){

            foreach($vars as $key=>$value) $template = str_replace('{' . $key . '}', $value, $template);

            $template = "<?php\n{$template}";

            return @file_put_contents(FNPATH . DIRECTORY_SEPARATOR . 'config.php', $template);

        }

         return FALSE;
    }

    public static function check_db_connection($host, $user, $password='', $db_name=FALSE){

        if( empty($db_name) ) $db_name = $user;

        if( $link_id = @mysqli_connect($host, $user, $password) ) return @mysqli_select_db($link_id, $db_name);

        return FALSE;

    }

    public static function have_cfg_file(){
        return @file_exists( FNPATH . DIRECTORY_SEPARATOR . 'config.php' );
    }

    public static function setup_cache_folder($folder){

        $folder = rtrim($folder, DIRECTORY_SEPARATOR); $cachepath = FNPATH;

        if( strpos($folder, DIRECTORY_SEPARATOR) === false ){ //simple path
            $cachepath = ( FNPATH . DIRECTORY_SEPARATOR . $folder );
        }
        else {
            $parts = @explode(DIRECTORY_SEPARATOR, $folder); if( count($parts) ) foreach($parts as $part){
                if( strlen($part) and ( $part != '.' ) ){
                    if( $part == '..' ) //upper directory
                        $cachepath = substr($cachepath, 0, strrpos($cachepath, DIRECTORY_SEPARATOR));
                    else
                        $cachepath.= (DIRECTORY_SEPARATOR . $part);
                }
            }
        }

       if( ( !is_dir($cachepath) and @mkdir($cachepath, 0755, true) ) or ( is_dir($cachepath) and is_writable($cachepath) ) ){

           $htaccess = "<IfModule mod_rewrite.c>\nRewriteEngine On\ndeny from all\n</IfModule>";

           if( @file_put_contents($cachepath . DIRECTORY_SEPARATOR . '.htaccess', $htaccess) )
               return $cachepath;
       }

        fn_Log::to_file("Nu se poate accesa directorul cache la " . $cachepath);

        return false;

    }

    public static function install_db($host='localhost', $user='root', $password='', $name='test'){

        global $fndb;

        if( !$fndb->connected )
            $fndb = new MySQLiDB($host, $user, $password, $name); //we need a working db

        if( !$fndb->connected ) return false;

        $db_install_file = ( FNPATH . '/setup/assets/database.setup.sql' );

        $queries = @file_get_contents($db_install_file);

        if( strlen($queries) )
            $done = $fndb->execute_multi_query($queries);

        return $done;

    }

    public static function setup_default_currency($ccode){

        global $fndb, $fnsql; $ccode = strtoupper($ccode); $details = fn_Currency::get_currency_details( $ccode, ( FNPATH . '/setup/assets/currencies.json' ) );

        if( $ccode ){

            $fnsql->insert(fn_Currency::$table, array('csymbol'=>$details['symbol'], 'cname'=>$details['name'], 'ccode'=>$ccode, 'cexchange'=>1));

            return $fndb->execute_query( $fnsql->get_query() );
        }

        return false;
    }

    /**
     * Cleans the /setup folder, except for the upgrade.php, header.php, footer.php and the index.html
     * @return bool
     */
    public static function clean(){

        $cleanList = array(
            'install.php',
            'license.htm',
            'requirements.php',
            'setup-cfg.php',
            'setup-user.php',
            'terms.php',
            'assets',
            '.remotehost'
        );

        foreach($cleanList as $file){
            $path = ( FNPATH . '/setup/' . $file ); if( is_dir($path) ) fn_Util::remove_dir($path); else @unlink($path);
        }

        @unlink(FNPATH . '/config-sample.php');

        return TRUE;

    }

    public static function get_session_ip(){
        return @file_get_contents(FNPATH . '/setup/.remotehost');
    }

    public static function bind_session_ip(){

        $existing_ip = self::get_session_ip();

        if( empty($existing_ip) ) {

            if( !isset($_SESSION['fn_install_host']) )
                $_SESSION['fn_install_host'] = $_SERVER['REMOTE_ADDR'];

            $saved =  @file_put_contents(FNPATH . '/setup/.remotehost', $_SESSION['fn_install_host']);

            if( $saved )
                return $saved;
            else
                fn_UI::fatal_error("Nu se poate salva fi&#351;ierul sesiune pe server. Verific&#259; permisiunile fi&#351;ierelor &#351;i &#238;ncearc&#259; din nou.");

        }

        return TRUE;

    }

    public static function is_valid_session_ip(){

        $binded_ip = self::get_session_ip();

        if( strlen($binded_ip) )
            return ( $binded_ip == $_SERVER['REMOTE_ADDR'] );

        return self::bind_session_ip();
    }

}