<?php

	use FinFlow\UI;
	use FinFlow\Util;
	use FinFlow\User;
	use FinFlow\Log;

	$component = empty($component) ? 'main/dashboard' : ( strpos($component, '/') === false ? ( $component . DIRECTORY_SEPARATOR . $component ) : $component );

?>
<!DOCTYPE html>
<html charset="utf-8">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title> FinFlow | <?php UI::page_title( $component ); ?></title>
	<meta name="generator" content="FinFlow <?php echo FN_VERSION; ?>" />
	<meta id="view" name="viewport" content="width=device-width, maximum-scale=1.0, minimum-scale=1.0, initial-scale=1"/>
	<link rel="shortcut icon" href="<?php UI::asset_url('/images/favicon.png'); ?>" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?php UI::asset_url('/assets/css/bootstrap.min.css'); ?>"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?php UI::asset_url('/assets/css/font-awesome.min.css'); ?>"/>

	<?php if( Util::is_development_environment() ): ?>
		<link rel="stylesheet/less" type="text/css" href="<?php UI::asset_url('/assets/less/styles.less'); ?>" />
		<script type="text/javascript" src="<?php UI::asset_url('/assets/js/less.min.js'); ?>"></script>
	<?php else: ?>
		<link rel="stylesheet" type="text/css" media="all" href="<?php UI::asset_url('/assets/css/styles.css'); ?>"/>
	<?php endif; ?>

	<link rel="stylesheet" type="text/css" media="all" href="<?php UI::asset_url('/assets/css/pikaday.css'); ?>"/>
	<?php UI::css(); ?>
</head>
<body id="page-<?php echo Util::xss_filter($component); ?>" class="<?php echo UI::get_body_class(); ?>" role="document">

<?php if ( User::is_authenticated() ) UI::component('main/header'); ?>

<div class="main">

	<div class="container">
		<?php UI::component( $component ); ?>
	</div>

</div>

<?php if ( User::is_authenticated() ) UI::component('main/footer'); ?>


<!--- debug output --->
<?php if( ( defined('FN_DEBUG') and FN_DEBUG ) or Util::is_development_environment() ) Log::display(); ?>
<!--- debug output --->

<script type="text/javascript" src="<?php UI::asset_url('js/jquery.min.js'); ?>"></script>
<script type="text/javascript" src="<?php UI::asset_url('js/bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php UI::asset_url('js/fn.js'); ?>"></script>

<?php UI::js(); ?>

</body>
</html>