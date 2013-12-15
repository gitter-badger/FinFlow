<?php
/**
 * Offers a file for download
 */

include_once '../inc/init.php';

if ( !fn_User::is_authenticated() ) exit();

$file       = @urldecode($_GET['file']);
$remove = isset($_GET['del']) ? TRUE : FALSE;

if( $file ) {

    $path = str_replace(FNPATH, "", $file);
    $path = ( FNPATH . DIRECTORY_SEPARATOR . trim($path, DIRECTORY_SEPARATOR) );

    if ( is_file($path) ) {

        $download = isset($_GET['name']) ? urldecode($_GET['name']) : basename($path);
        $size	  	    = @filesize($path);

        header("Content-length: $size");
        header("Content-disposition: attachment; filename=$download");

        if ( @readfile($path) ) {

            if( $remove ) @unlink($path);

            exit();
        }
        else fn_UI::fatal_error("Fi&#351;ierul " . basename($path) . " nu poate fi accesat pentru citire.");

    }
    else fn_UI::fatal_error("Fi&#351;ierul " . basename($file) . " nu poate fi g&#259;sit.");

}
else fn_UI::fatal_error("Fi&#351;ierul " . basename($file) . " nu poate fi g&#259;sit.");

?>