<?php
/**
 * Previews and allows download of files
 */

include_once '../inc/init.php';

if ( !fn_User::is_authenticated() ) exit();

$file = @urldecode($_GET['file']); $exists = false;

if( $file ){

    $path = fn_Util::get_file_path($file);
    $url    = fn_UI::get_file_url($file, false);

    $exists = @file_exists( $path );

    if( isset($_GET['delete']) ) { @unlink($path); $exists = false; };
}

if( $exists ){

    $deleteurl = fn_UI::get_file_preview_url($file, array('delete'=>1));
    $fileinfo = array();

    //--- get file info ---//
    $fileinfo = @pathinfo($path);

    $filename = isset($_GET['name']) ? urldecode($_GET['name']) : basename($path);

    $fileinfo['mdate'] = fn_UI::translate_date( @date(FN_DATETIME_FORMAT, @filemtime($path)) );
    $fileinfo['size']    = fn_Util::fmt_filesize(@filesize($path));
    //--- get file info ---//


    //--- get file download URL ---//
    $download_url = fn_UI::get_file_download_url($file, 0, $filename);
    //--- get file download URL ---//

}

$backURL = ( isset($_SERVER['HTTP_REFERER']) and strlen($_SERVER['HTTP_REFERER']) ) ? $_SERVER['HTTP_REFERER'] : "#";
$backJS   = ( $backURL != '#' ) ? 'return true;' : 'window.history.back();return false;';

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>FinFlow | Vizualizare fi&#351;ier</title>
<link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('/styles/bootstrap.min.css');; ?>" />
<link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('styles/style.css'); ?>" />
<link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('styles/font-awesome.min.css'); ?>" />
</head>
<body id="page-file-preview" class="file-preview" role="document">

    <?php if ( $exists ): ?>

        <div class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand">Fisier: <?php echo $filename;?></a>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="<?php echo $backURL; ?>" onclick="<?php echo $backJS; ?>" title="Inapoi"><span class="fa fa-arrow-left"></span></a> </li>
                        <li><a href="#info" onclick="jQuery('#ajaxModalInfo').modal(true);" title="Detalii fisier"><span class="fa fa-info"></span></a></li>
                        <li><a href="<?php echo $download_url; ?>" title="Descarca"><span class="fa fa-download"></span></a> </li>
                        <li><a href="#remove" onclick="confirm_delete('<?php echo $deleteurl; ?>');" title="Sterge"><span class="fa fa-remove"></span></a> </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Ajax File Details Modal -->
        <div id="ajaxModalInfo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                        <h4 class="myriad"> Informa&#355;ii fi&#351;ier</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <tr style="border: none;">
                                <td style="border: none;">Nume:</td>
                                <td style="border: none;"><?php echo $filename; ?></td>
                            </tr>
                            <tr>
                                <td>Director:</td>
                                <td><?php echo $fileinfo['dirname']; ?></td>
                            </tr>
                            <tr>
                                <td>Extensie:</td>
                                <td>
                                    <a href="http://www.fileinfo.com/extension/<?php echo strtolower( $fileinfo['extension'] ); ?>" title="informatii extensie" target="_blank">
                                        <?php echo $fileinfo['extension']; ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>Data ultimei modific&#259;ri:</td>
                                <td><?php echo $fileinfo['mdate']; ?></td>
                            </tr>
                            <tr>
                                <td>M&#259;rime:</td>
                                <td><?php echo $fileinfo['size']; ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <div class="pull-left">
                            <button class="btn" aria-hidden="true"><i class="fa fa-download"></i> Descarca</button>
                            <button class="btn" aria-hidden="true"><i class="fa fa-remove"></i> Sterge</button>
                        </div>

                        <button class="btn" data-dismiss="modal" aria-hidden="true">&#206;nchide</button>

                    </div>
                </div>
            </div>
        </div>
        <!-- Ajax File Details Modal -->

        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default"><?php fn_UI::html_embed_file($url, $filename, $download_url); ?></div>
                </div>
            </div>
        </div>

    <?php else: ?>

    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-warning" style="margin: 25px;">
                    <a href="<?php echo $backURL; ?>" onclick="<?php echo $backJS; ?>" title="Inapoi"><span class="icon-arrow-left"></span> &#238;napoi</a>
                    Fi&#351;ierul nu poate fi afi&#351;at. Probabil c&#259; fost &#351;ters sau nu este accesibil.
                </div>
            </div>
        </div>

    <?php endif; ?>

    <script type="text/javascript" src="<?php fn_UI::asset_url('js/jquery.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php fn_UI::asset_url('js/bootstrap.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php fn_UI::asset_url('js/fn.js'); ?>"></script>

</body>
</html>