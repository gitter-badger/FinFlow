<?php
/**
 * Class fn_Upgrade handles product upgrades
 * @version 1.0
 * @author Adrian7 (http://adrian.silimon.eu/)
 */

class fn_Upgrader{

    //TODO replace to subdomain ping.finflow.org and add https support
    static $versionURL = 'http://finflow.org/ping/latest.version.txt';

    static $changelogURL        = 'http://finflow.org/changelog.php';
    static $announcementURL  = 'http://finflow.org/blog/finflow-versiunea-{version}/';

    static $pingActiveVersionURL = 'http://finflow.org/ping/upgraded.php?v=';

    public static function get_latest_version(){

        if( $data = @file_get_contents(self::$versionURL) ){
            $data = @explode(" ", $data); if( count($data) > 1 ) return array('version'=>trim($data[0]), 'dbversion'=>trim($data[1]), 'checksum'=>strtoupper( trim($data[2]) ), 'url'=>trim($data[3]));
        }

        return false;

    }

    public static function upgrade_available(){

        $vcurrent = FN_VERSION;
        $latest      = self::get_latest_version();

        if( $latest and isset($latest['version']) ) {
            $vlatest = $latest['version'];

            $vcurrent = @explode('.', $vcurrent);
            $vlatest    = @explode('.', $vlatest);

            if( count($vlatest) > count($vcurrent) )
                return $latest;

            $k = 0;

            if( count($vlatest) and count($vcurrent) )
                foreach($vlatest as $l){
                    $c = $vcurrent[$k]; if( $l > $c ) return $latest; $k++;
                }

        }

        return false;

    }

    public static function get_announcement_url($version){
        $version = str_replace('.', '-', $version); return str_replace('{version}', $version, self::$announcementURL);
    }

    public static function start($version){

        set_time_limit(0);

        self::update_status(5, "incepe actualizarea");

        if( isset($version['url']) ){

            $downloaded = self::download($version['url']);

            if( $downloaded ){

                $size           = @filesize($downloaded);
                $checksum   = strtoupper( @md5_file($downloaded) );

                $db_ready   = false;

                if( ( $size > 10 ) and ( $checksum == $version['checksum'] ) ){

                    $dbversion = floatval( FN_DB_VERSION );

                    if( $dbversion < $version['dbversion'] ){

                        //--- upgrade the database to new version ---//
                        $db_ready = self::upgrade_db($downloaded, $version['dbversion']);
                        //--- upgrade the database to new version ---//

                    }
                    else $db_ready = true;

                    if( $db_ready ){

                        //---- un-zip the file ---//

                        self::update_status(75, "dezpachetez pachetul de actualizare");

                        //--- extract and overwrite the old files ---//
                        $dispatched = fn_Util::unzip($downloaded, FN_UPGRADE_DIR);
                        //--- extract and overwrite the old files ---//

                        if( $dispatched ){

                            $upgrade_files_dir = ( FN_UPGRADE_DIR . DIRECTORY_SEPARATOR . 'finflow' );

                            if( file_exists($upgrade_files_dir) and is_dir($upgrade_files_dir)  ){

                                fn_Util::fcopy($upgrade_files_dir, FNPATH);

                                self::clean($downloaded); //clean upgrade files

                                self::update_status(100, "Actualizarea s-a terminat.");

                            }
                            else self::update_status(1, "Eroare: pachetul de actualizare este incomplet.");
                        }
                        else self::update_status(1, "Eroare: pachetul de actualizare nu poate fi dezarhivat.");

                        //---- un-zip the file ---//
                    }
                    else self::update_status(1, "Eroare: pachetul de actualizare descarcat este incomplet.");

                }
                else self::update_status(1, "Eroare: pachetul de actualizare descarcat este incomplet.");
            }
            else self::update_status(1, "Eroare: pachetul de actualizare nu poate fi descarcat.");

        }else
            self::update_status(1, "Eroare: versiunea ceruta nu este disponibila.");
    }

    public static function stop(){
        //TODO allow user to stop the upgrade process
    }

    public static function clean($downloaded='download.zip'){

        //--- clean upgrade files ---//
        fn_Util::remove_dir(FN_UPGRADE_DIR . '/finflow');

        @unlink(FNPATH . '/config-sample.php');

        fn_Installer::clean();

        return @unlink( $downloaded );

        //--- clean upgrade files ---//
    }

