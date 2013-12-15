<?php
/**
 * Collection of validation functions
 * @author Adrian7
 * @since Trackify 0.6
 */

class fn_CheckValidityOf{
	
	/**
	 * validates an e-mail address;
	 *
	 * @param string $email
	 * @return bool
	 */
	public static function email( $email ){
			
		$to_work_out = @explode("@", $email);
		if ( !isset($to_work_out[0]) ) return FALSE;
		if ( !isset($to_work_out[1]) ) return FALSE;
			
		$pattern_local  	= '/^([0-9a-z]*([-|_]?[0-9a-z]+)*)(([-|_]?)\.([-|_]?)[0-9a-z]*([-|_]?[0-9a-z]+)+)*([-|_]?)$/i';
		$pattern_domain = '/^([0-9a-z]+([-]?[0-9a-z]+)*)(([-]?)\.([-]?)[0-9a-z]*([-]?[0-9a-z]+)+)*\.[a-z]{2,4}$/i';
		$match_local    	= preg_match($pattern_local, $to_work_out[0]);
		$match_domain   = preg_match($pattern_domain, $to_work_out[1]);
			
		if (strlen($email) > 4) {
			if ($match_local && $match_domain) {
				return TRUE;
			}
			else {
				return FALSE;
			}
		}
			
		return TRUE;
	}
	
	public static function username( $username ){
	
		if(strlen($username) < 4) 				 return FALSE;
		if(substr_count($username, " ") > 0) return FALSE;
	
		$found = preg_match('/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/', $username, $matches);
	
		return intval($found);
	}
	
	public static function image_extension($filename){
		return Image::is_valid_file_extension($filename);
	}
	
	public static function stringlen($string, $minlength=2, $lettersonly=FALSE, $trimspaces=TRUE){
		if ( $trimspaces ) $string = trim($string);
	
		if ($lettersonly){
			$numbers 	= array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
			$string  		= str_replace($numbers, "", $string);
		}
	
		return strlen($string) >= $minlength;
	}
	
	public static function phone_nr($phone, $length=10){
	
		$allowedchars = array('-', " ");
		$phone = preg_replace('/[^0-9]/', "", $phone);
	
		return strlen($phone) == $length;
	}
	
	public static function hostname($host){
		
		$result = $host;
		
		$check_ip = str_replace(".", "", $host);
		$check_ip = preg_replace('/[0-9]/', "", $check_ip);
		
		if ( empty($check_ip) ) //it is an ip address
			$result = @gethostbyaddr($host);
		else 
			$result = @gethostbyname($host);
		
		if ( $result === FALSE ) return FALSE; //malformed input
		
		return $result != $host;
		
	}
}

?>