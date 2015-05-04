<?php

if( !defined('FNPATH') ) exit;

use FinFlow\UI;
use FinFlow\Label;
use FinFlow\CanValidate;

global $fndb, $fnsql;

$_section = ( $s = url_part(2) ) ? $s : 'list';
$errors   = $warnings = $notices = array();

if ( isset($_POST['title']) ){

	//--- add a new label ---//
	
	$slug = Label::get_slug($_POST['title']);
	
	if ( ! CanValidate::stringlen($_POST['title']) )
		$errors[] = "Numele etichetei lipseste.";
	
	//--- check for unique slug ---//
	$Label = Label::get($slug);
	
	if ( count($Label) and isset($Label->label_id) )
		$errors[] = "Exista deja o eticheta cu acelas slug/nume.";
	//--- check for unique slug ---//
	
	if ( empty($errors) ){

		$saved = Label::add(post('title'), post('description'), post('parent_id'));
		
		if ( $saved )
			$notices[] = "Eticheta a fost adaugata";
		else
			$errors[] = "Eroare SQL: {$fndb->error}";
	}
	
	//--- add a new label ---//
}

if ( isset($_GET['del']) )
	Label::remove($_GET['del']);


$per_page= FN_RESULTS_PER_PAGE;
$offset  = isset($_GET['pag']) ? UI::pagination_get_current_offset($_GET['pag'], $per_page) : 0;

$activetab            = array();
$activetab[$_section] = 'active';

$Labels = Label::get_all($offset, $per_page); ?>

<div class="row">
	<div class="col-lg-8 col-md-8">
		
		<ul class="nav nav-justified nav-pills nav-page-menu" role="tablist">
			<li class="<?php echo av($activetab, 'list'); ?>">
				<a href="<?php UI::url('labels')?>">Lista</a>
			</li>
			<li class="separator"></li>
			<li class="<?php echo av($activetab, 'add'); ?>">
				<a href="<?php UI::url('labels/add')?>"> Adaug&#259; </a>
			</li>
		</ul>
		
		<?php if ( $_section == 'list' ): ?>

			<?php if (count($Labels) ) : $k=0; ?>
                <div class="panel panel-default">
                    <table class="table table-striped table-responsive list labels">
                        <tr>
                            <th>Eticheta</th>
                            <th>Slug</th>
                            <th>Tranzac&#355;ii</th>
                            <th>&nbsp;</th>
                        </tr>
                        <?php foreach ($Labels as $label):  ?>
                        <tr>
                            <td>
                                <a href="<?php UI::url('transactions', array('labels'=>$label->label_id, 'sdate'=>'1970-01-01')); ?>" title="vezi toate tranzactiile cu aceasta eticheta">
                                    <?php echo UI::esc_html($label->title); ?>
                                </a>
                            </td>
                            <td><?php echo $label->slug; ?></td>
                            <td><?php echo Label::get_ops_count($label->label_id); ?></td>
                            <td class="align-center">
                                <button class="btn btn-default" onclick="confirm_delete('<?php UI::page_url('labels', array('del'=>$label->label_id)); ?>')">
                                    <span class="fa fa-remove"></span>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>

                </div>

                <div class="pagination-bottom">
                <?php $total = Label::get_total(); if ( $total > $per_page ):?>
                    <ul class="pagination"><?php UI::pagination($total, $per_page, $offset, UI::url('labels', array(), FALSE)); ?></ul>
                <?php endif;?>
            </div>

			<?php else: ?>
				<?php UI::msg(sprintf("Nu sunt etichete predefinite! <a href=\"%s\">Adaug&#259; &rarr;</a>.", UI::page_url('labels', array('t'=>'add'), FALSE)), UI::MSG_WARN); ?>
			<?php endif;?>
		<?php endif;?>
		
		<?php

			if ( $_section == 'add' )
				include_once 'labels-add.php';

		?>
		
	</div>

	<?php UI::component('main/sidebar'); ?>
	
</div>
