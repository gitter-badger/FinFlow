<?php

namespace FinFlow;

class UI{

    const MSG_ERROR   = 'danger';
	const MSG_NOTE    = 'info';
	const MSG_WARN    = 'warning';
	const MSG_SUCCESS = 'success';

    public static $pageNames = array(
        'index'         => 'Panou Principal',
        'dashboard'     => 'Panou Principal',
        'transactions'  => 'Tranzac&#355;ii',
        'performance'   => 'Performan&#355;&#259;',
        'labels'        => 'Etichete',
        'accounts'      => 'Conturi',
        'currencies'    => 'Monede',
        'tools'         => 'Unelte',
        'settings'      => 'Set&#259;ri',
        'login'         => 'Autentificare',
        'pwreset'       => 'Recuperare parol&#259;'
    );

    public static $transactionTypesNames = array(FN_OP_IN=>'venit', FN_OP_OUT=>'cheluial&#259;');

    protected static $js_assets  = array();
    protected static $css_assets = array();

    protected static $inline_js  = array();
    protected static $inline_css = array();

    /**
     * Returns an html formatted message
     * @param $msg
     * @param string $type
     * @param bool $dismissable
     * @param bool $translate
     */
	public static function msg($msg, $type="note", $dismissable=true, $translate=true){
		if ( strlen($msg) ) echo '<div class="alert alert-' . $type . '"> ' . ( $dismissable ? '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">x</span></button>' : '' ) . $msg . ' </div>';
	}

