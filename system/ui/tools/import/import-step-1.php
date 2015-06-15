<?php

if ( !defined('FNPATH') ) exit();

use FinFlow\UI;
use FinFlow\OP;
use FinFlow\Session;
use FinFlow\File;
use FinFlow\CanValidate;
use FinFlow\Import;

$prepared = false;
$errors   = $notices = array();

if( post('import') ){

	$File = ufile('importfile');

	if( CanValidate::is_uploaded_file('importfile') ){

		//--- upload the file to import ---//
		$uploaded = File::upload( $File );

		if( $uploaded ){

			try{

				$Importer= Import::getImporter($uploaded);
				$columns = $Importer->getColumns();

				Session::setFlashdata('import_cols', $columns);
				Session::setFlashdata('import_file', $uploaded);

				$prepared = true;

			}
			catch(Exception $e){
				$errors[] = $e->getMessage();
			}

		}
		else
			$errors = array_merge(array('Could not upload file!'), File::getErrors());

	}
	else
		$errors[] = __t('Choose a file to import.');

}

UI::show_errors($errors);
UI::show_notes($notices);

?>

	<div class="panel panel-default">

		<div class="panel-heading">
			<h4>Import</h4>
		</div>

		<div class="panel-body">

			<?php if( ! $prepared ): ?>

			<form class="form form-horizontal" action="<?php UI::url('tools/import', array('step'=>1)); ?>" method="post" name="export-data-form" id="exportDataForm" enctype="multipart/form-data">

				<?php if(empty($_POST)): ?>
					<div class="alert alert-info">
						<span class="icon-info-sign"></span>
						Actiunea de import va incerca sa sincronizeze datele din fisierul importat cu cele stocate in baza de date . <br/>
						Este recomandat sa faci un <a href="<?php UI::url('tools/export'); ?>">export</a> in prealabil al datelor existente.
					</div>
				<?php endif; ?>

				<div class="form-group">
					<label class="control-label col-lg-3" for="importfile">Fi&#351;ier export: </label>
					<div class="col-lg-4">
						<input class="form-control" type="file" name="importfile" id="importfile" value=""/>
					</div>
				</div>


				<div class="form-group">
					<div class="col-lg-12 align-center form-submit">
						<input type="hidden" name="import" value="1"/>
						<button type="submit" class="btn btn-primary">Import&#259;</button>
					</div>
				</div>

			</form>

		<?php else: ?>
			<a href="<?php UI::url('tools/import/?step=2'); ?>" class="btn btn-success btn-lg">
				Next step <i class="fa fa-chevron-right"></i>
			</a>
		<?php endif; ?>

	</div>

</div>