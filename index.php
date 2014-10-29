<?php include_once 'inc/init.php'; if( empty($_GET['p']) ) $_GET['p'] = 'dashboard';  ?>
<!DOCTYPE html>
<html charset="utf-8">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<title> FinFlow | <?php fn_UI::page_title($_GET['p']); ?></title>
<meta name="generator" content="FinFlow <?php echo FN_VERSION; ?>" />
<meta id="view" name="viewport" content="width=device-width, maximum-scale=1.0, minimum-scale=1.0, initial-scale=1"/>
<link rel="shortcut icon" href="<?php fn_UI::asset_url('/images/favicon.png'); ?>" type="image/x-icon"/>
<link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('styles/bootstrap.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('styles/font-awesome.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('styles/style.css'); ?>"/>
<link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('styles/pikaday.css'); ?>"/>
<?php fn_UI::css(); ?>
</head>
<body id="page-<?php echo get('p'); ?>" class="<?php echo fn_UI::get_body_class(); ?>" role="document">

	
	<?php if ( fn_User::is_authenticated() ) include_once ( FNPATH . '/snippets/header.php' ); ?>

    <div class="main">

        <div class="container">
            <?php

                $snip = NULL;

                if ( isset($_GET['p']) ) $snip = urldecode($_GET['p']);

                if ( $snip ){
                    if ( file_exists(FNPATH . "/snippets/{$snip}.php") )
                        include_once (FNPATH . "/snippets/{$snip}.php");
                    else
                        include_once (FNPATH . "/snippets/404.php");
                }
                else
                    include_once (FNPATH . "/snippets/dashboard.php");

            ?>
        </div>

    </div>
	
	<?php if ( fn_User::is_authenticated() ) include_once (FNPATH . '/snippets/footer.php');?>


    <!--- debug output --->
    <?php if( ( defined('FN_DEBUG') and FN_DEBUG ) or fn_Util::is_development_environment() ) fn_Log::display(); ?>
    <!--- debug output --->

    <script type="text/javascript" src="<?php fn_UI::asset_url('js/jquery.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php fn_UI::asset_url('js/bootstrap.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php fn_UI::asset_url('js/fn.js'); ?>"></script>

    <?php fn_UI::js(); ?>

</body>
</html>