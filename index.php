<?php include_once 'inc/init.php';  ?>
<!DOCTYPE html>
<html charset="utf-8">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<title> FinFlow | <?php fn_UI::page_title($_GET['p']); ?></title>
<meta name="generator" content="FinFlow <?php echo FN_VERSION; ?>" />
<meta id="view" name="viewport" content="width=device-width, maximum-scale=1.0, minimum-scale=1.0, initial-scale=1" />
<link rel="shortcut icon" href="<?php echo FN_URL; ?>/images/favicon.png" type="image/x-icon">
<link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/bootstrap-responsive.min.css" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/font-awesome.min.css" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/style.css" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/pikaday.css" />
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo FN_URL; ?>/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo FN_URL; ?>/js/fn.js"></script>
</head>
<body id="page-<?php echo strtolower($_GET['p']); ?>" class="<?php echo fn_UI::get_body_class(); ?>">
<div class="container">
	
	<?php if ( fn_User::is_authenticated() ) include_once (FNPATH . '/snippets/header.php');?>
	
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
	
	<?php if ( fn_User::is_authenticated() ) include_once (FNPATH . '/snippets/footer.php');?>
	
</div>
</body>