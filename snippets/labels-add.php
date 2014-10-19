<?php if( !defined('FNPATH') ) exit; $Parents = fn_Label::get_parents(); fn_UI::show_errors($errors); fn_UI::show_notes($notices); ?>

<form class="form form-horizontal" action="<?php fn_UI::page_url('labels', array('t'=>'add')); ?>" method="post" name="add-label-form" id="addLabelForm" role="form">

    <div class="form-group">
        <label class="col-lg-3 control-label" for="title">Nume:</label>
        <div class="col-lg-4">
            <input class="form-control" type="text" size="45" maxlength="255" name="title" id="title" value="<?php echo fn_UI::extract_post_val('title'); ?>" />
        </div>
    </div>


    <div class="form-group">
        <label class="col-lg-3 control-label" for="parent_id">P&#259;rinte:</label>
        <div class="col-lg-4">
            <select class="form-control" name="parent_id" id="parent_id">
                <option value="0">- fara - </option>
                <?php if( count($Parents) ) foreach($Parents as $parent): ?>
                    <option value="<?php echo $parent->label_id; ?>" <?php echo fn_UI::selected_or_not($parent->label_id, $_POST['parent_id']) ?>>
                        <?php echo fn_UI::esc_html($parent->title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg-3 control-label" for="description">Descriere:</label>
        <div class="col-lg-4">
            <input class="form-control" type="text" size="45" maxlength="255" name="description" id="description" value="<?php echo fn_UI::extract_post_val('description'); ?>" />
        </div>
    </div>

    <div class="form-group">
        <div class="col-lg-4 col-lg-offset-5">
            <input type="hidden" name="add" value="yes" />
            <button class="btn btn-primary" type="submit">Adaug&#259;</button>
        </div>
    </div>

</form>