    public static function display_progress($autostart=1, $action='upgrade'){ ?>

        <progress id="updateProgress" style="width: 100%; height: 30px;" value="1" max="100"></progress>

        <p id="upgrade-msg" class="align-center msg" style="border: none; font-style: italic; box-shadow: inset 1px 1px 2px #888;">
            &#238;ncepe actualizarea...
        </p>

        <br class="clear"/>

        <p class="align-center">
            <button class="btn" id="btnBack" onclick="window.location.href='../index.php';">
                <span class="icon-arrow-left"></span> &#206;napoi
            </button>

            <button class="btn btn-primary" id="btnContinue" style="display: none; margin-left: 50px;" onclick="window.location.href='../index.php';">
                 Ce mai a&#351;tep&#355;i! Vezi noua versiune <span class="icon-arrow-right"></span>
            </button>
        </p>

        <script type="text/javascript">

            var latestStatus      = null;
            var updateFinished = false;
            var updateError     = false;

            function fn_update_finished(){
                if( !updateError ){
                    $('#upgrade-msg').text("Gata! Procesul de update s-a terminat!");  $('#updateProgress').val(100); $('#btnContinue').show();
                }
                else  $('#btnBack').attr('disabled', false);

                updateFinished = true; latestStatus = null;
            }

            function fn_update_status_msg(message){

                if( ( message !== undefined ) && message.length ){

                    if( ( message.indexOf('Eroare') > -1 ) || ( message.indexOf('eroare') > -1 ) || message.indexOf('error') > -1 ){
                        $('#upgrade-msg').addClass('error'); updateError = true; updateFinished = true;
                    }

                    $('#upgrade-msg').text(message);
                }
            }

            function fn_start_upgrade(){
                $('#btnBack').attr('disabled', true); $.get('upgrade.php?ustart=1', function(){ setTimeout('fn_update_finished()', 800) });
            }

            function fn_upgrade_status(){
                $.get('upgrade.php?ustatus=1', function(data){ console.log(data);
                    if( ( data.progress !== undefined ) && ( data != latestStatus ) && !updateFinished ) {
                        $('#updateProgress').val(data.progress); fn_update_status_msg(data.message); latestStatus = data;
                    }

                    if( !updateFinished ) setTimeout('fn_upgrade_status()', 500);

                }, 'json');
            }

            $(document).ready(function(){
                $('#btnContinue').hide('fast'); <?php if($autostart): ?>fn_start_upgrade();<?php endif; ?> fn_upgrade_status();
            });
        </script>

        <?php

    }

    public static function get_status($jsonify=1){

        if( isset($_SESSION['fn_upgrade_status']) )
           $status = $_SESSION['fn_upgrade_status'];
        else
            $status = array('progress'=>1, 'message'=>"unknown...");

        if( $jsonify )
            return json_encode($status);

        return $status;
    }

    public static function update_status($progress, $msg="new status..."){
        $_SESSION['fn_upgrade_status']= array('progress'=>intval($progress), 'message'=>strval($msg));
    }

    public static function upgrade_db($package_file, $version){

        global $fndb;

        self::update_status(45, "actualizez baza de date la versiunea " . $version);

        //--- check the database upgrade file ---//
        $db_upgrade_file = ('finflow/setup/assets/database.upgrade-' . str_replace('.', '_', FN_DB_VERSION) .'-' . str_replace('.', '_', $version) . '.sql');

        $extracted_db = fn_Util::unzip($package_file, FN_UPGRADE_DIR, array($db_upgrade_file));

        if( $extracted_db ){
            $extracted_db = (FN_UPGRADE_DIR . '/' . $db_upgrade_file); //TODO
        }else {
            self::update_status(1, "Eroare: pachetul de actualizare descarcat este incomplet. Lipseste fisierul pentru actualizarea bazei de date."); return false;
        }
        //--- check the database upgrade file ---//

        //--- run queries for upgrading the database ---//
        $queries = @file_get_contents($extracted_db);

        if( strlen($queries) ){
            $done = $fndb->execute_multi_query($queries); return $done;
        }
        else{
            self::update_status(1, "Eroare: pachetul de actualizare descarcat este incomplet. Lipseste fisierul pentru actualizarea bazei de date"); return false;
        }

        //--- run queries for upgrading the database ---//

    }

    public static function download($url){ //TODO move to fn_Util

        self::update_status(10, "descarc pachetul de actualizare");

        if( !function_exists('curl_init') ) return false;

        $path = ( FN_UPGRADE_DIR . '/' . basename($url) );

        $fp = @fopen ($path, 'w+');

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_FILE, $fp); // write curl response to file
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
        curl_exec($ch); // get curl response
        curl_close($ch);

        fclose($fp);

        return $path;
    }

}