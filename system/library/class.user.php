<?php

namespace FinFlow;

class User{
	
	protected $authenticated;
	protected $level;
	protected $username;
	protected $email;
	protected $avatar;
	
	public static $table = 'fn_users';
	
	public function __construct($email=FALSE, $password=FALSE){
		$this->authenticated = FALSE;
		$this->role				= 0;
		$this->username		= NULL;
		$this->avatar			= 'anonymous.jpg';
		
		if($email and $password)
			self::authenticate($email, $password);

	}


    public static function get_all($offset=0, $limit=25){

        global $fndb, $fnsql;

        $offset = intval($offset);
        $limit   = intval($limit);

        $fnsql->select('*', self::$table);
        $fnsql->limit($offset, $limit);

        return $fndb->get_rows( $fnsql->get_query() );

    }

    public static function get_total(){

        global $fndb, $fnsql;

        $fnsql->init(SQLStatement::$SELECT);
        $fnsql->table(self::$table);
        $fnsql->count('user_id', 'total');
        $fnsql->from(self::$table);

        $Row = $fndb->get_row( $fnsql->get_query() );

        if( isset($Row->total) ) return $Row->total;

        return 0;

    }

    public static function get($user_id, $fields='*'){

        global $fndb, $fnsql;

        $user_id  = intval($user_id);
        $fields    = $fndb->escape( $fields );

        if( $user_id ) {
            $fnsql->select($fields, self::$table, array('user_id'=>$user_id)); return $fndb->get_row( $fnsql->get_query() );

        }

        return null;

    }

    public static function get_by($value, $by='email'){ //email, pw_reset_key, id

        global $fndb, $fnsql;

        if( empty($value) ) return false;

        if( $by == 'email' ){
            $value = $fndb->escape($value); $fnsql->select('*', self::$table, array('email'=>$value)); return $fndb->get_row( $fnsql->get_query() );
        }

        if( $by == 'pw_reset_key' ){
            $value = $fndb->escape($value); $fnsql->select('*', self::$table, array('pw_reset_key'=>$value)); return $fndb->get_row( $fnsql->get_query() );
        }

        if( $by == 'id' ){
            return self::get($value);
        }

        return null;

    }

    public static function send_reset_pw_link($user_id){

        $random = strtoupper( Util::random_string(12) );
        $user   = self::get($user_id, 'email,user_id');

        if( $user and isset($user->user_id) ){

	        //TODO move this to EMail::class

            //--- update password reset key in database ---//
            $updated = self::update($user_id, array('pw_reset_key'=>$random));
            //--- update password reset key in database ---//

            if( $updated ){

                $url     = UI::url('/recover', array('key'=>$random), false);
                $baseURL = FN_URL;

                $Subject = "FinFlow - Resetare parola";
                $Content = "Salut, {$user->email} <br/><br/>";
                $Content.= "<p>Pentru a continua procesul de recuperare a parolei, d&#259; click pe urm&#259;torul link:<br/>";
                $Content.= ('<a href="' . $url . '" target="_blank">' . $url . '</a></p>');

                $Content.="<br/><br/><small>Dac&#259; nu ai solicitat recuperarea parolei pentru <em>{$baseURL}</em> atunci ignor&#259; acest mesaj.</small>";

                return Util::send_email($Subject, $Content, $user->email);

            }

        }

        return false;

    }

	/**
	 * Hashes passwords, or very very important secrets
	 * @param $password
	 * @param int $algo
	 *
	 * @return bool|string
	 */
	public static function hash_password($password, $algo=PASSWORD_DEFAULT){
		return password_hash($password, $algo);
	}
	
	
	public static function is_authenticated(){
		return (isset($_SESSION['fn_user_id']) and isset($_SESSION['fn_authentication']) and ( $_SESSION['fn_user_id'] > 0 ) );
	}
	
	public static function authenticate($email, $password){
		
		global $fndb, $fnsql;
		
		$email = $fndb->escape($email);
		
		$fnsql->select('*', self::$table, array('email'=>$email));
		
		$user = $fndb->get_row( $fnsql->get_query() );

		if ( count($user) and isset($user->user_id) and password_verify($password, $user->password) ){
			//TODO use session class
			$_SESSION['fn_user_id'] 	    = $user->user_id;
			$_SESSION['fn_authentication']	= TRUE;

            $fnsql->update(self::$table, array('last_login'=>date(FN_MYSQL_DATE)), array('user_id'=>$user->user_id));
            $fndb->execute_query( $fnsql->get_query() );
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	public static function logout(){
		
		$_SESSION['fn_user_id'] = 0;
		
		unset($_SESSION['fn_user_id']);
		unset($_SESSION['fn_authentication']);
		
	}

    public static function current_user_id(){
        if( isset($_SESSION['fn_user_id']) ) return intval($_SESSION['fn_user_id']); return 0;
    }


    public static function add($data){

        global $fndb, $fnsql;

        if( $data['password'] )
            $data['password'] = self::hash_password($data['password']);

        $data = $fndb->escape($data);

        $fnsql->insert(self::$table, $data);

        return $fndb->execute_query( $fnsql->get_query() );

    }


    public static function update($user_id, $data){

        global $fndb, $fnsql;

        if( isset( $data['password'] ) )
            $data['password'] = self::hash_password($data['password']);

        $user_id = intval($user_id);
        $data    = $fndb->escape($data);

        if( $user_id ){
            $fnsql->update(self::$table, $data, array('user_id'=>$user_id)); return ( $fndb->execute_query( $fnsql->get_query() ) === false ) ? false : true;
        }

        return false;

    }

    public static function delete($user_id){

        global $fndb, $fnsql;

        $count = self::get_total();

        if( $count > 1 ){ //there's still one user left
            $fnsql->delete(self::$table, array('user_id'=>$user_id)); return $fndb->execute_query( $fnsql->get_query() );
        }

        return false;

    }


}