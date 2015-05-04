<?php if( !defined('FNPATH') ) exit;

use FinFlow\UI;
use FinFlow\Label;

$Parents = Label::get_parents();

UI::show_errors($errors); UI::show_notes($notices); ?>


<div class="panel panel-default">

	<div class="panel-heading">
		<h4><i class="fa fa-plus-circle"></i> Adauga eticheta</h4>
	</div>

	<div class="panel-body form-container">

		<form class="form form-horizontal" action="<?php UI::url('labels/add'); ?>" method="post" name="add-label-form" id="addLabelForm" role="form">

		    <div class="form-group">
		        <label class="col-lg-3 control-label" for="title">Nume:</label>
		        <div class="col-lg-4">
		            <input class="form-control" type="text" size="45" maxlength="255" name="title" id="title" value="<?php echo UI::extract_post_val('title'); ?>" />
		        </div>
		    </div>


		    <div class="form-group">
		        <label class="col-lg-3 control-label" for="parent_id">P&#259;rinte:</label>
		        <div class="col-lg-4">
		            <select class="form-control" name="parent_id" id="parent_id">
		                <option value="0">- fara - </option>
		                <?php if( count($Parents) ) foreach($Parents as $parent): ?>
		                    <option value="<?php echo $parent->label_id; ?>" <?php echo UI::selected_or_not($parent->label_id, post('parent_id')); ?>>
		                        <?php echo UI::esc_html($parent->title); ?>
		                    </option>
		                <?php endforeach; ?>
		            </select>
		        </div>
		    </div>

		    <div class="form-group">
		        <label class="col-lg-3 control-label" for="description">Descriere:</label>
		        <div class="col-lg-9">
		            <textarea class="form-control" cols="45" name="description" id="description"><?php echo UI::extract_post_val('description'); ?></textarea>
		        </div>
		    </div>

		    <div class="form-group">
		        <div class="col-lg-4 col-lg-offset-5">
		            <input type="hidden" name="add" value="yes" />
		            <button class="btn btn-primary btn-submit" type="submit">Adaug&#259;</button>
		        </div>
		    </div>

		</form>

	</div>

</div>