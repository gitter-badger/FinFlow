<?php

namespace FinFlow;

use Ghunti\HighchartsPHP;

class Util{

    /**
     * A list of translitaration(s) for generating slugs
     * @var array
     */
    protected static $_transliteration = array(
        '/ä|æ|ǽ/' => 'ae',
        '/ö|œ/' => 'oe',
        '/ü/' => 'ue',
        '/Ä/' => 'Ae',
        '/Ü/' => 'Ue',
        '/Ö/' => 'Oe',
        '/À|Á|Â|Ã|Å|Ǻ|Ā|Ă|Ą|Ǎ/' => 'A',
        '/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª/' => 'a',
        '/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
        '/ç|ć|ĉ|ċ|č/' => 'c',
        '/Ð|Ď|Đ/' => 'D',
        '/ð|ď|đ/' => 'd',
        '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě/' => 'E',
        '/è|é|ê|ë|ē|ĕ|ė|ę|ě/' => 'e',
        '/Ĝ|Ğ|Ġ|Ģ/' => 'G',
        '/ĝ|ğ|ġ|ģ/' => 'g',
        '/Ĥ|Ħ/' => 'H',
        '/ĥ|ħ/' => 'h',
        '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ/' => 'I',
        '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı/' => 'i',
        '/Ĵ/' => 'J',
        '/ĵ/' => 'j',
        '/Ķ/' => 'K',
        '/ķ/' => 'k',
        '/Ĺ|Ļ|Ľ|Ŀ|Ł/' => 'L',
        '/ĺ|ļ|ľ|ŀ|ł/' => 'l',
        '/Ñ|Ń|Ņ|Ň/' => 'N',
        '/ñ|ń|ņ|ň|ŉ/' => 'n',
        '/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ/' => 'O',
        '/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º/' => 'o',
        '/Ŕ|Ŗ|Ř/' => 'R',
        '/ŕ|ŗ|ř/' => 'r',
        '/Ś|Ŝ|Ş|Š|Ș/' => 'S',
        '/ś|ŝ|ş|ș|š|ſ/' => 's',
        '/Ţ|Ț|Ť|Ŧ/' => 'T',
        '/ţ|ť|ț|ŧ/' => 't',
        '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ/' => 'U',
        '/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ/' => 'u',
        '/Ý|Ÿ|Ŷ/' => 'Y',
        '/ý|ÿ|ŷ/' => 'y',
        '/Ŵ/' => 'W',
        '/ŵ/' => 'w',
        '/Ź|Ż|Ž/' => 'Z',
        '/ź|ż|ž/' => 'z',
        '/Æ|Ǽ/' => 'AE',
        '/ß/' => 'ss',
        '/Ĳ/' => 'IJ',
        '/ĳ/' => 'ij',
        '/Œ/' => 'OE',
        '/ƒ/' => 'f'
    );

    /**
     * A list of extensions allowed for files upload
     * @var array
     */
    public static $allowedExtensions = array(
        'pdf', 'png', 'jpg', 'gif', 'doc', 'xls', 'docx', 'xlsx', 'psd', 'odt', 'ods', 'odf', 'txt', 'rtf', 'ppt', 'pptx', 'mp3', 'ogg', 'mp4', 'avi', 'mkv', 'zip', 'gz', 'tar', 'swf', 'fla', 'aac'
    );

    /**
     * Given a date, will return the time passed from as xx &lt;time&gt; ago
     * @param string $date
     * @param string $custom_tense
     * @param array $before_singulars
     * @param array $before_plurals
     * @param bool $is_unix_date
     * @return string
     */
    public static function nicetime($date, $custom_tense="", $before_singulars=array(), $before_plurals=array(), $is_unix_date=FALSE){

        if( empty($date) ) {
            return "No date provided";
        }

	    //TODO get these from translation files
        $periods         	= array("secund&#259;", "minut", "or&#259;", "zi", "s&#259;pt&#259;m&#226;n&#259;", "lun&#259;", "an", "decad&#259;");
        $periods_plural  	= array("secunde", "minute", "ore", "zile", "s&#259;pm&#259;m&#226;ni", "luni", "ani", "decade");
        $lengths         	= array("60", "60", "24", "7", "4.35", "12", "10");

        $singulars = array(
            'f'     =>array($periods[0], $periods[2], $periods[3], $periods[4], $periods[5], $periods[7]),
            'm'   => array($periods[1], $periods[6])
        );

        $plurals  = array(
            'f'     =>array($periods_plural[0], $periods_plural[1], $periods_plural[2], $periods_plural[3], $periods_plural[4], $periods_plural[5], $periods_plural[7]),
            'm'   => array($periods_plural[6])
        );

        $now = time();

        if ($is_unix_date)
            $unix_date  = $date;
        else
            $unix_date  = @strtotime($date);

        // check validity of date
        if( empty($unix_date) ) {
            return "Bad date";
        }

        // is it future date or past date
        if($now > $unix_date) {
            $difference = $now - $unix_date;
            $tense      = "acum";

        } else {
            $difference = $unix_date - $now;
            $tense      = "peste";
        }

        if( strlen($custom_tense) )
	        $tense = $custom_tense;

	    $genre_prefix = "";

        for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
            $difference /= $lengths[$j];
        }

