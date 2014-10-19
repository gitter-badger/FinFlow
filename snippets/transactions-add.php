<?php if ( !defined('FNPATH') ) exit(); if( !isset($_POST['date']) ) $_POST['date'] = date('Y-m-d'); ?>

<?php fn_UI::show_errors($errors); fn_UI::show_notes($notices); fn_UI::show_warnings($warnings); ?>

<form class="form form-horizontal" action="<?php fn_UI::page_url('transactions', array('t'=>'add')); ?>" method="post" name="add-transaction-form" id="addTransactionForm" enctype="multipart/form-data">

    <div class="form-group">
        <label class="control-label col-lg-3" for="date">Data:</label>
        <div class="col-lg-3">
            <input class="form-control" type="text" size="45" maxlength="255" name="date" id="date" value="<?php echo fn_UI::extract_post_val('date'); ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3" for="optype">Tip:</label>
        <div class="col-lg-3">
            <select class="form-control" name="optype" id="optype">
                <option value="<?php echo FN_OP_IN; ?>" <?php echo fn_UI::selected_or_not(FN_OP_IN, $_POST['optype']); ?>>venit</option>
                <option value="<?php echo FN_OP_OUT; ?>" <?php echo fn_UI::selected_or_not(FN_OP_OUT, $_POST['optype']); ?>>cheltuial&#259;</option>
            </select>
        </div>
        <div class="col-lg-4">
            <div class="checkbox">
                <label class="control-label" for="add_pending">
                    <input type="checkbox" name="add_pending" id="add_pending" /> &#238;n a&#351;teptare
                </label>
            </div>
        </div>
    </div>

    <div class="form-group recurring-choices">
        <label class="control-label col-lg-3" for="value">Se repet&#259;:</label>
        <div class="col-lg-4">
            <select class="form-control" name="recurring" id="recurring">
                <option value="no">nu</option>
                <option value="daily">zilnic</option>
                <option value="monthly">lunar</option>
                <option value="yearly">anual</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3" for="value">Valoare:</label>
        <div class="col-lg-4">
            <input class="form-control" type="number" step="any" size="45" maxlength="255" name="value" id="value" value="<?php echo fn_UI::extract_post_val('value'); ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3" for="currency_id">Moneda:</label>
        <div class="col-lg-4">
            <?php $Currencies = fn_Currency::get_all(FALSE); if ( count($Currencies) ):?>
                <select class="form-control" name="currency_id" id="currency_id">
                    <?php foreach ($Currencies as $currency): ?>
                        <option value="<?php echo $currency->currency_id; ?>" <?php echo fn_UI::selected_or_not($currency->currency_id, $_POST['currency_id']); ?>>
                            <?php echo $currency->ccode; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif;?>
        </div>
    </div>

    <?php $Contacts = fn_Contacts::get_all(0, 999); if ( count($Contacts) ):?>
    <div class="form-group">
        <label class="control-label col-lg-3" for="contact_id">Contact:</label>
        <div class="col-lg-4">
            <select name="contact_id" id="contact_id">
                <?php foreach ($Contacts as $contact): ?>
                    <option value="<?php echo $contact->contact_id; ?>" <?php echo fn_UI::selected_or_not($contact->contact_id, $_POST['contact_id']); ?>>
                        <?php echo fn_UI::esc_html("{$contact->first_name} {$contact->last_name} ({$contact->organization})"); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <?php endif;?>


    <div class="form-group">
        <?php $Labels = fn_Label::get_parents(); if ( count($Labels) ): ?>
            <label class="control-label col-lg-3" for="labels">Etichete:</label>

            <div class="col-lg-4">
                <select class="form-control" name="labels[]" id="labels" size="10" multiple="multiple">
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
            </div>

        <?php else: ?>
        <div class="col-lg-6"><a href="<?php fn_UI::page_url('labels', array('t'=>'add')); ?>">Adaug&#259; &rarr;</a></div>
        <?php endif;?>
    </div>

    <?php if( fn_Accounts::has_accounts() ): ?>
    <div class="form-group">
        <label class="control-label col-lg-3" for="labels">Cont:</label>
        <div class="col-lg-4">
            <?php $Accounts = fn_Accounts::get_all(0, 999); ?>
            <select class="form-control" name="account_id" id="account_id">
                <option value="0">- neasociat -</option>
                <?php foreach ($Accounts as $account): ?>
                    <option value="<?php echo $account->account_id; ?>" <?php echo fn_UI::selected_or_not($account->account_id, $_POST['account_id']); ?>>
                        <?php echo fn_UI::esc_html($account->holder_name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <?php endif;?>

    <div class="form-group">
        <label class="control-label col-lg-3" for="comments">Comentarii:</label>
        <div class="col-lg-4">
            <input class="form-control" type="text" size="45" maxlength="255" name="comments" id="comments" value="<?php echo fn_UI::extract_post_val('comments'); ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3" for="attachment_1" style="height: 95px;">Fi&#351;iere:</label>
        <div class="col-lg-4">
            <input class="form-control" type="file" size="45" maxlength="255" data-alerts="a1" name="attachment_1" onchange="fn_check_safe_browser_file(this);" id="attachment_1" value="" />
        </div>

        <div class="col-lg-5">
            <div class="alert alert-warning a1">&larr; Tipul de fi&#351;ier ales nu are suport nativ in navigatoarele web.</div>
            <div class="alert alert-warning a1">&larr; Fi&#351;ierul <span class="filename"></span> ales este prea mare pentru a fi &#238;ncarc&#259;t.</div>
        </div>

        <div class="col-lg-4 col-lg-offset-3">
            <input class="form-control" type="file" size="45" maxlength="255" data-alerts="a2" name="attachment_2" onchange="fn_check_safe_browser_file(this);" id="attachment_2" value="" />
            &nbsp;&nbsp;&nbsp;
            <span class="unsafe-file-warn alert a2">&larr; Tipul de fi&#351;ier ales nu are suport nativ in navigatoarele web.</span>
            <span class="toobig-file-warn alert a2">&larr; Fi&#351;ierul <span class="filename"></span> ales este prea mare pentru a fi &#238;ncarc&#259;t.</span>
        </div>

        <div class="col-lg-4 col-lg-offset-3">
            <input class="form-control" type="file" size="45" maxlength="255" data-alerts="a3" name="attachment_3" onchange="fn_check_safe_browser_file(this);" id="attachment_3" value="" />
            &nbsp;&nbsp;&nbsp;
            <span class="unsafe-file-warn alert a3">&larr; Tipul de fi&#351;ier ales nu are suport nativ in navigatoarele web.</span>
            <span class="toobig-file-warn alert a3">&larr; Fi&#351;ierul <span class="filename"></span> ales este prea mare pentru a fi &#238;ncarc&#259;t.</span>
        </div>

    </div>


    <div class="form-group">
        <div class=" col-lg-5 col-lg-offset-5">
            <input type="hidden" name="add" value="yes" />
            <button class="btn btn-primary" type="submit">Adaug&#259;</button>
        </div>
    </div>

</form>

<script type="text/javascript" src="<?php fn_UI::asset_url('/js/moment.min.js'); ?>"></script>
<script type="text/javascript" src="<?php fn_UI::asset_url('/js/moment-ro.js'); ?>"></script>
<script type="text/javascript" src="<?php fn_UI::asset_url('/js/pikaday.js'); ?>"></script>
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
</script>