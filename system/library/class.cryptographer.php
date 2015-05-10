<?php

namespace FinFlow;

class Cryptographer{

	const WARN_IF_MCRYPT_MISSING = true;

    const CYPHER  = MCRYPT_BLOWFISH;
    const MODE    = MCRYPT_MODE_CBC;

    const _SALT = 'VeryWeakSalt';

	public static function encrypt($plaintext, $salt=null, $base64=true){

		if( empty($salt) )
			trigger_error(__CLASS__ . '::' . __FUNCTION__ . ' requires the random-generated salt to generate strong encryption. Your input was encrypted using a weak encryption technique.', E_USER_WARNING);

		if( function_exists('mcrypt_module_open') ){
			return self::mcrypt_encrypt($plaintext, $salt, $base64);
		}


		if( self::WARN_IF_MCRYPT_MISSING ){
			trigger_error(__CLASS__ . '::' . __FUNCTION__ . ' requires the Mcrypt extension to generate strong encryption. Your input was encrypted using a weak encryption technique.', E_USER_WARNING);
		}

		return self::compat_encrypt($plaintext, $salt, $base64);

	}

	public static function decrypt($crypttext, $salt=null, $base64=true){

		if( function_exists('mcrypt_module_open') ){
			return self::mcrypt_decrypt($crypttext, $salt, $base64);
		}

		return self::compat_decrypt($crypttext, $salt, $base64);

	}

	public static function getSupportedCiphers(){

		if( function_exists('mcrypt_list_algorithms') )
			return mcrypt_list_algorithms("/usr/local/lib/libmcrypt");

		return 'vernam';

	}

	public static function compat_encrypt($plaintext, $salt=null, $base64=true){

		$salt      = empty($salt) ? self::_SALT : strval($salt);
		$crypttext = '';

		if(strlen($plaintext))

		// Iterate through each character
		for($i=0; $i<strlen($plaintext);) {
			for($j=0; $j<strlen($salt); $j++, $i++) {

				$l = empty($plaintext{$i}) ? '' : $plaintext{$i};
				$r = empty($salt{$j}) ? '' : $salt{$j};

				$crypttext .= ( $l ^ $r );
			}
		}

		return $base64 ? base64_encode( $crypttext ) : $crypttext;

	}

	public static function compat_decrypt($crypttext, $salt=null, $base64=true){

		$salt      = empty($salt) ? self::_SALT : strval($salt);
		$crypttext = $base64 ? base64_decode($crypttext) : $crypttext;
		$plaintext = '';

		// Iterate through each character
		for($i=0; $i<strlen($crypttext);) {
			for($j=0; $j<strlen($salt); $j++, $i++) {

				$l = empty($crypttext{$i}) ? '' : $crypttext{$i};
				$r = empty($salt{$j}) ? '' : $salt{$j};

				$plaintext .= ( $l ^ $r );
			}
		}

		return $plaintext;

	}

    public static function mcrypt_encrypt($plaintext, $salt=null, $base64=true){

	    $salt = empty($salt) ? self::_SALT : strval($salt);

        $td = mcrypt_module_open(self::CYPHER, '', self::MODE, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

        mcrypt_generic_init($td, $salt, $iv);

        $crypttext = mcrypt_generic($td, $plaintext);

        mcrypt_generic_deinit($td);

        return $base64 ? base64_encode( $iv . $crypttext ) : ( $iv . $crypttext );

    }

    public static function mcrypt_decrypt($crypttext, $salt=null, $base64=true){

	    $salt = empty($salt) ? self::_SALT : strval($salt);

        $crypttext = $base64 ? base64_decode($crypttext) : $crypttext;
        $plaintext = '';
        $td        = mcrypt_module_open(self::CYPHER, '', self::MODE, '');
        $ivsize    = mcrypt_enc_get_iv_size($td);
        $iv        = substr($crypttext, 0, $ivsize);
        $crypttext = substr($crypttext, $ivsize);

        if ($iv){
            mcrypt_generic_init($td, $salt, $iv); $plaintext = mdecrypt_generic($td, $crypttext);
        }
        return trim($plaintext);
    }
}