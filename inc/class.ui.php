<?php

class fn_UI{
	
	public static $MSG_ERROR = 'error';
	public static $MSG_NOTE  = 'note';
	public static $MSG_WARN = 'warning';

    public static $pageNames = array(
        'index'             => 'Panou Principal',
        'dashboard'      =>  'Panou Principal',
        'transactions'    => 'Tranzac&#355;ii',
        'performance'   => 'Performan&#355;&#259;',
        'labels'             => 'Etichete',
        'accounts'         => 'Conturi',
        'currencies'       => 'Monede',
        'tools'              => 'Unelte',
        'settings'          => 'Set&#259;ri',
        'login'             => 'Autentificare',
        'pwreset'       => 'Recuperare parol&#259;'
    );
	
	public static function msg($msg, $type="note"){
		if ( strlen($msg) ) echo '<p class="msg ' . $type . '"> ' . $msg . ' </p>';
	}
	
	public static function error_page_css(){ ?>
		<style>
		<!--
		/*Import google web font*/
		@import url(http://fonts.googleapis.com/css?family=Ubuntu:400,700&subset=latin,latin-ext);
		
		body{
			margin: 0px;
			padding: 0px;
			color: #000;
			font-family: 'Ubuntu', Verdana, Arial, Helvetica, sans-serif;
			font-size: 1.4em;
			background: #F7F7F7;
		}
		
		body.error{
			padding: 20px;
		}
		
		.clear{
			clear: both;
		}
		
		.wrap{
			width:90%;
			margin:1% auto;
			padding:1em;
			height:auto;
			background:#FFF;
		}
		
		p.msg{
			border: 1px solid;
		    margin: 10px 0px;
		    padding:10px;
			border-radius: 4px;
			color: #333;
			width: 98%;
			background-color: #BDE5F8;
			box-shadow: 2px 3px 6px #999;
		}
		
		p.msg.error{
			background-color: #FFBABA;
		}
		
		p.msg.warning, p.msg.warn{
			background-color: #FEEFB3;
		}
		
		p.msg.note, p.msg.info{
			background-color: #BDE5F8;
		}
		
		p.msg a{
			text-decoration:underline;
		}
		-->
		</style>
		<?php 
	}

	public static function fatal_error($msg, $die=TRUE, $html=TRUE, $msgtype="error", $prefix="Eroare: ", $title="Eroare"){
		
		if ( $html ): ?>
			<html>
			<head>
			<meta charset="utf-8" />
			<title>FinFlow | <?php echo $title; ?> </title>
			<?php fn_UI::error_page_css(); ?>
			</head>
			<body class="error">
				<div class="wrap">
					<p class="msg <?php echo $msgtype; ?>"><strong><?php echo $prefix; ?> </strong><?php echo $msg; ?></p>
				</div>
			</body>
			</html>
		<?php 
			else: 
				echo "{$prefix} {$msg} \n";
			endif;
			
		if ( $die ) die();
		
	}

    public static function loading_screen($msg, $die=TRUE){
        ?>
        <html>
        <head>
            <meta charset="utf-8" />
            <title>FinFlow | loading </title>
            <?php fn_UI::error_page_css(); ?>
        </head>
        <body class="error">
        <div class="wrap">
            <p style="text-align: center"><img src="<?php echo FN_URL; ?>/images/preloader.gif" align="absmiddle"/></p>
            <p class="msg" style="text-align: center;"></strong><?php echo $msg; ?></p>
        </div>
        </body>
        </html>
        <?php

        if( $die ) die();
    }
	
	public static function esc_html( $input ){
		return htmlentities( stripslashes($input) , ENT_NOQUOTES, 'UTF-8' );
	}
	
	public static function esc_attr( $input ){
		return htmlspecialchars( stripslashes($input) , ENT_NOQUOTES, 'UTF-8' );
	}
	
	public static function extract_post_val($key, $default="", $escape=FALSE){
		$val = isset( $_POST[$key] ) ? $_POST[$key] : $default; return $escape ? self::esc_html($val) : $val;
	}
	
	public static function extract_get_val($key, $default="", $escape=FALSE){
		$val = isset( $_GET[$key] ) ? urldecode($_GET[$key]) : $default; return $escape ? self::esc_html($val) : $val;
	}
	
	public static function checked_or_not($inputval, $checkval=""){
		if ( $inputval == $checkval ) return 'checked';
		
		return "";
	}
	
	public static function selected_or_not($inputval, $checkval=""){
		
		if ( is_array($checkval) and in_array($inputval, $checkval)) return 'selected';
		
		if ( $inputval == $checkval ) return 'selected'; return "";
	}

    public static function form_hidden_fields($data, $ignore=array()){
        if( count($data) ) foreach($data as $key=>$value) : if( in_array($key, $ignore, true) ) continue; ?>
            <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>"/>
       <?php endforeach;
    }
	
	public static function show_errors($errors){

		if ( is_array($errors) ) 
			foreach ($errors as $msg) self::msg($msg, 'error');
		else 
			self::msg($errors, 'error');
	}
	
	public static function show_notes($notes){
		if ( is_array($notes) )
			foreach ($notes as $msg) self::msg($msg, 'note');
		else
			self::msg($notes, 'note');
	}

    public static function show_warnings($warnings){
        if ( is_array($warnings) )
            foreach ($warnings as $msg) self::msg($msg, 'warning');
        else
            self::msg($warnings, 'note');
    }
	
	
	public static function get_translated_strings($what='months'){
			$months = array(
				'Ianuarie'		=>array('january', 'jan'),
				'Februarie'	   =>array('february', 'feb'),
				'Martie'		  =>array('march', 'mar'),
				'Aprilie'		 =>array('april', 'apr'),
				'Mai'			 =>array('may'),
				'Iunie'	     =>array('june', 'jun'),
				'Iulie'			    =>array('july', 'jul'),
				'August'		  =>array('august', 'aug'),
				'Septembrie'=>array('september', 'sep'),
				'Octombrie'	=>array('october', 'oct'),
				'Noiembrie'	=>array('november', 'nov'),
				'Decembrie'	=>array('december', 'dec')
		);
		
		$days	= array(
				'Luni'							=>array('monday', 'mon'),
				'Mar&#355;i'				=>array('tuesday', 'tue'),
				'Miercuri'					=>array('wednesday', 'wed'),
				'Joi'							=>array('thursday', 'thu'),
				'Vineri'						=>array('friday', 'fri'),
				'Samb&#259;t&#259;'	=>array('saturday', 'sat'),
				'Duminic&#259;'			=>array('sunday', 'sun')
		);

		if($what == 'months') 
			return $months;
		
		if($what == 'days')
			return $days;

		return array();
		
	}
	
	public static function translate_date( $date ){

			$months = self::get_translated_strings('months');
			$days		= self::get_translated_strings('days');
			
			$date = strtolower($date);			

			if (count($months)) foreach ($months as $translated=>$m){
				$date = str_replace($m[0], $translated, $date);
				$date = str_replace($m[1], $translated, $date);
			}
			
			if (count($days)) foreach ($days as $translated=>$d){
				$date = str_replace($d[0], $translated, $date);
				$date = str_replace($d[1], $translated, $date);
			}
			
			$date = str_replace('of', "", $date);
			$date = str_replace('th', "", $date);
			$date = str_replace('st', "", $date);
			$date = str_replace('nd', "", $date);
			$date = str_replace('rd', "", $date);

            $date = str_replace(array('Augu', 'augu'), array('August', 'august'), $date);

			$date = str_replace(' am', " AM", $date);
			$date = str_replace(' pm', " PM", $date);
			
			return $date;
			
	}

    public static function format_nr($number, $decimals=2, $precision=FALSE){
        return fn_Util::format_nr($number, $decimals, $precision);
    }

    public static function format_money($value, $currency=false, $show_cc=false, $currency_after=false){

        $value = self::format_nr($value);

        $currency = empty($currency) ? 0 : $currency;

        if( empty($currency) ){
            $currency = fn_Currency::get_default();
        }else{

            if( is_string($currency) )
                $currency = fn_Currency::get_by_code($currency);

            if( is_int( $currency ) )
                $currency = fn_Currency::get($currency);

        }

        if( is_object($currency) ){
            $s_currency = $show_cc ?  $currency->ccode : $currency->csymbol; return $currency_after ? "{$value} {$s_currency}" : "{$s_currency} {$value}";
        }

        return $value;
    }



	public static function page_url($page='index', $vars=array(), $echo=TRUE){

		if( strlen($page) )
			$vars = array_merge(array('p'=>strtolower($page)), $vars);
		
		$url = ( trim(FN_URL, "/") . '/?' . http_build_query($vars) );

		if( $echo )	
			echo $url;
		else 			
			return $url;
	}

    public static function asset_url($asset, $version=null, $echo=true){
        $version = empty($version) ? FN_VERSION : $version; $url = ( self::base_url($asset) . '?v' . $version ); if( $echo ) echo $url; else return $url;
    }

    public static function page_title($page, $echo=true){

        $page = urldecode($page); $name = "Eroare - Pagina nu poate fi gasit&#259;";

        if( empty($page) )
            $name = self::$pageNames['index'];

        if( isset(self::$pageNames[$page]) )
            $name = self::$pageNames[$page];

        if( $echo ) echo $name; else return $name;
    }
	
	public static function get_translated_month( $monthnum=1 ){

		$monthnum = intval($monthnum);
		
		if($monthnum > 0){

			$months = self::get_translated_strings('months');	$i=0;
			
			if (count($months)) foreach ($months as $month=>$values){ 
				$i++; if ($i == $monthnum) return $month; 
			}

		}
		
	}

    public static function base_url($relative_url='/'){
        return ( rtrim( fn_Util::get_base_url(), '/' ) . '/' . trim($relative_url, '/') );
    }

    public static function current_page_url(){
        return ( fn_Util::get_server_base_url() . $_SERVER['REQUEST_URI'] );
    }
	
	public static function get_body_class(){
	
		$classes = array();
	
		if ( fn_User::is_authenticated() )
			$classes[] = "logged-in";
		else
			$classes[] = "logged-out";
	
		if( isset($_GET['p']) )
			$classes[] = ( "page-" . self::esc_html( strtolower( urldecode($_GET['p'] ) ) ) );
		else{
			if ( fn_User::is_authenticated() )
				$classes[] = "dashboard";
			else
				$classes[] = "login";
		}
			
		return implode(" ", $classes);
	
	}

    public static function pagination_get_current_offset($page, $per_page=25){
        if( $page <= 1 ) return 0; return ($page-1)*$per_page;
    }

    public static function pagination_get_multiplier($offset, $per_page=25){
        $multiplier = floor($offset/$per_page);return $multiplier <= 0 ? 1 : $multiplier+1;
    }

    public static function pagination_add_standard_vars($baseurl){

        return $baseurl;

        $stdvars = array('p', 't', 'sdate', 'edate', 'labels', 'accounts', 'currency_id', 'type'); //TODO add standard supported GET vars
        $setvars= array();

        foreach($stdvars as $var) if ( isset($_GET[$var]) ) $setvars[$var] = urlencode($_GET[$var]);

        if( ( strpos($baseurl, '?') === false ) and ( strpos($baseurl, '&') === false ) )
            $baseurl.="?";
        elseif( strpos($baseurl, '?') > 0)
            $baseurl.="&";

        return ( $baseurl . http_build_query($setvars) );
    }

	public static function pagination($total, $per_page=25, $current=0, $baseurl="?", $smartnav=15, $echo=TRUE, $bootstrap_compatible=TRUE){

        if (empty($per_page)) $per_page = intval($_GET['per_page']);

        $nr_pages = floor($total/$per_page);
        $lastPage  = $total%$per_page;

        $nav  	 = "";
        $jPage 	= 0;
        $i     		= 1;
        $starter = 0;
        $finisher =0;

        $before_a = $after_a =  $before_current = "";

        if ($bootstrap_compatible){
            $before_a 			= '<li>';
            $after_a	   			= '</li>';
            $before_current	= '<li class="active">';
        }

        if ( ($total/$per_page) > 1 ){

            if (  $smartnav > 0 ){
                $smartnav 	= (floor($smartnav/2) == 0) ? ($smartnav +1) : $smartnav; //this must be an odd number
            }

            if ( $nr_pages > $smartnav ){ //set up a starter so, we don't generate all of the pages, only a smaller subset needed
                $starter = ( $current - (2 * $per_page) );
                $finisher= ( $current + (2 * $per_page) );

                $starter = $starter > 0 ? $starter : 0;

                $nr_pages = $finisher > $nr_pages ? $nr_pages : $finisher;
                //TODO add a limiter, so only pages in range are generated

            }

            $pages = array();

            for ($i = 0; $i < $nr_pages; $i++){

                if ( ( $current == $jPage ) or ( ($current > $jPage) and $current < ( $jPage + $per_page)) ) {
                    $pages[] 		= ($before_current . '<a id="current" >' . ($i+1) .'</a> ' . $after_a);
                    $current_page = count($pages)-1;
                }
                else {
                    $pages[]= ($before_a . '<a href="' . self::pagination_add_standard_vars( $baseurl . '&pag=' . self::pagination_get_multiplier($jPage, $per_page) ) . '">' . ($i+1) .' </a> ' . $after_a);
                }

                $jPage+= $per_page;

            }

            if ($lastPage > 0){

                if ( $current == $jPage ){
                    $pages[]			= ($before_current . '<a id="current">'. ($i+1) .'</a> ' . $after_a);
                    $current_page = count($pages)-1;
                }
                else {
                    $pages[]= ($before_a . ' <a href="' . self::pagination_add_standard_vars( $baseurl . '&pag=' . self::pagination_get_multiplier($jPage, $per_page) ) . '">'. ($i+1) .' </a> ' . $after_a);
                }
            }
        }

        if( $smartnav > 0 ){

            $half  = floor($smartnav/2);

            if( count($pages) > $smartnav ){

                $lower = $current_page > $half ? $current_page - $half : 0;
                $upper = $current_page < $half ? $smartnav : $current_page + $half;

                if( $upper > count($pages) ){
                    $upper = count($pages); $lower = $upper - $smartnav-1;
                }

                if($lower > 0){
                    $pag = $pages[$lower];

                    $link = substr($pag, (strpos($pag, 'href="')+6));
                    $link = substr($link, 0, strpos($link, '"'));

                    $pages[$lower] = ($before_a . ' <a class="prev" href="' . $link . '">&lt;&lt; </a> '. $after_a);
                }

                if( $upper < count($pages) ) {
                    $pag = $pages[$upper];

                    $link = substr($pag, (strpos($pag, 'href="')+6));
                    $link = substr($link, 0, strpos($link, '"'));

                    $pages[$upper] = ($before_a . '<a class="next" href="' . $link . '"> &gt;&gt;</a> ' . $after_a);
                }

                for($i=$lower; $i<=$upper; $i++){
                    $nav.=$pages[$i];
                }
            }
            else{
                for($i=0; $i<count($pages); $i++){
                    $nav.=$pages[$i];
                }
            }

        }else{
            if(count($pages)) foreach($pages as $p){
                $nav.= $p;
            }
        }

        if($echo) echo $nav;

        return $nav;
	}

    public static function get_file_url($file, $encode=true){
        if( strpos($file, FNPATH) === false ){
            $file_url = str_replace(FN_URL, "", $file); $file_url = ( FN_URL . $file_url );
        }
        else $file_url = str_replace(FNPATH, FN_URL, $file);

        if($encode) $file_url = urlencode($file_url);

        return $file_url;
    }

    public static function get_file_download_url($file, $remove_after_download=null, $custom_filename=null){

        $file = str_replace(FNPATH, "", $file);
        $file = urlencode($file);

        $vars = "";

        if( $remove_after_download ) $vars.="&del=1";
        if( $custom_filename ) $vars.= "&name=" . urlencode($custom_filename);

        return ( FN_URL . "/snippets/file-download.php?file=" . $file . $vars );

    }

    public static function get_file_preview_url($file, $vars=array()){
        $file = str_replace(FNPATH, "", $file);
        $file = urlencode($file);

        $query = ""; if( count($vars) ){ $query = http_build_query($vars); $query = ('&' . $query); }

        return ( FN_URL . "/snippets/file-preview.php?file=" . $file . $query );
    }

	public static function html_embed_file($file_url, $display_filename=null, $download_url=null){

        $ext = fn_Util::get_file_extension($file_url); $download_url = empty($download_url) ? $file_url : $download_url;

        if( empty($display_filename) ) $display_filename = basename($file_url);

        $images = array('jpg', 'jpeg', 'gif', 'png');
        $videos = array();
        $sounds = array();

        if( in_array($ext, $images) ) : /*it's an mage*/ ?>
            <div class="file-embed image"><img src="<?php echo $file_url; ?>"/></div>
        <?php return; endif;

        if( $ext == 'pdf' ) : /*it's a pdf file*/ ?>
            <div class="file-embed pdf"><iframe src="<?php echo $file_url; ?>" scrolling="no" style="border: 0; width: 100%; height: 460px;" wmode="opaque"></iframe> </div>
            <?php return; endif;

        if( in_array($ext, $videos) ) : /*it's a video*/ ?>
            <div class="file-embed">
                <video width="100%" controls><source src="<?php echo $file_url; ?>" type="video/<?php echo $ext; ?>"></video>
            </div>
        <?php return; endif;

        if( in_array($ext, $sounds) ) : /*it's a sound*/ ?>
            <div class="file-embed">
                <audio controls width="100%"><source src="<?php echo $file_url; ?>" type="audio/<?php echo $ext; ?>"></audio>
            </div>
        <?php return; endif;

            /*all other files*/ ?>
            <div class="file-embed">
                <p style="font-size: 3em; text-align: center; padding: 25px 0px; border: 1px #bbb solid; border-radius: .2em; margin: 20px;">
                    <a href="<?php echo $download_url; ?>" style="text-decoration: none;">
                        <span class="icon-file"></span> <?php echo $display_filename; ?>
                    </a>
                </p>
            </div>
        <?php return;

    }

}