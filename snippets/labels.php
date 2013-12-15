<?php

if( !defined('FNPATH') ) exit;

global $fndb, $fnsql;

if ( count($_POST['title']) ){
	//--- add a new label ---//
	
	$errors 	= array();
	$notices = array();
	
	$slug = fn_Label::get_slug($_POST['title']);
	
	if ( !fn_CheckValidityOf::stringlen($_POST['title']) ) $errors[] = "Numele etichetei lipseste.";
	
	//--- check for unique slug ---//
	$Label = fn_Label::get($slug);
	
	if ( count($Label) and isset($Label->label_id)) $errors[] = "Exista deja o eticheta cu acelas slug/nume.";
	//--- check for unique slug ---//
	
	if ( empty($errors) ){
		$saved = fn_Label::add($_POST['title'], $_POST['description']);
		
		if ( $saved )
			$notices[] = "Eticheta a fost adaugata";
		else
			$errors[] = "Eroare SQL: {$fndb->error}";
	}
	
	//--- add a new label ---//
}

if ( isset($_GET['del']) ) fn_Label::remove($_GET['del']);

$offset     = isset($_GET['pag']) ? intval($_GET['pag']) : 0;
$per_page= FN_RESULTS_PER_PAGE;

$Labels = fn_Label::get_all($offset, $per_page);

?>

<div class="row content">
	<div class="span10">
		
		<?php $tab = isset($_GET['t']) ? urldecode($_GET['t']) : 'list'; $activetab = array(); $activetab[$tab] = 'active'; ?>
		
		<ul class="nav nav-tabs">
			<li class="<?php echo $activetab['list']; ?>"><a href="<?php fn_UI::page_url('labels', array('t'=>'list'))?>">Lista</a></li>
			<li class="<?php echo $activetab['add']; ?>"><a href="<?php fn_UI::page_url('labels', array('t'=>'add'))?>"> Adaug&#259; </a></li>
		</ul>
		
		<?php if ( $tab == 'list' ): ?>
			<?php if (count($Labels) ) : $k=0; ?>
			<table class="list labels" border="1">
				<tr>
					<th>Eticheta</th>
					<th>Slug</th>
					<th>Tranzac&#355;ii</th>
					<th>&nbsp;</th>
				</tr>
				<?php foreach ($Labels as $label):  $k++; $trclass= ( $k%2 == 0) ? 'even' : 'odd'; ?>
				<tr class="<?php echo $trclass; ?>">
					<td>
                        <a href="<?php fn_UI::page_url('transactions', array('labels'=>$label->label_id, 'sdate'=>'1970-01-01')); ?>" title="vezi toate tranzactiile cu aceasta eticheta">
                            <?php echo fn_UI::esc_html($label->title); ?>
                        </a>
                    </td>
					<td><?php echo $label->slug; ?></td>
					<td><?php echo fn_Label::get_ops_count($label->label_id); ?></td>
					<td class="align-center">
						<button class="btn" onclick="confirm_delete('<?php fn_UI::page_url('labels', array('del'=>$label->label_id)); ?>')">
							<span class="icon-remove"></span>
						</button>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>

            <div class="pagination">
                <?php $total = fn_Label::get_total(); if ( $total > $per_page ):?>
                <ul><?php fn_UI::pagination($total, $per_page, $_GET['pag'], fn_UI::page_url('labels', array(), FALSE)); ?></ul>
                <?php endif;?>
            </div>

			<?php else: ?>
				<?php fn_UI::msg(sprintf("Nu sunt etichete predefinite! <a href=\"%s\">Adaug&#259; &rarr;</a>.", fn_UI::page_url('labels', array('t'=>'add'), FALSE)), fn_UI::$MSG_WARN); ?>
			<?php endif;?>
		<?php endif;?>
		
		<?php if ( $tab == 'add' ): ?>
		
			<?php fn_UI::show_errors($errors); fn_UI::show_notes($notices); ?>
		
			<form action="<?php fn_UI::page_url('labels', array('t'=>'add'))?>" method="post" name="add-label-form" id="addLabelForm">
				<p>
					<label for="title">Nume:</label>
					<input type="text" size="45" maxlength="255" name="title" id="title" value="<?php echo fn_UI::extract_post_val('title'); ?>" /> 
					<span class="required">*</span> 
				</p>
				<p>
					<label for="description">Descriere:</label>
					<input type="text" size="45" maxlength="255" name="description" id="description" value="<?php echo fn_UI::extract_post_val('description'); ?>" /> 
				</p>
				
				<p>
					<input type="hidden" name="add" value="yes" />
					<button class="btn btn-primary" type="submit">Adaug&#259;</button>
				</p>
			</form>
			
		<?php endif;?>
		
	</div>
	
	<?php include_once ( FNPATH . '/snippets/sidebar.php' ); ?>
	
</div>