    /**
     * Generates the css for the fatal error pages
     */
	public static function error_page_css(){ ?>
		<style>
		<!--
		
		body{
			margin: 0px;
			padding: 0px;
			color: #000;
			font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif;
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

	public static function fatal_error($msg, $die=TRUE, $html=TRUE, $msgtype="danger", $prefix="Error: ", $title="Error", $http_header=true){

        if( defined('FN_BYPASS_UI_FATAL_ERRORS') and FN_BYPASS_UI_FATAL_ERRORS )
	        return false;

		if( $http_header )
			self::header_500();

		if ( $html ): ?>
			<html>
			<head>
				<meta charset="utf-8"/>
				<title>FinFlow | <?php __e($title); ?> </title>
				<link rel="stylesheet" type="text/css" media="all" href="<?php UI::asset_url('/assets/css/bootstrap.min.css'); ?>"/>
				<link rel="stylesheet" type="text/css" media="all" href="<?php UI::asset_url('/assets/css/font-awesome.min.css'); ?>"/>
				<link rel="stylesheet" type="text/css" media="all" href="<?php UI::asset_url('/assets/css/styles.css'); ?>" />
			</head>
			<body id="page-error" class="error fatal-error">
				<div class="wrap container-fluid">
					<div class="row">
                        <div class="col-lg-12">

	                        <p class="logo">
		                        <img align="absmiddle" src="<?php UI::asset_url('/assets/images/finflow-logo.png'); ?>">
	                        </p>

                            <div class="alert alert-<?php echo $msgtype; ?>">
	                            <strong><i class="fa fa-exclamation-triangle"></i> <?php __e($prefix); ?> </strong><?php echo $msg; ?>
                            </div>
                        </div>
					</div>
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
            <link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('styles/bootstrap.min.css'); ?>" />
            <link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('styles/font-awesome.min.css'); ?>" />
            <link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('styles/style.css'); ?>" />
        </head>
        <body id="page-loading" class="loading">
            <div class="wrap container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <p style="text-align: center">
                            <img src="<?php echo FN_URL; ?>/images/preloader.gif" align="absmiddle"/><br/><br/>
                        </p>
                        <div class="clearfix"></div>
                        <div class="alert alert-info" style="text-align: center;">
                            <em><?php echo $msg; ?></em>
                        </div>
                    </div>
            </div>
        </body>
        </html>
        <?php

        if( $die ) die();
    }

	public static function start($component=false, $args=array()){
		$component = empty($component) ? 'main/dashboard' : trim( Util::xss_filter($component) ); include_once (FNPATH . '/system/ui/main.php');
	}

	/**
	 * Includes a ui component with desired variables
	 * @param $template
	 * @param array $vars
	 */
	public static function component($template, $vars=array()){
		global $fndb, $fnsql; @extract($vars, EXTR_SKIP); $anon_tpl_path = ( FNPATH . '/system/ui/' . Util::xss_filter( trim( $template ) ) . '.php' ); if( file_exists( $anon_tpl_path ) ) include $anon_tpl_path; else ( headers_sent() ? self::msg("UI Template {$anon_tpl_path} not found... .") : self::fatal_error("UI Template {$anon_tpl_path} not found... .") );
	}

	public static function header_nocache($legacy=false){
		if( ! headers_sent() ) {

			header( "Cache-Control: no-cache, must-revalidate" ); // HTTP/1.1

			if( $legacy )
				header( "Expires: Sat, 26 Jul 1997 05:00:00 GMT" );   // Date in the past
		}
	}

	public static function header_404(){
		if( ! headers_sent() )
			header( $_SERVER['SERVER_PROTOCOL'] . " 404 Not Found", true, 404);
		else
			self::msg('Could not set 404 header! Headers already sent.', self::MSG_ERROR);
	}

	public static function header_500($message='Internal Server Error'){
		if( ! headers_sent() )
			header( ( $_SERVER['SERVER_PROTOCOL'] . ' 500 ' . $message ), true, 500);
		else
			self::msg('Could not set 500 header! Headers already sent.', self::MSG_ERROR);
	}

	public function header_redirect($url, $permament=false){

		if( ! headers_sent() ){

			if( $permament )
				header( 'Location: ' . $url, true, 301 );
			else
				header( 'Location: ' . $url, true, 307);

		}

	}

	public static function esc_html( $input ){
		return htmlentities( stripslashes($input), ENT_NOQUOTES, 'UTF-8' );
	}
	
	public static function esc_attr( $input ){
		return htmlspecialchars( stripslashes( Util::xss_filter($input) ) , ENT_NOQUOTES, 'UTF-8' );
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
			foreach ($errors as $msg) self::msg($msg, self::MSG_ERROR);
		else 
			self::msg($errors, 'error');
	}
	
	public static function show_notes($notes){
		if ( is_array($notes) )
			foreach ($notes as $msg) self::msg($msg, self::MSG_NOTE);
		else
			self::msg($notes, 'note');
	}

    public static function show_warnings($warnings){
        if ( is_array($warnings) )
            foreach ($warnings as $msg) self::msg($msg, self::MSG_WARN);
        else
            self::msg($warnings, 'warning');
    }

	public static function show_success(){
		//TODO...
	}
	
	
	public static function get_translated_strings($what='months'){
			$months = array(
				'Ianuarie'	   =>array('january', 'jan'),
				'Februarie'	   =>array('february', 'feb'),
				'Martie'	   =>array('march', 'mar'),
				'Aprilie'	   =>array('april', 'apr'),
				'Mai'		   =>array('may'),
				'Iunie'	       =>array('june', 'jun'),
				'Iulie'		   =>array('july', 'jul'),
				'August'	   =>array('august', 'aug'),
				'Septembrie'   =>array('september', 'sep'),
				'Octombrie'	   =>array('october', 'oct'),
				'Noiembrie'	   =>array('november', 'nov'),
				'Decembrie'	   =>array('december', 'dec')
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
				$date = isset($m[0]) ? str_replace($m[0], $translated, $date) : $date;
				$date = isset($m[1]) ? str_replace($m[1], $translated, $date) : $date;
			}
			
			if (count($days)) foreach ($days as $translated=>$d){
				$date = isset($d[0]) ? str_replace($d[0], $translated, $date) : $date;
				$date = isset($d[1]) ? str_replace($d[1], $translated, $date) : $date;
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
        return Util::format_nr($number, $decimals, $precision);
    }

    public static function format_money($value, $currency=false, $show_cc=false, $currency_after=false){

        $value = self::format_nr($value);

        $currency = empty($currency) ? 0 : $currency;

        if( empty($currency) ){
            $currency = Currency::get_default();
        }else{

            if( is_string($currency) )
                $currency = Currency::get_by_code($currency);

            if( is_int( $currency ) )
                $currency = Currency::get($currency);

        }

        if( is_object($currency) ){
            $s_currency = $show_cc ?  $currency->ccode : $currency->csymbol; return $currency_after ? "{$value} {$s_currency}" : "{$s_currency} {$value}";
        }

        return $value;
    }



	public static function page_url($component='index', $vars=array()){
		$url = ( trim(FN_URL, '/') . '/' . trim($component, '/') . '/' . ( count($vars) ? ( '?' . http_build_query($vars) ) : '' ) ); return $url;
	}

	public static function url($component, $vars=array(), $echo=TRUE){
		if( $echo )
			echo self::page_url($component, $vars);
		else
			return self::page_url($component, $vars);
	}

    public static function asset_url($asset, $echo=true, $version=null){
        $version = empty($version) ? FN_VERSION : $version; $url = ( self::base_url($asset) . '?v=' . $version ); if( $echo ) echo $url; else return $url;
    }

	public static function url_part($index=1){

		$path  = parse_url( self::current_page_url(), PHP_URL_PATH );
		$parts = @explode('/', trim($path, '/'));

		return $index == 0 ? '/' : ( isset($parts[$index-1]) ? urldecode($parts[$index-1]) : null );
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
        return ( rtrim( Util::get_base_url(), '/' ) . '/' . trim($relative_url, '/') );
    }

    public static function current_page_url(){
        return ( Util::get_server_base_url() . $_SERVER['REQUEST_URI'] );
    }
	
	public static function get_body_class($main_component=null){
	
		$classes = array();
	
		if ( User::is_authenticated() )
			$classes[] = "logged-in";
		else
			$classes[] = "logged-out";
	
		if( isset($_GET['p']) ) {
            $classes[] = ( 'page-' . get('p') ); if( isset($_GET['t']) ) $classes[] = ( 'page-' . get('p') . '-' .  get('t') ); else $classes[] =   ( 'page-' . get('p') . '-index' );
        }else{
			if ( User::is_authenticated() )
				$classes[] = "dashboard";
			else
				$classes[] = "login";
		}
			
		return implode(" ", $classes);
	
	}

    public static function sidebar_grid_class($default='col-sm-offset-1 col-lg-2 col-md-2 col-sm-3'){
        $setting = Settings::get('sidebar_grid_class', $default); echo $setting;
    }

    public static function main_container_grid_class($default='col-lg-9 col-md-9 col-sm-7'){
        $setting = Settings::get('main_container_grid_class', $default); echo $setting;
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
        $setvars = array();

        foreach($stdvars as $var) if ( isset($_GET[$var]) ) $setvars[$var] = urlencode($_GET[$var]);

        if( ( strpos($baseurl, '?') === false ) and ( strpos($baseurl, '&') === false ) )
            $baseurl.="?";
        elseif( strpos($baseurl, '?') > 0)
            $baseurl.="&";

        return ( $baseurl . http_build_query($setvars) );
    }

	public static function pagination($total, $per_page=25, $current=0, $baseurl="?", $smartnav=15, $echo=true, $bootstrap_compatible=true, $onpage_link=''){

        if (empty($per_page)) $per_page = intval($_GET['per_page']);

        $nr_pages = floor($total/$per_page);
        $lastPage  = $total%$per_page;

        $nav  	 = "";
        $jPage 	 = 0;
        $i     	 = 1;
        $starter = 0;
        $finisher=0;

        $before_a = $after_a =  $before_current = "";

        if ($bootstrap_compatible){
            $before_a 		= '<li>';
            $after_a	   	= '</li>';
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

        if( $remove_after_download )
	        $vars.="&del=1";

        if( $custom_filename )
	        $vars.= "&name=" . urlencode($custom_filename);

        return ( FN_URL . "/system/ui/extras/file-download.php?file=" . $file . $vars );

    }

    public static function get_file_preview_url($file, $vars=array()){
        $file = str_replace(FNPATH, "", $file);
        $file = urlencode($file);

        $query = ""; if( count($vars) ){ $query = http_build_query($vars); $query = ('&' . $query); }

        return ( FN_URL . "/system/ui/extras/file-preview.php?file=" . $file . $query );
    }

	public static function html_embed_file($file_url, $display_filename=null, $download_url=null){

        $ext = Util::get_file_extension($file_url); $download_url = empty($download_url) ? $file_url : $download_url;

        if( empty($display_filename) ) $display_filename = basename($file_url);

        $images = array('jpg', 'jpeg', 'gif', 'png');
        $videos = array('mp4', 'ogv');
        $sounds = array('mp3', 'ogg');

        if( in_array($ext, $images) ) : /*it's an mage*/ ?>
            <div class="file-embed image embed-responsive-4by3"><img class="embed-responsive-item img-responsive" src="<?php echo $file_url; ?>"/></div>
        <?php return; endif;

        if( $ext == 'pdf' ) : /*it's a pdf file*/ ?>
            <div class="file-embed pdf embed-responsive embed-responsive-4by3">
                <iframe class="embed-responsive-item" src="<?php echo $file_url; ?>" scrolling="no" frameborder="0" wmode="opaque"></iframe>
            </div>
            <?php return; endif;

        if( in_array($ext, $videos) ) : /*it's a video*/ ?>
            <div class="file-embed embed-responsive embed-responsive-4by3">
                <video width="100%" class="embed-responsive-item" controls><source src="<?php echo $file_url; ?>" type="video/<?php echo $ext; ?>"></video>
            </div>
        <?php return; endif;

        if( in_array($ext, $sounds) ) : /*it's a sound*/ ?>
            <div class="file-embed embed-responsive">
                <audio class="embed-responsive-item" controls width="100%"><source src="<?php echo $file_url; ?>" type="audio/<?php echo $ext; ?>"></audio>
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

    public static function transaction_icon($type){
        ?><a class="fa fa-arrow-<?php echo $type == OP::TYPE_IN ? 'right' : 'left' ?> icon-transaction-<?php echo $type;  ?>" title="<?php echo self::$transactionTypesNames[$type]; ?>"></a><?php
    }

    public static function enqueue_js($src){
        if( ! in_array($src, self::$js_assets) ) self::$js_assets[] = ( Util::str_startswith('//', $src, true) or Util::str_startswith('http:', $src, true) ) ? $src : self::asset_url($src, false);
    }

    public static function enqueue_css($link){
        if( ! in_array($link, self::$css_assets) ) self::$css_assets[] = ( Util::str_startswith('//', $link, true) or Util::str_startswith('http:', $link, true) ) ? $link : self::asset_url($link, false);
    }

    public static function enqueue_inline($data, $type='css'){
        if( $type == 'css' )
            self::$inline_css[] = $data;
        else
            self::$inline_js[] = $data;
    }

    public static function js(){
        if( count(self::$js_assets) ) foreach (self::$js_assets as $asset_src) {
           ?><script type="text/javascript" src="<?php echo $asset_src; ?>"></script><?php
        }

        if( count(self::$inline_js) ) foreach (self::$inline_js as $inline) {
            ?><script type="text/javascript"><?php echo $inline; ?></script><?php
        }

	    self::$js_assets = self::$inline_js = array();

    }

    public static function css(){
        if( count(self::$css_assets) ) foreach (self::$css_assets as $asset_link) {
            ?><link rel="stylesheet" media="all" href="<?php echo $asset_link; ?>"/><?php
        }

        if( count(self::$inline_css) ) foreach (self::$inline_css as $inline) {
            ?><style type="text/css" media="all"><?php echo $inline; ?></style><?php
        }

	    self::$css_assets = self::$inline_css = array();
    }

}