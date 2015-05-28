<?php if ( !defined('FNPATH') ) exit();

use FinFlow\UI;

$_section = ( $s = url_part(2) ) ? $s : 'list';
$errors   = $warnings = $notices = array();

?>

<div class="row">
    <div class="col-lg-8 col-md-8">

	    <?php if ( $_section == 'list' ) : ?>
		    <div class="panel panel-default">

			    <div class="panel-heading"><h4><?php __e('Available tools'); ?></h4></div>

			    <div class="panel-body">

				    <div class="row">
					    <div class="col-lg-4">
						    <a href="<?php UI::url('tools/import'); ?>">Import</a>
					    </div>
					    <div class="col-lg-4">
						    Export
					    </div>
					    <div class="col-lg-4">
						    Backup
					    </div>
					    <div class="col-lg-4">
						    Archive
					    </div>
				    </div>


			    </div>

		    </div>
		<?php endif; ?>

	    <?php UI::component('tools/tools-' . $_section); ?>


    </div>

	<?php UI::component('main/sidebar'); ?>

</div>