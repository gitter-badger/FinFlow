<?php if ( !defined('FNPATH') ) exit();

include_once ( FNPATH . '/inc/class.exporter.php' );

$exported = false;

$errors = $notices = array();

if( isset($_POST['export']) ){

    $encrypted = $_POST['encrypted'] == 0 ? false : true;

    $exportFile = $encrypted ? (FN_BACKUP_DIR . '/finflow-export-' . date('Y-m-d') . '.fdata' ) : (FN_BACKUP_DIR . '/finflow-export-' . date('Y-m-d') . '.xml' );

    $_SESSION['fn_exported_file'] = $exportFile;

    //--- generate the export file ---//
    $Exporter  = new fn_Exporter( $exportFile );

    if( $Exporter->export_data($encrypted) )
        $exported = true;
    else
        $errors[] = "Fisierul export nu poate fi salvat. Verifica permisiunile asupra directorului radacina, apoi incearca din nou.";


    //--- generate the export file ---//

}

if( isset($_GET['download']) ){
    //--- download a previous exported file ---//
}

?>

<?php if( !$exported ): ?>

    <?php fn_UI::show_errors($errors); fn_UI::show_notes($notices); ?>

    <form action="<?php fn_UI::page_url('tools', array('t'=>'export'))?>" method="post" name="export-data-form" id="exportDataForm">

        <h4 class="form-label-normal"><em>Alege tipul de fi&#351;ier</em></h4>

        <p>
            <label for="encrypted_file"> <input type="radio" name="encrypted" id="encrypted_file" checked="checked" value="1"/> Fi&#351;ier criptat </label>
            <br class="clear"/>
            <span class="details">
                <em>Un fi&#351;ier criptat, p&#259;streaz&#259; informa&#355;iile exportate in siguran&#355;&#259;, insa nu este compatibil decat cu aceasta instalare a FinFlow.</em>
            </span>
        </p>

        <p class="clear">
            <label for="unencrypted_file" style="width: 40%;">  <input type="radio" name="encrypted" id="unencrypted_file" value="0"/> Fi&#351;ier xml original, f&#259;r&#259; criptare  </label>
            <br class="clear"/>
            <span class="details">
                <em>
                    Un fi&#351;ier xml ne-criptat, este compatibil cu oricare instalare de FinFlow versiunea <?php echo FN_VERSION; ?>, sau cu alte aplica&#355;ii care sunt capabile s&#259; prelucreze date in format xml,
                    ins&#259; nu ofer&#259; protec&#355;ie datelor.
                </em>
            </span>
        </p>

        <br class="clear"/>

        <p class="submit clear">
            <input type="hidden" name="export" value="1"/>
            <button type="submit" class="btn btn-primary">Genereaz&#259;</button>
        </p>

    </form>
<?php else: ?>
    <p class="msg note">
        Fi&#351;ierul export a fost generat cu succes.
        <strong>
            <a href="<?php echo fn_UI::get_file_download_url($exportFile, 1); ?>" onclick="setTimeout(function(){window.location.reload();}, 1200);">
                Descarc&#259; <?php echo basename($exportFile); ?>
            </a> .
        </strong>
        <br/><br/>
        <small><em>Fi&#351;ierul va fi eliminat automat de pe server dup&#259; desc&#259;rcare.</em></small>
    </p>
<?php endif; ?>