<?php
/**
 * FinFlow 1.0 - Actions for ajax requests
 * @author Adrian S. (adrian.silimon@yahoo.com)
 * @version 1.0
 */

use FinFlow\UI;
use FinFlow\User;
use \Klein\Klein;

$router = new Klein();

if( User::is_authenticated() ){

	//Respond to ajax requests

}
else{

	UI::header_400();

	//AJAX::error('messsage', 400);

}


$router->dispatch();