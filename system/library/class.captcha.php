<?php

namespace FinFlow;

use FinFlow\Session;

class Captcha{

	/**
	 * Relative path to TTF fonts dir (relative to FNPATH )
	 * @var string
	 */
    public static $fonts_dir = '/assets/ttf';

    public static function init($length=6, $math_max=100){
        self::set($length, $math_max);
    }

    public static function is_initialized(){
        return isset($_SESSION['capchakey']);
    }

	public static function getFontsDir($path=null){
		return $path ? ( FNPATH . $path ) : ( FNPATH . self::$fonts_dir );
	}

    public static function supports_img(){
        if ( @function_exists('gd_info') ) {
            $gdinfo = gd_info(); if( $gdinfo['FreeType Support'] and $gdinfo['PNG Support'] ) return true;
        }

        return false;
    }

	/**
	 * Generates a random string; maximum length 32
	 * @param int $length
	 *
	 * @return string
	 */
	public static function randomstr( $length=6 ){
		if ( $length > 32 ) $length = 32; return strtoupper( Util::random_string($length) );
	}

    public static function radom_math_question($max=100){

        $randA = rand(0, $max/2);
        $randB = rand($max/2, $max);

        $_SESSION['capcha_math'] = "$randA + $randB = ";

        return ( $randA + $randB );

    }
	
	public static function set( $length=7, $math_max=100 ){

		@session_start();

        if ( empty($_SESSION['capchakey']) ) {
            if( self::supports_img() )
                $_SESSION['capchakey'] = self::randomstr($length);
            else
                $_SESSION['capchakey'] = self::radom_math_question($math_max);
        }
	}
	
	public static function get(){
		//TODO drop $_SESSION and use Session Class
		if ( self::is_initialized() ) return $_SESSION['capchakey'];

        trigger_error(__CLASS__ . '::' . __FUNCTION__ ." Error: The captcha verification string hasn't been initialized.", E_USER_WARNING);

        return NULL;
	}
	
	public static function validate( $inputStr ){

		if ( isset($_SESSION['capchakey']) and (strtoupper($_SESSION['capchakey']) == strtoupper($inputStr)) ){
			unset($_SESSION['capchakey']); return TRUE;
		}
		
		return FALSE;
	}
	
	public static function choose_font( $dir ){
	
		if ( !is_dir($dir) ) return 1;
	
		$fonts = array();
		$files = @scandir($dir);
	
	
		if( count($files)) foreach($files as $f){
	
			if( ($f == ".") or ( $f == "..") or strpos($f, ".php") ) continue;
	
			$fonts[] = $f;
	
		}
	
		$max = count($fonts)-1;
		$j		= rand(0, $max);
	
		return ( rtrim($dir, "/") . "/" . $fonts[$j] );
	
	}

    public static function output_math($before='<em>', $after='</em>'){
        $math = isset($_SESSION['capcha_math']) ? $_SESSION['capcha_math'] : '---'; echo ( $before . $math . $after );
    }
	
	public static function output_img($width=120, $height=60, $textcolor=array('r'=>0, 'g'=>0, 'b'=>0), $fontsize=14){
		
		$randomStr =  self::get();
		$fontsDir  = self::getFontsDir();
	
		if ( !is_dir($fontsDir) ){ trigger_error(__CLASS__ . '::' . __FUNCTION__ ." Error: Fonts dir is missing.", E_USER_WARNING); return null; }
	
		if ( !extension_loaded('gd') and !function_exists('gd_info') ){
            trigger_error(__CLASS__ . '::' . __FUNCTION__ ." Error: GD Extension is required for generating the image captcha.", E_USER_WARNING); return null;
        }
	
		if ( !function_exists('imagettftext') ) {
            trigger_error(__CLASS__ . '::' . __FUNCTION__ ." Error: FreeType GD support is required for generating the image captcha.", E_USER_WARNING); return null;
        }
	
		$font = self::choose_font($fontsDir);
		$size = $fontsize > 10 ? intval($fontsize) : 10;
	
		$source 	= @imagecreatetruecolor($width, $height);
	
		@imagesavealpha($source, true);
	
		$bg 	    = @imagecolorallocatealpha($source, 0, 0, 0, 127);
		$font_color = imagecolorallocate($source, $textcolor['r'], $textcolor['g'], $textcolor['b']);
	
		@imagefill($source, 0, 0, $bg);

		//draw lines
		$black = imagecolorallocate($source, 0, 0, 0);

		imageline($source, 0, 0, $width, $height, $black);
		imageline($source, $height/2, $width/2, $width/3, $height/3, $black);
		imageline($source, $height, $width, $width, 0, $black);
		imageline($source, 0, $height, $width, 0, $black);
	
		//calculate font middle on y
		$pos_y = ($height/2 + $size/2);
	
		//calculate best fit on x
		$pos_x = ($width - $size * strlen($randomStr))/2;
	
		imagettftext($source, $size, 0, $pos_x, $pos_y, $font_color, $font, $randomStr);

		header('Content-Type: image/png');
	
		//Output the image
		@imagepng($source);
	
		@imagedestroy($source);

	}
	
}