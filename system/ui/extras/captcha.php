<?php
/**
 * Outputs a captcha image or math challenge, if GD extension is missing
 * Captcha::init() should be called previously loading this file
 */

use FinFlow\Captcha;

$maxtries = 99;
$size     = get('mag') ? array(500, 240, 77) : array(100, 30, 12);

list($width, $height, $fontsize) = $size;

//TODO switch to Session:: class
$_SESSION['captcha_tries'] = intval($_SESSION['captcha_tries']) +1;

//--- limit the number of captcha tries for current session ---//
if( $_SESSION['captcha_tries'] > $maxtries ) exit;
//--- limit the number of captcha tries for current session ---//


function output_captcha($width, $height, $fontsize){

    if( Captcha::supports_img() and ! headers_sent() )
        Captcha::output_img($width, $height, array('r'=>0, 'g'=>0, 'b'=>0), $fontsize);
    else
        Captcha::output_math();

}


if( Captcha::is_initialized() ){

	output_captcha($width, $height, $fontsize);

}
else
	UI::fatal_error("Codul captcha nu a fost ini&#355;ializat.", true, true, fn_UI::$MSG_WARN, 'Aten&#355;ie:', 'Aten&#355;ie');