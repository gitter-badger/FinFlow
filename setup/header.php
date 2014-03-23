<?php
/**
 * FinFlow 1.0 - Setup header file
 * @author Adrian S. (https://www.amsquared.co.uk/)
 * @version 1.0
 */

define('FN_IS_INSTALLING', true);

include_once '../inc/init.php';
include_once ( FNPATH . '/inc/class.installer.php' );
include_once ( FNPATH . '/inc/class.upgrade.php' );

if( !fn_Installer::is_valid_session_ip() )
    fn_UI::fatal_error("Accesul la aceasta pagin&#259; a fost restric&#355;ionat.");

?>
<!DOCTYPE html>
<html charset="utf-8">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title> FinFlow | Instalare</title>
    <meta name="description" content="" />
    <meta name="author" content="Adrian7 (http://adrian.silimon.eu)" />
    <meta id="view" name="viewport" content="width=device-width, maximum-scale=1.0, minimum-scale=1.0, initial-scale=1" />
    <link rel="shortcut icon" href="<?php echo FN_URL; ?>/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/bootstrap-responsive.min.css" />
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/style.css" />
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo FN_URL; ?>/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo FN_URL; ?>/js/fn.js"></script>
    <style type="text/css" media="all">label{ width: 28%; } .inner-container{ width: 70%; }</style>
</head>
<body id="page-setup" class="finflow-setup">
<div class="container">

    <div class="row content">
        <div class="span2">&nbsp;</div>

        <div class="span8">

            <div class="inner-container container-login">
