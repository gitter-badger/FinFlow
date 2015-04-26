<?php
/**
 * FinFlow 1.0 - Actions for web
 * @author Adrian S. (adrian.silimon@yahoo.com)
 * @version 1.0
 */

use FinFlow\UI;
use FinFlow\User;

$router = new \Klein\Klein();

if( User::is_authenticated() ){

	//pages and sections
	$router->respond('/[:section]/[:page]', function(){
		die("User is authenticated"); //TODO...
	});

}
else{

	//password reset
	$router->respond('/recover/?', function(){
		UI::start('public/pwreset');
	});

	//login
	$router->respond('/login/?', function(){
		UI::start('public/login');
	});

	//serve login page by default
	$router->respond('/?', function(){
		UI::start('public/login');
	});

}

//captcha output
$router->respond('/captcha/?', function(){
	UI::component('extras/captcha');
});

//404 error
$router->respond(function(){
	UI::start('errors/404');
});


$router->dispatch();