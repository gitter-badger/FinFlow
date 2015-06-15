<?php

if ( !defined('FNPATH') ) exit();

use FinFlow\UI;
use FinFlow\OP;
use FinFlow\Session;
use FinFlow\Helpers\HTML\HTML;

$matched = false;
$columns  = Session::getFlashdata('import_cols');
$defaults = OP::getHumanReadableArgsArray(true);
$errors   = $notices = array();

//TODO add support for excel import and other file types

if( count($_POST) ){

	$import = post('import');
	$hinted  = post('columns');

	if( ! empty($import) ){

		$columnsHints = array();

		foreach($import as $index){
			if( isset($columns[$index]) )
				$columnsHints[$index] = $hinted[$index];
		}


		if( empty($columnsHints) )
			$errors[] = __t('No columns matched. Please select the columns you want to match');
		else
			$matched = true;

		Session::setFlashdata('import_col_hints', $columnsHints);

	}
	else
		$errors[] = __t('Please select columns to import.');
}

$columnsSelect = HTML::select('columns[]', $defaults, null, array('class'=>'form-control'));

UI::show_errors($errors);
UI::show_notes($notices);

?>

<div class="panel panel-default">

	<div class="panel-heading">
		<h4>Select and match fields to import</h4>
	</div>

	<div class="panel-body" style="padding-top: 0px;">

		<?php if( empty($matched) ): ?>
			<form name="importColumnsMatchForm" id="importColumnsMatchForm" action="<?php UI::url('/tools/import/', array('step'=>2)); ?>" class="form form-horizontal" method="post">

				<?php foreach ($columns as $k=>$name): $htmlId = ( 'import-field-' . ($k+1) );?>
				<div class="form-group import-field-line default" id="<?php echo $htmlId; ?>">
					<label class="control-label col-lg-4">
						<input type="checkbox" name="import[]" onchange="fn_check_highlight(this, '#<?php echo $htmlId; ?>');" value="<?php echo $k; ?>"/>
						<?php echo ucwords( __t($name) ); ?>:
					</label>
					<div class="col-lg-4">
						<?php echo $columnsSelect; ?>
					</div>
				</div>
				<?php endforeach; ?>

			</form>
		<?php else: ?>
			<script type="application/javascript">
				window.location.href = '<?php UI::url('/tools/import', array('step'=>3)); ?>';
			</script>
		<?php endif; ?>

	</div>

	<div class="panel-footer">
		<div class="row">
			<div class=" col-lg-12 align-center">

				<button class="btn btn-primary btn-submit" type="submit">
					<i class="fa fa-chevron-left"></i> Previous step
				</button>

				<button class="btn btn-primary btn-submit" type="button" onclick="document.getElementById('importColumnsMatchForm').submit();">
					Next step <i class="fa fa-chevron-right"></i>
				</button>
			</div>
		</div>
	</div>

</div>