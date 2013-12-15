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

}

$backURL = ( isset($_SERVER['HTTP_REFERER']) and strlen($_SERVER['HTTP_REFERER']) ) ? $_SERVER['HTTP_REFERER'] : "#";
$backJS   = ( $backURL != '#' ) ? 'return true;' : 'window.history.back();return false;';

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>FinFlow | Vizualizare fi&#351;ier</title>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/bootstrap-responsive.min.css" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/font-awesome.min.css" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/style.css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo FN_URL; ?>/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo FN_URL; ?>/js/fn.js"></script>
<style type="text/css">
    .navbar{
        position: fixed;
        box-shadow: 2px 2px 5px #333;
        margin-top: -46px;
    }

    .navbar .navbar-inner {
        padding: 0;
        -webkit-border-radius: 0px;
        -moz-border-radius: 0px;
        border-radius: 0px;
    }

    .navbar .nav {
        margin: 0;
        display: table;
        width: 100%;
    }
    .navbar .nav li {
        display: table-cell;
        width: 1%;
        float: none;
        font-size: 1.1em;
        font-family: 'Verdana', Arial, sans-serif;
        text-rendering: optimizelegibility;
    }
    .navbar .nav li a {
        text-align: center;
        border-left: 1px solid rgba(255,255,255,.75);
        border-right: 1px solid rgba(0,0,0,.1);
        line-height: normal;
    }
    .navbar .nav li:first-child a {
        border-left: 0;
        border-radius: 0px;
    }
    .navbar .nav li:last-child a {
        border-right: 0;
        border-radius: 0px;
    }
    .navbar .nav li:first-child a {
        border-radius: 0px;
    }
    .navbar .nav li.filename{
        font-size: 12px;
        text-align: center;
        color: #333;
        font-weight: bold;
    }

    body{
        padding-top: 45px;
        overflow: auto;
    }

    #ajaxModalInfo{ z-index: 9999; }

    iframe, .file-embed{ z-index: 1; }
    .file-embed{ float: left; width: 100%; }
</style>
</head>
<body style="background: #FFF;">

    <?php if ( $exists ): ?>
    <div class="navbar">
        <div class="navbar-inner">
            <div class="container">
                <ul class="nav">
                    <li><a href="<?php echo $backURL; ?>" onclick="<?php echo $backJS; ?>" title="Inapoi"><span class="icon-arrow-left"></span> </a> </li>
                    <li><a href="#info" onclick="$('#ajaxModalInfo').modal(true);" title="Detalii fisier"><span class="icon-info-sign"></span></a> </li>
                    <li><a href="<?php echo fn_UI::get_file_download_url($file, 0, $filename); ?>" title="Descarca"><span class="icon-download-alt"></span> </a> </li>
                    <li><a href="#remove" onclick="confirm_delete('<?php echo $deleteurl; ?>');" title="Sterge"><span class="icon-remove"></span></a> </li>
                </ul>
            </div>
        </div>
    </div>

	<?php fn_UI::html_embed_file($url, $filename); else: ?>
	<p class="msg warn" style="margin: 25px;">
        <a href="<?php echo $backURL; ?>" onclick="<?php echo $backJS; ?>" title="Inapoi"><span class="icon-arrow-left"></span> &#238;napoi</a>
        Fi&#351;ierul nu poate fi afi&#351;at. Probabil c&#259; fost &#351;ters sau nu este accesibil.
    </p>
	<?php endif; ?>

    <!-- Ajax Error Modal -->
    <div id="ajaxModalInfo" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
            <button class="btn" data-dismiss="modal" aria-hidden="true">&#206;nchide</button>
        </div>
    </div>
    <!-- Ajax Error Modal -->

</body>
</html>