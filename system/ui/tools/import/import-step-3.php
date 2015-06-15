<?php

if ( !defined('FNPATH') ) exit();

use FinFlow\UI;
use FinFlow\OP;
use FinFlow\Session;
use FinFlow\Import;

$importFile    = Session::getFlashdata('import_file', false);
$columnsMatch  = array_values( Session::getFlashdata('import_col_hints', false) );
$humanReadable = OP::getHumanReadableArgsArray(true);
$errors        = $notices = array();


$Importer = Import::getImporter($importFile);

UI::show_errors($errors);
UI::show_notes($notices);

?>

<div class="panel panel-default">

	<div class="panel-heading">
		<h4>Define columns defaults</h4>
	</div>

	<div class="panel panel-body">

		<form name="importColumnsOverrideForm" id="importColumnsOverrideForm" action="<?php UI::url('/tools/import/', array('step'=>3)); ?>" class="form form-horizontal" method="post">

			<?php foreach ($humanReadable as $field=>$column):

				if( in_array($field, $columnsMatch) )
					continue;

				$htmlId = ( 'import-field-' . $field );

			?>

				<div class="form-group import-field-line default" id="<?php echo $htmlId; ?>">
					<label class="control-label col-lg-4">
						<input type="checkbox" name="<?php echo $field; ?>" onchange="fn_check_highlight(this, '#<?php echo $htmlId; ?>');" value="<?php echo $field; ?>"/>
						<?php echo ucwords( __t($column) ); ?>:
					</label>
					<div class="col-lg-4">
						<input type="text" name="<?php echo $field; ?>_override" class="form-control" value=""/>
					</div>
				</div>


			<?php endforeach; ?>

		</form>

	</div>

	<div class="panel-footer">
		<div class="row">
			<div class=" col-lg-12 align-center">

				<a href="<?php UI::url('/tools/import', array('step'=>2)); ?>" class="btn btn-primary btn-submit">
					<i class="fa fa-chevron-left"></i> Previous step
				</a>

				<a href="#submit" class="btn btn-primary btn-submit" onclick="document.getElementById('importColumnsOverrideForm').submit();">
					Next step <i class="fa fa-chevron-right"></i>
				</a>
			</div>
		</div>
	</div>

</div>