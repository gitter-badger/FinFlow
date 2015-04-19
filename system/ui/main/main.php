<?php $page = get('p'); $page = empty($page) ? 'main/dashboard' : urldecode( $page ); ?>
<!DOCTYPE html>
<html charset="utf-8">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title> FinFlow | <?php fn_UI::page_title( $page ); ?></title>
	<meta name="generator" content="FinFlow <?php echo FN_VERSION; ?>" />
	<meta id="view" name="viewport" content="width=device-width, maximum-scale=1.0, minimum-scale=1.0, initial-scale=1"/>
	<link rel="shortcut icon" href="<?php fn_UI::asset_url('/images/favicon.png'); ?>" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('styles/bootstrap.min.css'); ?>"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('styles/font-awesome.min.css'); ?>"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('styles/style.css'); ?>"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('styles/pikaday.css'); ?>"/>
	<?php fn_UI::css(); ?>
</head>
<body id="page-<?php echo $page; ?>" class="<?php echo fn_UI::get_body_class(); ?>" role="document">

<?php if ( fn_User::is_authenticated() ) fn_UI::component('main/header'); ?>

<div class="main">

	<div class="container">
		<?php

		if ( $page )
			fn_UI::component($page);
		else
			fn_UI::component('main/dashboard');

		?>
	</div>

</div>

<?php if ( fn_User::is_authenticated() ) fn_UI::component('main/footer'); ?>


<!--- debug output --->
<?php if( ( defined('FN_DEBUG') and FN_DEBUG ) or fn_Util::is_development_environment() ) fn_Log::display(); ?>
<!--- debug output --->

<script type="text/javascript" src="<?php fn_UI::asset_url('js/jquery.min.js'); ?>"></script>
<script type="text/javascript" src="<?php fn_UI::asset_url('js/bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php fn_UI::asset_url('js/fn.js'); ?>"></script>

<?php fn_UI::js(); ?>

</body>
</html>