<?php
/**
 * This file will prepare a demo account for the application, with the given username and password.
 * The username, password and a session encrypted string has to be sent via $_COOKIE array
 */

include_once 'inc/init.php';
include_once 'inc/class.gcrypt.php';

$enabled = TRUE;

if($enabled ){

    $errors = array();

    if( isset($_COOKIE['fn_demo_user']) and isset($_COOKIE['fn_demo_pass']) and strlen($_COOKIE['fn_demo_user']) and strlen($_COOKIE['fn_demo_pass']) ){

        $session = trim(urldecode( $_COOKIE['fn_demo_sess'] ));

        if( $session ){

            $ip_addr = $_SERVER['REMOTE_ADDR'];

            $username = trim($_COOKIE['fn_demo_user']);
            $password = trim($_COOKIE['fn_demo_pass']);
            $session    = fn_gCrypt::decrypt($session);

            $session_validation_str = ( $password . "+" . $ip_addr . "+" . date('Y-n-j G') ); //validate session by password sent, ip address and hour

            if( $session == $session_validation_str ){

                //--- setup demo session here ---//

                $Users = fn_User::get_all(0, 999);  if( count($Users) ) foreach($Users as $user) fn_User::delete( $user->user_id );

                $saved = fn_User::add(array('email'=>$username, 'password'=>$password));

                if($saved) {

                    fn_User::authenticate($username, $password);

                    if( !isset( $_SESSION['fn_demo_start'] ) ) $_SESSION['fn_demo_start'] = time();

                    $demo_started = true;
                }

                //--- setup demo session here ---//

            }
            else fn_UI::fatal_error('Oups! Codul de validare a sesiunii demo a expirat. Pentru o noua sesiune mergi la <a href="http://finflow.org/#demo">finflow.org/#demo</a>.');

        }
        else fn_UI::fatal_error("Oups! Codul de validare a sesiunii demo este invalid.");

    }
    else fn_UI::fatal_error("Oups! Pentru pregatirea unei sesiuni demo ai nevoie de un email de utilizator si o parola.");

}

?>
<!DOCTYPE html>
<html charset="utf-8">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title> FinFlow | Pregatire demo...</title>
    <meta name="generator" content="FinFlow <?php echo FN_VERSION; ?>" />
    <meta id="view" name="viewport" content="width=device-width, maximum-scale=1.0, minimum-scale=1.0, initial-scale=1" />
    <link rel="shortcut icon" href="<?php echo FN_URL; ?>/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/bootstrap-responsive.min.css" />
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/style.css" />
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo FN_URL; ?>/js/bootstrap.min.js"></script>
</head>
<body id="page-<?php echo strtolower($_GET['p']); ?>" class="">
<div class="container">

<div class="row content">
    <div class="span2">&nbsp;</div>

    <div class="span8">

        <div class="inner-container container-login">
            <h2 align="center" class="brand">FinFlow</h2>

                    <?php if( $demo_started ): ?>

                        <p style="text-align: center; font-size: 110%;">

                            Sesiunea ta demo a fost pregatit&#259;

                            <br class="clear"/><br/>

                            <span class="counter" style="font-size: 150%;">00:59:59</span>

                            <br class="clear"/><br/>

                            <a class="btn btn-primary" href="index.php">Start <span class="icon-play"></span> </a>

                        </p>

                        <script type="text/javascript">
                            $(document).ready(function(){
                                var min = 59; var sec = 59; var ctime=1;  setInterval(function(){ ctime++; if(ctime > 3600 ){ $('.counter').text('00:00:00'); return 1; }; sec = ( sec <= 0 ) ? 59 : sec-1; min = ( sec <= 0 ) ? min-1 : min; $('.counter').text('00:' + ( min < 10 ? '0'+min : min ) + ':' + ( sec < 10 ? '0'+ sec : sec )); }, 1000);
                            });
                        </script>

                    <?php else: ?>
                        <p class="msg warning">
                            Sesiunea demo nu a putut fi pregatita pe moment. <a href="http://finflow.org/#demo">Incearc&#259; o sesiune nou&#259; &rarr;</a>.
                        </p>
                    <?php endif; ?>

        </div>
    </div>
    <div class="span2">&nbsp;</div>
</div>

</body>
</html>
