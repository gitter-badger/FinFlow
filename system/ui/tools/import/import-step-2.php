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

if( count($_POST) ){

	$import = post('import');
	$matched= post('columns');

	if( ! empty($import) ){

		$columnsHints = array();

		foreach($import as $index){
			if( isset($columns[$index]) )
				$columnsHints[$columns[$index]] = $matched[$index];
		}


		if( empty($columnsHints) )
			$errors[] = __t('No columns matched. Please select the columns you want to match');

		Session::setFlashdata('import_col_hints', $columnsHints);
		Session::setFlashdata('import_step', 3);

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

		<?php if( ! $matched ): ?>
			<form name="importColumnsMatchForm" id="importColumnsMatchForm" action="<?php UI::url('/tools/import/'); ?>" class="form form-horizontal" method="post">

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

				<div class="form-group">
					<div class=" col-lg-12 align-center">
						<button class="btn btn-primary btn-submit" type="submit">
							Preview <i class="fa fa-chevron-right"></i>
						</button>
					</div>
				</div>

			</form>
		<?php else: ?>
			<div class=" col-lg-12 align-center">
				<a href="<?php UI::url('tools/import'); ?>" class="btn btn-primary btn-submit">
					Next step <i class="fa fa-chevron-right"></i>
				</a>
			</div>
		<?php endif; ?>

	</div>

</div>