<?php

if ( !defined('FNPATH') ) exit();

use FinFlow\UI;
use FinFlow\Session;


$step = Session::getFlashdata('import_step');

if( ! $step ){
	UI::component('tools/import/import-step-1');
}
else
	UI::component('tools/import/import-step-' . intval($step));