        $difference = round($difference);

        if($difference != 1) {
            $period = $periods_plural[$j];

            if( count($before_plurals) )
                $genre_prefix  = ( array_search($period, $plurals['f']) === FALSE ) ? $before_plurals['m'] : $before_plurals['f'];

            if ($difference >= 20) $period = ("de ".$period);

        }
        else{
            $period = $periods[$j];

            if( count($before_singulars) )
                $genre_prefix  = ( array_search($period, $singulars['f']) === FALSE ) ? $before_singulars['m'] : $before_singulars['f'];
        }

        return "{$tense} {$genre_prefix} $difference $period";
    }
	
	public static function set_date($year=NULL, $month=FALSE, $day=NULL, $format='Y-m-d H:i:s'){
		
		$date = "";
		
		if ( $year )
			$year = intval($year);
		else 
			$year = "1970"; // year 0 (see http://en.wikipedia.org/wiki/Unix_time)
		
		if ( $month )
			$month = self::add_leading_zeros(intval($month));
		else 
			$month = "01";
		
		if ( $day )
			$day	= self::add_leading_zeros(intval($day));
		else
			$day = "01";
		
		return date($format, strtotime("{$year}-{$month}-{$day}"));
		
	}

	public static function add_leading_zeros($input, $stdlen=2, $ignore_empty=FALSE){

        if( $ignore_empty and ( strlen($input) == 0 ) ) return "";

		if ( strlen($input) < $stdlen ) for ($i=0; $i<($stdlen - strlen($input)); $i++){
			$input = "0{$input}";
		}
		
		return $input;
		
	}

    public static function get_input($key, $xss_filter=true){
        $value = isset($_GET[$key]) ? $_GET[$key] : ( isset($_POST[$key]) ? $_POST[$key] : null ); if( $value ) return ( $xss_filter ? self::xss_filter($value) : $value ); return null;
    }

	public static function get_arg($index, $xss_filter=false){
		//TODO...
	}

    public static function array_value($array, $key, $xss_filter=false){
        $value = isset($array[$key]) ? $array[$key] : null; if( $value ) return ( $xss_filter ? self::xss_filter($value) : $value ); return null;
    }


	/**
	 * Filters input for xss
	 * @param string|array $input
	 *
	 * @return mixed
	 */
    public static function xss_filter($input){
        return is_array($input) ? filter_var_array($input, FILTER_SANITIZE_STRING) : filter_var($input, FILTER_SANITIZE_STRING);
    }

	public static function get_relative_time($days="", $months="", $years="", $relative=NULL, $past=TRUE, $format='Y-m-d'){
		
		if ( empty($relative) )
			$relative = time();
		else 
			$relative = @strtotime($relative);
		
		if( $relative > 0 ){
			
			
			if ( intval($days) > 0 ) 		
				$days = ( intval($days) . " days");
			else 
				$days = "";
			
			if ( intval($months) > 0 ) 	
				$months = ( intval($months) . " months");
			else 
				$months = "";
			
			if ( intval($years) > 0 ) 		
				$years = ( intval($years) . " years");
			else 
				$years = "";
			
			$diff = trim("{$days} {$months} {$years}");
			
			if ( $past ) //calculate a date in the past			
				return date($format, strtotime("-{$diff}", $relative));
			else
				return date($format, strtotime("+{$diff}", $relative));
			
		}
		
		return NULL;
		
	}
	
	public static function date_diff($from, $to, $output="days"){ //ANSI-formatted dates
		
		$date1 = new \DateTime($from);
		$date2 = new \DateTime($to);
		
		$interval = $date1->diff($date2);
		
		if ( is_object($interval) ){

            $days = intval( $interval->format('%a') );

            if ( $output == 'years' )
                return $interval->format('%y');

			if ( $output == 'months' )
				return ( ( $interval->format('%y') * 12 ) + $interval->format('%m') );

            if ( $output == 'days' )
                return $days;

            if ( $output == 'hours' )
                return ( $days * 24 ) +  $interval->h;

            if ( $output == 'minutes' )
                return $days > 0 ? ( $days * 24  * 60 ) + $interval->i : ( $interval->h * 60 ) + $interval->i ;

            if ( $output == 'seconds' )
                return $days > 0 ? ( $days * 24 * 60 * 60 ) + $interval->s : ( $interval->h * 3600 ) + ( $interval->i * 60 ) + $interval->s;

		}

		return NULL;
		
	}

	/**
	 * Removes empty elements from an array
	 * @param $array
	 *
	 * @return mixed
	 */
	public static function clean_array($array){
		
		if ( is_array($array) and count($array)) foreach ($array as $key=>$value){
			if( is_array($value) )
				self::clean_array($value);
			elseif ( empty($value) )
				unset($array[$key]);
		}
			
		return $array;
	}

	public static function trim_query_string($query_str){
		return trim(trim($query_str), "&?=");
	}
	
	public static function cronjob($script, $minute="*", $hour="*", $day="*", $month="*", $days_of_week='*', $executable="php -q", $apply_server_offset=TRUE){
		
		if ( $apply_server_offset and ( $hour != '*' ) ) {
			$offset = self::get_server_timezone_offset(FN_TIMEZONE); $hour+= $offset; if ( $hour >= 24 ) $hour = $hour-24;
		}
		
		return "{$minute} {$hour} {$day} {$month} {$days_of_week} {$executable} {$script}";

	}

	/**
	 * Encrypts the input with padded salt;
	 * @param string $input
	 * @param string $salt
	 *
	 * @return string
	 */
    public static function encrypt($input, $salt=null){
	    return Cryptographer::encrypt($input, $salt);
    }

	/**
	 * Decrypts an input previously encrypted with self::s_encrypt()
	 * @param $input
	 * @param string $salt
	 * @see s_encrypt
	 *
	 * @return string
	 */
    public static function decrypt($input, $salt=null){
	    return Cryptographer::decrypt($input, $salt);
    }

	/**
	 * Generates a random string
	 * @param int $length
	 *
	 * @return string
	 */
	public static function random_string($length=12){
		$seed = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length) . substr(md5( time() + mt_rand(1, 9999999)), $length); return substr( str_shuffle($seed), 0, $length );
	}

	//TODO move to highcarts helper
    public static function highchart_prepare_data($Sums, $currency_code="RON", $series_color=FALSE, $negative_color=FALSE){

        $chartdtspan  	= "";
        $categories 		= array();
        $seriesdata 		= array();
        $series				= array();

        $k=0; $sumscount = count($Sums);

        foreach ($Sums as $sum){

            $k++;

            $ld = ( $sum['year'] . "-01-01");
            $fm	= FN_YEAR_FORMAT;

            if ( isset($sum['month']) ){
                $ld = ( $sum['year'] . "-" . $sum['month'] . "-01" );
                $fm	= FN_MONTH_FORMAT;
            }

            if ( isset( $sum['day'] ) ){
                $ld = ( $sum['year'] . "-" . $sum['month'] . "-" . $sum['day'] );
                $fm	= FN_DAY_FORMAT;
            }

            $fmdate = UI::translate_date(date($fm, strtotime($ld)));

            //--- better illustrate the title ---//
            if ( $k == 1 )
	            $chartdtspan.= " {$fmdate} -";

            if ( $k == $sumscount )
	            $chartdtspan.= " {$fmdate}";

            $categories[] = $fmdate;
            $seriesdata[] = round($sum['sum'], 2, PHP_ROUND_HALF_EVEN);

        }

        $series[] = array('name'=>$currency_code, 'data' =>$seriesdata, 'threshold'=>0, 'negativeColor'=>'#F2637B');

        return array('series'=>$series, 'chartdtspan'=>$chartdtspan, 'categories'=>$categories);
    }
	
	public static function highchart($renderTo, $type, $title="Chart", $categories=array(), $yAxisTitle=FALSE, $series=array(), $disableLegend=FALSE){
		
		if ( class_exists('Ghunti\HighchartsPHP\Highchart') ){
		
			$Chart = new HighchartsPHP\Highchart();

            $Chart->series = array();

			$Chart->chart->renderTo 	= $renderTo;
			$Chart->chart->type 		= $type;
			
			$Chart->title->text = $title ;
			
			if ( $yAxisTitle )
				$Chart->yAxis->title->text = $yAxisTitle;
			
			if ( $disableLegend )
				$Chart->legend->enabled = FALSE;
				
			
			$Chart->credits->enabled = FALSE;

            $Chart->plotOptions->series->shadow = TRUE;

			$Chart->xAxis->labels->style->font = "normal 14px Verdana, sans-serif";
			
			$Chart->xAxis->categories = $categories;
			
			if ( count($series) ) foreach ($series as $s) $Chart->series[] = $s;
			
			return $Chart;
			
		}
		else die('Class Highchart not loaded!');
		
	}
	
	public static function get_timezone_offset($timezone_from, $timezone_to){

		$defaultTz= new \DateTimeZone($timezone_from);
		$offsetTz = new \DateTimeZone($timezone_to);
		
		$serverTime = new \DateTime("now", $defaultTz);
		$offsetTime = new \DateTime("now", $offsetTz);
		
		$serverOffset 	= $serverTime->getOffset(); //relative offset to GMT
		$localOffset    = $offsetTime->getOffset(); //relative offset to GMT
		
		$offset = 0; $back = FALSE;
			
		if ( $localOffset > $serverOffset ) {
			$back	   = TRUE;
			$offset = $localOffset - $serverOffset;
		}
		elseif ( $localOffset < $serverOffset ){
			$offset = $serverOffset - $localOffset;
		}
		
		$offset = round( $offset/3600, 0, PHP_ROUND_HALF_UP );
		
		if ( $back ) $offset = 0 - $offset;
		
		return intval($offset);
		
	}
	
	public static function get_server_timezone_offset($timezone="Europe/Bucharest"){
		return self::get_timezone_offset(FN_SERVER_TIMEZONE, $timezone);
	}

    public static function transliterate($string, $replacement = '_') {

        $quotedReplacement = preg_quote($replacement, '/');

        $merge = array(
            '/[^\s\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/mu' => ' ',
            '/\\s+/' => $replacement,
            sprintf('/^[%s]+|[%s]+$/', $quotedReplacement, $quotedReplacement) => '',
        );

        $map = self::$_transliteration + $merge;

        return preg_replace( array_keys($map), array_values($map), $string );

    }

    public static function make_slug($string){
        return strtolower( self::transliterate($string, "-") );
    }

	/**
	 * @param int $length
	 *
	 * @return string
	 * @deprecated
	 */
    public static function get_rand_string($length=12){
	    //TODO better random string support
        return substr(md5( time() + rand(1, 9999) ), 0, $length);
    }

    public static function get_array_key_by_value_thresholds($input_array, $input_value){

        $pass = false; $i=0; $max = ( count($input_array) -1 ); $prevkey = false;  foreach($input_array as $key=>$value){

            if( $value == 0 ) continue;

            if(  ( $i <  $max  ) and ( $input_value <= $value ) ) {
                $pass = $key; break;
            }
            elseif( ( $i ==  $max  ) and empty($pass) ){
                if( $input_value > $value ) $pass = $key; else $pass = $prevkey;
            }

            $i++; $prevkey = $key;

        }

        return $pass;
    }

    /**
     * Sends a single email message
     */
    public static function send_email($subject, $message, $to, $from=FALSE, $reply_to=FALSE){

        $phpversion = phpversion();

        if( empty($from) ){
            $fn_domain = @parse_url( FN_URL ); $fn_domain = $fn_domain['host']; $from = "no-reply@{$fn_domain}";
        }

        $headers = "";

        if( is_array($from) ) {
	        $from_email = $from[1];
	        $headers   .= "From: {$from[0]}<{$from[1]}>\r\n";
        }else {
	        $from_email = $from;
	        $headers.= "From: {$from} \r\n";
        }


        if( $reply_to ){
            if( is_array($reply_to) )
                $headers.= "Reply-To: {$reply_to[0]}<{$reply_to[1]}>\r\n";
            else
                $headers.= "Reply-To: {$reply_to} \r\n";
        }

        $headers.= "Content-type: text/html\r\n";
        $headers.= "X-Mailer: PHP {$phpversion}\r\n";

        if( @mail($to, $subject, $message, $headers, $from_email) )
	        return true;

        return false;

    }


	/**
	 * @param bool $force
	 *
	 * @return bool
	 * @deprecated
	 */
    public static function is_https($force=FALSE){

        if( $force )
            return true;
        else
            if( isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] and ( $_SERVER['HTTPS'] != 'off' ) ) return TRUE;

        return FALSE;

    }

    public static function get_base_url($static=false, $force_https=false, $path_hint=''){

        if( $static and ( strlen($static) > 10 ) ) return $static;

        if( defined('FN_URL') ) return FN_URL;

        $url = isset( $_SERVER['HTTP_HOST'] ) ? ( strpos($_SERVER['HTTP_HOST'], $_SERVER['SERVER_NAME']) === false ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST']  ) : $_SERVER['SERVER_NAME'];

        $url = self::is_https($force_https) ? ( 'https://' . $url ) : ( 'http://' . $url );

        if( ( $_SERVER['SERVER_PORT'] != '80' ) and ( $_SERVER['SERVER_PORT'] != '443' ) ) //not standard http(s) ports
            $url.= ( ':' . $_SERVER['SERVER_PORT'] );

        $docroot = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR);

        if( strpos(FNPATH, $docroot) === false ){
            $url.= str_replace('?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
        }else
            $url  = str_replace($docroot, $url, FNPATH);

        if( $path_hint and strpos($url, $path_hint) )
            $url = substr($url, 0, strrpos($url, $path_hint));

        if( !self::is_https() and $force_https and !headers_sent() ) @header("Location: " . $url); //redirect to the https url right away

        return $url;

    }

    public static function get_server_base_url(){

        $url = self::get_base_url();

        while(strrpos($url, "/") > 7){
            $url = rtrim($url, "/"); $url=substr($url, 0, strrpos($url, "/"));
        }

        return $url;
    }

	public static function get_hostname($url=null){

		if( empty($url) )
			$url = FN_URL;

		return @parse_url($url, PHP_URL_HOST);

	}

    public static function get_file_path($url){
        if( strpos(self::get_base_url(), $url) === FALSE )
            return ( rtrim(FNPATH, "/") . "/" . ltrim($url, "/") );
        else
            return str_replace(self::get_base_url(), FN_URL, $url);
    }

    public static function format_nr($number, $decimals=2, $precision=FALSE){ //TODO add international support

	    $rmode     = PHP_ROUND_HALF_EVEN;
	    $precision = $precision > 0 ? $precision : $decimals;

	    $number = round(floatval($number), $precision, $rmode);

	    return number_format($number, $decimals, ",", ".");

    }

    /**
     * Formats a file size to a human-readable format
     * @param number $size in bytes
     * @return string
     */
    public static function fmt_filesize( $size ){

        if ($size >= 1073741824)
	        return ( round(($size / 1073741824), 2) . " GB" );

        if ($size >= 1048576)
	        return ( round(($size / 1048576), 2) . " MB" );

        if ($size >= 1024)
	        return ( round(($size / 1024), 2) . " KB" );

        return ($size . ' bytes');
    }

    /**
     * Finds the value of a PHP configuration directive
     * @param $var name of the directive
     * @param bool $parseint (optional), set it to 1 or true in case that directive represents a file size in K (KB), M (MB) or G (GB) and you want the value in bytes
     * @return bool|int|string
     */
    public static function env_get_cfg($var, $parseint=FALSE){

        $value = @ini_get($var);

        if($value){

            if ($parseint){
                $last 	= substr($value, -1, 1);

                if ( in_array($last, array('K', 'M', 'G')) ){

                    $value = intval( substr($value, 0, strlen($value)-1) );

                    if ( $last == 'K' ) return ($value * 1024);
                    if ( $last == 'M' ) return ($value * 1024 * 1024);
                    if ( $last == 'G' ) return ($value * 1024 * 1024 * 1024);
                }

            }

            return $value;

        }

        return FALSE;

    }

    public static function get_max_upload_filesize(){

        $upload_max_size= self::env_get_cfg('upload_max_filesize', TRUE);
        $post_max_size  = self::env_get_cfg('post_max_size', TRUE);

        return min($upload_max_size, $post_max_size);

    }

    public static function get_file_extension($filename){
        return strtolower( substr($filename, strrpos($filename, ".")+1) );
    }

	/**
	 * Uploads a file to the server
	 * @param $file
	 * @param $folder
	 * @param null $filename
	 * @param string $prefix
	 * @param string $suffix
	 * @param array $allowExtensions
	 *
	 * @return array|bool
	 * @deprecated
	 */
    public static function file_upload($file, $folder, $filename=NULL, $prefix="", $suffix="", $allowExtensions=array()){

        if( is_array($file) and empty($file['error']) ){

            $ofilename = $file['name']; $allowedExts = array_merge(self::$allowedExtensions, $allowExtensions);

            $filename  = isset($filename) ? $filename : basename($ofilename);
            $extension = self::get_file_extension($ofilename);

            $filename = str_replace($extension, "", $filename);
            $filename = str_replace(strtoupper($extension), "", $filename);
            $filename = trim($filename, ".");

            if( !in_array($extension, $allowedExts) ){
                $allowed = self::$allowedExtensions; $last = array_pop($allowed); $allowed = @implode(", ", $allowed); $allowed.= " sau {$last}";
                $Error =  "Fisierul {$file['name']} nu este acceptat din cauza restrictiilor privind extensia fisierelor. Sunt acceptate doar fisiere {$allowed} . ";

                return array('success'=>0, 'msg'=>$Error);
            }

            //set filename
            $upload_filename = ( $prefix . $filename . $suffix . '.' . $extension );

            //upload path
            $uploaded = ( rtrim($folder, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR .  $upload_filename );

            if( @move_uploaded_file($file['tmp_name'], $uploaded ) )
                return array('success'=>1, 'msg'=>$uploaded, 'filename'=>$upload_filename, 'path'=>$uploaded);
            else
                array('success'=>0, 'msg'=>"Directorul {$folder} nu este accesibil pentru salvarea fisierului incarcat. Verifica permisiunile asupra directorului si incearca din nou.");


        }
        else if($file['error']){

            if( $file['error'] == UPLOAD_ERR_INI_SIZE ){
                $sysmaxsize  = self::fmt_filesize(  self::env_get_cfg('upload_max_filesize', TRUE) );
                $Error          = "Fisierul {$file['name']} este prea mare. Sunt acceptate doar fisiere mai mici de {$sysmaxsize}";
            }

            if( $file['error'] == UPLOAD_ERR_CANT_WRITE )
                $Error = "Fatal error: can't write to the temporary folder, set for file uploads. Please review your server's php.ini settings and try again.";

            if( $file['error'] == UPLOAD_ERR_EXTENSION )
                $Error = "The {$file['name']} cound not be uploaded due to file extension restrictions.";

            if( empty($Error) )
                $Error = "Oups! Unknown error. Maybe " . urldecode($_GET['upload']) . " is not writable by the current php process.";

            return array('success'=>0, 'msg'=>$Error);

        }

        return FALSE;

    }


	/**
	 * Expands zip archive
	 * @param $path
	 * @param bool $dir
	 * @param array $entries
	 *
	 * @return bool|string
	 */
    public static function unzip($path, $dir=FALSE, $entries=array()){

        if( empty($dir) )
	        $dir = dirname($path);

        if( !class_exists('ZipArchive') )
	        return false;

        $zip = new ZipArchive();

        if ( true === $zip->open($path) ) {

            if( count($entries) )
                $zip->extractTo($dir, $entries);
            else
                $zip->extractTo($dir);

            $zip->close();

            return $dir;
        }

        return false;

    }

    /**
     * Recursively removes a directory
     * @param $path
     * @return bool
     */
    public static function remove_dir($path){

        if( !is_dir($path) ) return FALSE;

        $it     = new RecursiveDirectoryIterator($path);
        $files = new RecursiveIteratorIterator($it,  RecursiveIteratorIterator::CHILD_FIRST);

        foreach($files as $file) {

            if ( ( $file->getFilename() === '.' ) or ( $file->getFilename() === '..' ) )
                continue;

            if ($file->isDir())
                @rmdir($file->getRealPath());
            else
                @unlink($file->getRealPath());

        }

        return @rmdir($path);
    }

    /**
     * Recursively copies directory or file
     * @param $spath
     * @param $dstpath
     * @param bool $clean_dst
     * @param int $rlevel internal variable used to determine directory level
     */
    public static function fcopy($spath, $dstpath, $clean_dst=FALSE, $rlevel=0) {

        $spath   = rtrim($spath, DIRECTORY_SEPARATOR);
        $dstpath = rtrim($dstpath, DIRECTORY_SEPARATOR);

        if( $clean_dst and file_exists ( $dstpath ) ){
            if( is_file($dstpath) )
	            @unlink($dstpath); else self::remove_dir($dstpath);
        }

        if ( is_dir ( $spath ) ) {

            if( $clean_dst or $rlevel)
	            @mkdir($dstpath);

            $files = scandir ( $spath );

            if( count($files) ) foreach ( $files as $file )
                if ($file != "." && $file != "..")
                    self::fcopy( $spath . DIRECTORY_SEPARATOR . $file, $dstpath . DIRECTORY_SEPARATOR . $file, FALSE, ++$rlevel );

        } else if ( file_exists ( $spath ) )
            @copy ( $spath, $dstpath );
    }

    public static function get_cache_folder_path(){
        if( defined('FN_CACHE_FOLDER') )
	        return rtrim( (strpos(FN_CACHE_FOLDER, DIRECTORY_SEPARATOR) === false ) ? ( FNPATH . DIRECTORY_SEPARATOR . FN_CACHE_FOLDER ) : (  strpos(FN_CACHE_FOLDER, DIRECTORY_SEPARATOR) == 0 ) ? FN_CACHE_FOLDER : ( FNPATH . DIRECTORY_SEPARATOR . FN_CACHE_FOLDER ), DIRECTORY_SEPARATOR);
    }

    public static function get_cache_file_path($filename){
        return (self::get_cache_folder_path() . DIRECTORY_SEPARATOR . trim($filename, DIRECTORY_SEPARATOR) );
    }

    public static function get_cache_path($relative_path=null){
        return $relative_path ? self::get_cache_folder_path() : self::get_cache_file_path($relative_path);
    }

	/**
	 * @param $value
	 *
	 * @return mixed
	 * @deprecated
	 */
    public static function escape_xml($value){

        $value = str_replace('&', '&amp;', $value);
        $value = str_replace('<', '&lt;', $value);
        $value = str_replace('>', '&gt;', $value);
        $value = str_replace('\'', '&apos;', $value);
        $value = str_replace('"', '&quot;', $value);

        return $value;
    }

	/**
	 * @param $value
	 *
	 * @return mixed
	 * @deprecated
	 */
    public static function unescape_xml($value){

        $value = str_replace('&amp;', '&', $value);
        $value = str_replace('&lt;', '<', $value);
        $value = str_replace('&gt;', '>', $value);
        $value = str_replace('&apos;', '\'', $value);
        $value = str_replace('&quot;', '"', $value);

        return $value;

    }

    /**
     * Checks if $string starts with $needle
     * @param $needle
     * @param $string
     * @return bool
     */
    public static function str_startswith($needle, $string, $ignorecase=false){

        if( $ignorecase ) {

            $string  = strtolower($string);
            $needle= strtolower($needle);

        }

        return (strpos($string, $needle) === FALSE ? FALSE : (strpos($string, $needle) == 0));

    }

    /**
     * Returns the full config file path
     * @param string $default
     * @return string
     */
    public static function cfg_file_path($default='/config/config.php'){

        $env = FN_ENVIRONMENT;

	    if( file_exists( FNPATH . "/config/config-{$env}.php") )
		    return ( FNPATH . "/config-{$env}.php" );

	    return ( FNPATH . $default );

    }

    public static function is_development_environment(){
        return FN_ENVIRONMENT == 'development';
    }

    public static function is_unittest_environment(){
        return FN_ENVIRONMENT == 'unittest';
    }

    public static function is_staging_environment(){
        return ( FN_ENVIRONMENT == 'staging' ) or ( FN_ENVIRONMENT == 'test' );
    }

	public static function is_demo_environment(){
		return ( FN_ENVIRONMENT == 'demo' );
	}

    public static function is_production_environment(){
        return FN_ENVIRONMENT == 'production';
    }
	
}