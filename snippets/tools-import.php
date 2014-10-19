<?php if ( !defined('FNPATH') ) exit();

include_once ( FNPATH . '/inc/class.importer.php' );

if( isset($_POST['import']) ){

    $imported = false;
    $errors      = array();

    if( strlen($_FILES['importfile']['name']) ){

        //--- upload the file to import ---//
        $uploaded = fn_Util::file_upload($_FILES['importfile'], FN_UPLOADS_DIR, null, "", "", array('fdata', 'xml'));

        if( $uploaded['success'] ){

            $Importer = new fn_Importer($uploaded['msg']);
            $imported = $Importer->import();

            if( !$imported ) $errors = $Importer->Errors;

            $Importer->remove_file(); //for safety, remove the uploaded file

        }
        else $errors[] = $uploaded['msg'];

    }
    else $errors[] = "Alege un fisier pentru import.";

}

?>

<?php if( !$imported ): ?>

<?php fn_UI::show_errors($errors); fn_UI::show_notes($notices); ?>

<form class="form form-horizontal" action="<?php fn_UI::page_url('tools', array('t'=>'import'))?>" method="post" name="export-data-form" id="exportDataForm" enctype="multipart/form-data">

    <?php if(empty($_POST)): ?>
    <div class="alert alert-info">
        <span class="icon-info-sign"></span> Actiunea de import va incerca sa sincronizeze datele din fisierul importat cu cele stocate in baza de date . <br/>
        Este recomandat sa faci un <a href="<?php echo fn_UI::page_url('tools', array('t'=>'export')); ?>">export</a> in prealabil al datelor existente.
    </div>
    <?php endif; ?>

    <div class="form-group">
        <label class="control-label col-lg-3" for="importfile">Fi&#351;ier export: </label>
        <div class="col-lg-4">
            <input class="form-control" type="file" name="importfile" id="importfile" value=""/>
        </div>
    </div>


    <div class="form-group">
        <div class="col-lg-12 align-center">
            <input type="hidden" name="import" value="1"/>
            <button type="submit" class="btn btn-primary">Import&#259;</button>
        </div>
    </div>


<?php else: ?>
    <div class="alert alert-success"> Fi&#351;ierul a fost importat cu succes. <?php echo $imported; ?> de inregistrari au fost sincronizate.</div>
<?php endif;