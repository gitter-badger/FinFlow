<?php if( !defined('FNPATH') ) exit; $Parents = fn_Label::get_parents(); ?>

<?php fn_UI::show_errors($errors); fn_UI::show_notes($notices); ?>

<form action="<?php fn_UI::page_url('labels', array('t'=>'add'))?>" method="post" name="add-label-form" id="addLabelForm">
    <p>
        <label for="title">Nume:</label>
        <input type="text" size="45" maxlength="255" name="title" id="title" value="<?php echo fn_UI::extract_post_val('title'); ?>" />
        <span class="required">*</span>
    </p>
    <p>
        <label for="parent_id">P&#259;rinte:</label>
        <select name="parent_id" id="parent_id">
            <option value="0">- fara - </option>
            <?php if( count($Parents) ) foreach($Parents as $parent): ?>
                <option value="<?php echo $parent->label_id; ?>" <?php echo fn_UI::selected_or_not($parent->label_id, $_POST['parent_id']) ?>>
                    <?php echo fn_UI::esc_html($parent->title); ?>
                </option>
            <?php endforeach; ?>
        </select>
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