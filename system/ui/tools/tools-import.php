<?php if ( !defined('FNPATH') ) exit();

use FinFlow\UI;
use FinFlow\OP;
use FinFlow\File;
use FinFlow\CanValidate;
use FinFlow\Import;

$imported = false;
$errors   = $notices = array();

if( isset($_POST['import']) ){

	$File = ufile('importfile');

    if( CanValidate::is_uploaded_file('importfile') ){

        //--- upload the file to import ---//
        $uploaded = File::upload( $File );

        if( $uploaded ){

            $Importer = Import::getImporter($uploaded);

            try{

	            OP::import($Importer);

            }
            catch(Exception $e){
	            $errors[] = $e->getMessage();
            }

            //$Importer->remove_file(); //for safety, remove the uploaded file

        }
        else
	        $errors = array_merge(array('Could not upload file!'), File::getErrors());

    }
    else
	    $errors[] = __t('Choose a file to import.');

}

?>

<?php if( ! $imported ): ?>

	<?php UI::show_errors($errors); UI::show_notes($notices); ?>

	<form class="form form-horizontal" action="<?php UI::url('tools/import', array('t'=>'import'))?>" method="post" name="export-data-form" id="exportDataForm" enctype="multipart/form-data">

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
    <div class="alert alert-success">
        Fi&#351;ierul a fost importat cu succes. <?php echo $imported; ?> de inregistrari au fost sincronizate.
    </div>
<?php endif;