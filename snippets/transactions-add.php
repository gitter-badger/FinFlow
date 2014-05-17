<?php if ( !defined('FNPATH') ) exit(); if( !isset($_POST['date']) ) $_POST['date'] = date('Y-m-d'); ?>

<?php fn_UI::show_errors($errors); fn_UI::show_notes($notices); fn_UI::show_warnings($warnings); ?>

<form action="<?php fn_UI::page_url('transactions', array('t'=>'add')); ?>" method="post" name="add-transaction-form" id="addTransactionForm" enctype="multipart/form-data">

    <p>
        <label for="date">Data:</label>
        <input type="text" size="45" maxlength="255" name="date" id="date" value="<?php echo fn_UI::extract_post_val('date'); ?>" />
    </p>

    <p>
        <label for="optype">Tip:</label>
        <select name="optype" id="optype" style="float: left;">
            <option value="<?php echo FN_OP_IN; ?>" <?php echo fn_UI::selected_or_not(FN_OP_IN, $_POST['optype']); ?>>venit</option>
            <option value="<?php echo FN_OP_OUT; ?>" <?php echo fn_UI::selected_or_not(FN_OP_OUT, $_POST['optype']); ?>>cheltuial&#259;</option>
        </select>
        <label for="add_pending" class="wrap-input wrap-last" style="margin-left: 20px;">
            <input type="checkbox" name="add_pending" id="add_pending" /> &#238;n a&#351;teptare
        </label>
        <br class="clear"/>
    </p>

    <p class="recurring-choices">
        <label for="value">Se repet&#259;:</label>
        <select name="recurring" id="recurring">
            <option value="no">nu</option>
            <option value="daily">zilnic</option>
            <option value="monthly">lunar</option>
            <option value="yearly">anual</option>
        </select>
    </p>

    <p>
        <label for="value">Valoare:</label>
        <input type="number" step="any" size="45" maxlength="255" name="value" id="value" value="<?php echo fn_UI::extract_post_val('value'); ?>" />
    </p>

    <p>
        <label for="currency_id">Moneda:</label>
        <?php $Currencies = fn_Currency::get_all(FALSE); if ( count($Currencies) ):?>
            <select name="currency_id" id="currency_id">
                <?php foreach ($Currencies as $currency): ?>
                    <option value="<?php echo $currency->currency_id; ?>" <?php echo fn_UI::selected_or_not($currency->currency_id, $_POST['currency_id']); ?>>
                        <?php echo $currency->ccode; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endif;?>
    </p>

    <?php $Contacts = fn_Contacts::get_all(0, 999); if ( count($Contacts) ):?>
    <p>
        <label for="contact_id">Contact:</label>
        <select name="contact_id" id="contact_id">
            <?php foreach ($Contacts as $contact): ?>
                <option value="<?php echo $contact->contact_id; ?>" <?php echo fn_UI::selected_or_not($contact->contact_id, $_POST['contact_id']); ?>>
                    <?php echo fn_UI::esc_html("{$contact->first_name} {$contact->last_name} ({$contact->organization})"); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <?php endif;?>


    <p>
        <?php $Labels = fn_Label::get_parents(); if ( count($Labels) ): ?>
        <label for="labels">Etichete:</label>
            <select name="labels[]" id="labels" size="10" multiple="multiple">
                <?php foreach ($Labels as $label): $ChildrenLabels = fn_Label::get_children($label->label_id); ?>

                    <option value="<?php echo $label->label_id; ?>" <?php echo fn_UI::selected_or_not($label->label_id, $_POST['labels']); ?>>
                        <?php echo fn_UI::esc_html( $label->title ); ?>
                    </option>

                    <?php if( count($ChildrenLabels) ) foreach($ChildrenLabels as $child): ?>
                        <option value="<?php echo $child->label_id; ?>" <?php echo fn_UI::selected_or_not($child->label_id, $_POST['labels']); ?>>
                            &#150; <?php echo fn_UI::esc_html( $child->title ); ?>
                        </option>
                    <?php endforeach; ?>

                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <a href="<?php fn_UI::page_url('labels', array('t'=>'add')); ?>">Adaug&#259; &rarr;</a>
        <?php endif;?>
    </p>

    <?php if( fn_Accounts::has_accounts() ): ?>
    <p>
        <label for="labels">Cont:</label>
        <?php $Accounts = fn_Accounts::get_all(0, 999); ?>
            <select name="account_id" id="account_id">
                <option value="0">- neasociat -</option>
                <?php foreach ($Accounts as $account): ?>
                    <option value="<?php echo $account->account_id; ?>" <?php echo fn_UI::selected_or_not($account->account_id, $_POST['account_id']); ?>>
                        <?php echo fn_UI::esc_html($account->holder_name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
    </p>
    <?php endif;?>

    <p>
        <label for="comments">Comentarii:</label>
        <input type="text" size="45" maxlength="255" name="comments" id="comments" value="<?php echo fn_UI::extract_post_val('comments'); ?>" />
    </p>

    <p>

        <label for="attachment_1" style="height: 95px;">Fi&#351;iere:</label>
        <input type="file" size="45" maxlength="255" data-alerts="a1" name="attachment_1" onchange="fn_check_safe_browser_file(this);" id="attachment_1" value="" />
        &nbsp;&nbsp;&nbsp;
        <span class="unsafe-file-warn alert a1">&larr; Tipul de fi&#351;ier ales nu are suport nativ in navigatoarele web.</span>
        <span class="toobig-file-warn alert a1">&larr; Fi&#351;ierul <span class="filename"></span> ales este prea mare pentru a fi &#238;ncarc&#259;t.</span>
        <br/>

        <input type="file" size="45" maxlength="255" data-alerts="a2" name="attachment_2" onchange="fn_check_safe_browser_file(this);" id="attachment_2" value="" />
        &nbsp;&nbsp;&nbsp;
        <span class="unsafe-file-warn alert a2">&larr; Tipul de fi&#351;ier ales nu are suport nativ in navigatoarele web.</span>
        <span class="toobig-file-warn alert a2">&larr; Fi&#351;ierul <span class="filename"></span> ales este prea mare pentru a fi &#238;ncarc&#259;t.</span>
        <br/>

        <input type="file" size="45" maxlength="255" data-alerts="a3" name="attachment_3" onchange="fn_check_safe_browser_file(this);" id="attachment_3" value="" />
        &nbsp;&nbsp;&nbsp;
        <span class="unsafe-file-warn alert a3">&larr; Tipul de fi&#351;ier ales nu are suport nativ in navigatoarele web.</span>
        <span class="toobig-file-warn alert a3">&larr; Fi&#351;ierul <span class="filename"></span> ales este prea mare pentru a fi &#238;ncarc&#259;t.</span>

    </p>

    <br class="clear"/>

    <p>
        <input type="hidden" name="add" value="yes" />
        <button class="btn btn-primary" type="submit">Adaug&#259;</button>
    </p>
</form>

<script type="text/javascript" src="<?php echo FN_URL; ?>/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo FN_URL; ?>/js/moment-ro.js"></script>
<script type="text/javascript" src="<?php echo FN_URL; ?>/js/pikaday.js"></script>
<script type="text/javascript">

    max_filesize = parseInt('<?php echo fn_Util::get_max_upload_filesize(); ?>');

    var today  = new Date();
    var picker = new Pikaday({
                                 field: document.getElementById('date'),
                                 firstDay: 1,
                                 i18n: PikadayRO,
                                 format: 'YYYY-MM-DD',
                                 onSelect: function(date) {
                                     if( date > today ) {
                                         //---- is a pending transaction ---//
                                         $('#add_pending').prop('checked', true); $('.recurring-choices').slideDown('fast');
                                         //---- is a pending transaction ---//
                                     }
                                 }
     });

    $(document).ready(function($){
       $('#add_pending').click(function(){
           if( $(this).is(':checked') ) $('.recurring-choices').slideDown('fast'); else $('.recurring-choices').slideUp('fast');
       });
    });

</script>