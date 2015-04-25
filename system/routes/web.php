<?php
/**
 * FinFlow 1.0 - Routes for web
 * @author Adrian S. (adrian.silimon@yahoo.com)
 * @version 1.0
 */

use FinFlow\UI;
use FinFlow\User;

$router = new \Klein\Klein();

if( User::is_authenticated() ){

	$router->respond('/[:section]/[:page]', function($request){
		die("User is authenticated"); //TODO...
	});

}
else{

	$router->respond('/recover/?', function($request){
		UI::start('public/pwreset');
	});

	$router->respond('/login/?', function($request){
		die("Loading login page...");
	});

	$router->respond('/?', function($request){
		die("Loading login page...");
	});
}

//print_r($_SERVER); die();


$router->dispatch();