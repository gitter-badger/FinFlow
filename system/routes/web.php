<?php
/**
 * FinFlow 1.0 - Actions for web
 * @author Adrian S. (adrian.silimon@yahoo.com)
 * @version 1.0
 */

use FinFlow\UI;
use FinFlow\User;
use \Klein\Klein;

$router = new Klein();

if( User::is_authenticated() ){

	//dashboard
	$router->respond('/dashboard/?', function(){
		UI::start('main/dashboard');
	});

	//logout
	$router->respond('/logout/?', function(){
		UI::start('extras/logout');
	});

	$router->respond('/[:section]/?', function($request){
		UI::start($request->section);
	});

	//pages and sections
	$router->respond('/[:page]/[:section]/?', function($request){

		if( ($request->page == 'transactions') and ( $request->section == 'detail' )){
			UI::component('transactions/transaction-details');
		}
		else
			UI::start($request->page);

	});

	//serve dashboard page by default
	$router->respond('/?', function(){
		UI::start('main/dashboard');
	});

}
else{

	//serve login page by default
	$router->respond('/?', function(){
		UI::start('public/login');
	});

}

//password reset
$router->respond('/recover/?', function(){
	UI::start('public/pwreset');
});

//login
$router->respond('/login/?', function(){
	UI::start('public/login');
});

//captcha output
$router->respond('/captcha/?', function(){
	UI::component('extras/captcha');
});

//http errors
$router->onHttpError(function($code){

	switch ($code) {

		case 404:
			UI::header_404(); UI::start('errors/404');
		break;

		default:
			UI::fatal_error('There\'s a ' . $code . ' error', true, true, 'warning', 'Ops! ', 'Ops!', false);

	}

});


$router->dispatch();