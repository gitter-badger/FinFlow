<?php

if( !defined('FNPATH') ) exit;

global $fndb, $fnsql;

$errors 	= array();
$notices = array();

if ( isset($_POST['title']) and count($_POST['title']) ){
	//--- add a new label ---//
	
	$slug = fn_Label::get_slug($_POST['title']);
	
	if ( !fn_CheckValidityOf::stringlen($_POST['title']) ) $errors[] = "Numele etichetei lipseste.";
	
	//--- check for unique slug ---//
	$Label = fn_Label::get($slug);
	
	if ( count($Label) and isset($Label->label_id)) $errors[] = "Exista deja o eticheta cu acelas slug/nume.";
	//--- check for unique slug ---//
	
	if ( empty($errors) ){

		$saved = fn_Label::add($_POST['title'], $_POST['description'], $_POST['parent_id']);
		
		if ( $saved )
			$notices[] = "Eticheta a fost adaugata";
		else
			$errors[] = "Eroare SQL: {$fndb->error}";
	}
	
	//--- add a new label ---//
}

if ( isset($_GET['del']) ) fn_Label::remove($_GET['del']);

$per_page= FN_RESULTS_PER_PAGE;
$offset     = isset($_GET['pag']) ? fn_UI::pagination_get_current_offset($_GET['pag'], $per_page) : 0;

$Labels = fn_Label::get_all($offset, $per_page); ?>

<div class="row">
	<div class="<?php fn_UI::main_container_grid_class(); ?>">
		
		<?php $tab = isset($_GET['t']) ? urldecode($_GET['t']) : 'list'; $activetab = array(); $activetab[$tab] = 'active'; ?>
		
		<ul class="nav nav-tabs">
			<li class="<?php echo $activetab['list']; ?>"><a href="<?php fn_UI::page_url('labels', array('t'=>'list'))?>">Lista</a></li>
			<li class="<?php echo $activetab['add']; ?>"><a href="<?php fn_UI::page_url('labels', array('t'=>'add'))?>"> Adaug&#259; </a></li>
		</ul>
		
		<?php if ( $tab == 'list' ): ?>
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
                                <a href="<?php fn_UI::page_url('transactions', array('labels'=>$label->label_id, 'sdate'=>'1970-01-01')); ?>" title="vezi toate tranzactiile cu aceasta eticheta">
                                    <?php echo fn_UI::esc_html($label->title); ?>
                                </a>
                            </td>
                            <td><?php echo $label->slug; ?></td>
                            <td><?php echo fn_Label::get_ops_count($label->label_id); ?></td>
                            <td class="align-center">
                                <button class="btn btn-default" onclick="confirm_delete('<?php fn_UI::page_url('labels', array('del'=>$label->label_id)); ?>')">
                                    <span class="fa fa-remove"></span>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>

                </div>

                <div class="pagination-bottom">
                <?php $total = fn_Label::get_total(); if ( $total > $per_page ):?>
                    <ul class="pagination"><?php fn_UI::pagination($total, $per_page, $offset, fn_UI::page_url('labels', array(), FALSE)); ?></ul>
                <?php endif;?>
            </div>

			<?php else: ?>
				<?php fn_UI::msg(sprintf("Nu sunt etichete predefinite! <a href=\"%s\">Adaug&#259; &rarr;</a>.", fn_UI::page_url('labels', array('t'=>'add'), FALSE)), fn_UI::$MSG_WARN); ?>
			<?php endif;?>
		<?php endif;?>
		
		<?php if ( $tab == 'add' ) include_once 'labels-add.php'; ?>
		
	</div>
	
	<?php include_once ( FNPATH . '/snippets/sidebar.php' ); ?>
	
</div>
