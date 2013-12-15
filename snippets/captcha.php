<?php

include_once '../inc/init.php';
include_once ( FNPATH . '/inc/class.captcha.php' );

$maxtries = 999;
$size        = isset($_GET['mag']) ? array(500, 240, 77) : array(100, 30, 12); list($width, $height, $fontsize) = $size;

$_SESSION['captcha_tries'] = intval($_SESSION['captcha_tries']) +1;

//--- limit the number of captcha tries for current session ---//
if( $_SESSION['captcha_tries'] > $maxtries ) exit;
//--- limit the number of captcha tries for current session ---//


function output_captcha($width, $height, $fontsize){

    if( fn_Captcha::supports_img() )
        fn_Captcha::output_img($width, $height, array('r'=>0, 'g'=>0, 'b'=>0), $fontsize);
    else
        fn_Captcha::output_math();

}


if( fn_Captcha::is_initialized() ){

    if( $_GET['mag'] ){
        if( isset($_GET['htmlmag']) ){ ?>
            <html>
                <link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/bootstrap.min.css" />
                <link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/bootstrap-responsive.min.css" />
                <link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/font-awesome.min.css" />
                <style type="text/css">
                    img{
                        background: #ffffff; /* Old browsers */
                        /* IE9 SVG, needs conditional override of 'filter' to 'none' */
                        background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2ZmZmZmZiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjQ3JSIgc3RvcC1jb2xvcj0iI2YzZjNmMyIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjYyJSIgc3RvcC1jb2xvcj0iI2VkZWRlZCIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNmZmZmZmYiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
                        background: -moz-linear-gradient(top,  #ffffff 0%, #f3f3f3 47%, #ededed 62%, #ffffff 100%); /* FF3.6+ */
                        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(47%,#f3f3f3), color-stop(62%,#ededed), color-stop(100%,#ffffff)); /* Chrome,Safari4+ */
                        background: -webkit-linear-gradient(top,  #ffffff 0%,#f3f3f3 47%,#ededed 62%,#ffffff 100%); /* Chrome10+,Safari5.1+ */
                        background: -o-linear-gradient(top,  #ffffff 0%,#f3f3f3 47%,#ededed 62%,#ffffff 100%); /* Opera 11.10+ */
                        background: -ms-linear-gradient(top,  #ffffff 0%,#f3f3f3 47%,#ededed 62%,#ffffff 100%); /* IE10+ */
                        background: linear-gradient(to bottom,  #ffffff 0%,#f3f3f3 47%,#ededed 62%,#ffffff 100%); /* W3C */
                        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ffffff',GradientType=0 ); /* IE6-8 */
                        border: 1px #eeeeee solid;

                        box-shadow: inset 2px 2px 4px #999999;
                    }

                    button{
                        font-size: 22px;
                        height: 40px;
                    }

                </style>
                <body style="padding: 0px; margin: 0px; background: #fbfaf9 ;">
                    <div style="margin-right: 10px; padding: 10px; clear: both;">
                        <button class="btn pull-right" onclick="window.close();"> <span class="icon-remove"></span> </button>
                    </div>

                    <div style="margin: auto; text-align: center; padding-top: 50px;">
                        <img style="border-radius: 1em;" src="<?php echo (FN_URL . '/snippets/captcha.php?o=1&mag=1'); ?>"/>
                    </div>

                    <div style="margin-top: 20px; text-align: center;">
                        <p>
                            <button class="btn" onclick="window.location.reload();"> <span class="icon-refresh"></span> Re&#238;ncarc&#259; </button>
                        </p>
                    </div>
                </body>
            </html>
        <?php
        }
        else output_captcha($width, $height, $fontsize);
    }
    else output_captcha($width, $height, $fontsize);

}
else fn_UI::fatal_error("Codul captcha nu a fost ini&#355;ializat.", true, true, fn_UI::$MSG_WARN, 'Aten&#355;ie:', 'Aten&#355;ie');