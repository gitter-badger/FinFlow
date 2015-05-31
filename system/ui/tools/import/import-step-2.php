<?php

if ( !defined('FNPATH') ) exit();

use FinFlow\UI;
use FinFlow\OP;
use FinFlow\Session;
use FinFlow\Helpers\HTML\HTML;

$imported = false;
$columns  = Session::getFlashdata('import_cols');
$defaults = OP::getHumanReadableArgsArray(true);
$errors   = $notices = array();

if( post('colmatch') ){
	//TODO...
}


$columnsSelect = HTML::select('', $columns, array('class'=>'form-control'));

UI::show_errors($errors); UI::show_notes($notices);

?>

<div class="panel panel-default">

	<div class="panel-heading">
		<h4>Import Step 2</h4>
	</div>

	<div class="panel-body">

		<form name="importColumnsMatchForm" id="importColumnsMatchForm" action="<?php UI::url('/tools/import/'); ?>" class="form form-horizontal">

			<?php print_r($defaults); ?>

			<?php foreach ($defaults as $field=>$readable): ?>
			<div class="form-group">
				<label class="control-label col-lg-4"><?php echo ucwords( __t($readable) ); ?>: </label>
				<div class="col-lg-4"><?php echo $columnsSelect; ?></div>
				<div class="col-lg-4">
					<input type="text" name="test" class="form-control" value=""/>
				</div>
			</div>
			<?php endforeach; ?>

		</form>


	</div>

</div>