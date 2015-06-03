<?php

if ( !defined('FNPATH') ) exit();

use FinFlow\UI;
use FinFlow\OP;
use FinFlow\Session;
use FinFlow\Helpers\HTML\HTML;

$matched       = false;
$columnsMatch  = Session::getFlashdata('import_col_hints');
$defaults = OP::getHumanReadableArgsArray(true);
$errors   = $notices = array();

if( post('confirm') ){

	//TODO proceed with import

}


UI::show_errors($errors);
UI::show_notes($notices);

?>

<div class="panel panel-default">

	<div class="panel-heading">
		<h4>Select and match fields to import</h4>
	</div>

	<div class="panel-body">



	</div>

</div>