<?php if ( !defined('FNPATH') ) exit(); ?>

<form class="form form-horizontal filter" action="<?php fn_UI::page_url('transactions', array('t'=>'list')); ?>" name="filter-transactions-form" id="filterTransactionsForm" method="get">

    <div class="form-group">
        <label class="control-label col-lg-3">Din: </label>
        <div class="col-lg-3">

            <?php if ( $month > 0 ): ?>
                <select class="form-control" id="sday">
                    <?php foreach ($days as $d):?>
                        <option value="<?php echo fn_Util::add_leading_zeros($d); ?>" <?php echo fn_UI::selected_or_not($d, intval($day)); ?>><?php echo $d; ?></option>
                    <?php endforeach;?>
                </select>
            <?php endif; ?>

        </div>

        <div class="col-lg-3">

            <select class="form-control" id="smonth">
                <?php  foreach ($months as $m): if ($month == $m) $selected='selected';?>
                    <option value="<?php echo fn_Util::add_leading_zeros($m);?>" <?php echo fn_UI::selected_or_not($m, $month); ?>><?php echo fn_UI::get_translated_month($m); ?></option>
                <?php endforeach;?>
            </select>

        </div>

        <div class="col-lg-3">

            <select class="form-control" id="syear">
                <?php foreach ($years as $y): ?>
                    <option value="<?php echo $y;?>" <?php echo fn_UI::selected_or_not($y, $year); ?>><?php echo $y; ?></option>
                <?php endforeach;?>
            </select>

        </div>
    </div>

    <div class="form-group">

        <label class="control-label col-lg-3">P&#226;n&#259;:</label>

        <div class="col-lg-3">
            <?php if ( $month > 0 ): if (empty($eday)) $eday = $edays[count($edays)-1]; ?>
                <select class="form-control" id="eday">
                    <?php  foreach ($edays as $d):?>
                        <option value="<?php echo fn_Util::add_leading_zeros($d); ?>" <?php echo fn_UI::selected_or_not($d, $eday); ?>><?php echo $d; ?></option>
                    <?php endforeach;?>
                </select>
            <?php endif; ?>
        </div>

        <div class="col-lg-3">

            <select class="form-control" id="emonth">
                <?php foreach ($months as $m): if ($month == $m) $selected='selected';?>
                    <option value="<?php echo fn_Util::add_leading_zeros($m); ?>" <?php echo fn_UI::selected_or_not($m, $emonth); ?>><?php echo fn_UI::get_translated_month($m); ?></option>
                <?php endforeach;?>
            </select>

        </div>

        <div class="col-lg-3">
            <select class="form-control" id="eyear">
                <?php foreach ($years as $y):?>
                    <option value="<?php echo $y;?>" <?php echo fn_UI::selected_or_not($y, $eyear); ?>><?php echo $y; ?></option>
                <?php endforeach;?>
            </select>
        </div>

    </div>

    <?php $Labels = fn_Label::get_all(0, 999); if ( count($Labels) ): ?>
    <div class="form-group">
        <label class="control-label col-lg-3" for="labels">Etichete:</label>
        <div class="col-lg-9">
            <select class="form-control" name="labels" id="labels" size="5" multiple="multiple">
                <?php foreach ($Labels as $label): ?>
                    <option value="<?php echo $label->label_id; ?>" <?php echo fn_UI::selected_or_not($label->label_id, $labels); ?>>
                        <?php echo fn_UI::esc_html($label->title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <?php endif;?>

    <?php if( fn_Accounts::has_accounts() ): ?>
    <div class="form-group">
        <label class="control-label col-lg-3" for="accounts">Conturi:</label>
        <div class="col-lg-9">
            <?php $Accounts = fn_Accounts::get_all(0, 999); if ( count($Accounts) ): ?>
                <select class="form-control" name="accounts" id="accounts" size="5" multiple="multiple">
                    <?php foreach ($Accounts as $account): ?>
                        <option value="<?php echo $account->account_id; ?>" <?php echo fn_UI::selected_or_not($account->account_id, $accounts); ?>>
                            <?php echo fn_UI::esc_html($account->holder_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif;?>
        </div>
    </div>
    <?php endif; ?>

    <div class="form-group">
        <label class="control-label col-lg-3" for="currency_id">Moneda:</label>
        <div class="col-lg-9">
            <?php $Currencies = fn_Currency::get_all(FALSE); if (count($Currencies)):?>
                <select class="form-control" name="currency_id" id="currency_id">
                    <option value="0">- toate - </option>
                    <?php foreach ($Currencies as $currency): ?>
                        <option value="<?php echo $currency->currency_id; ?>" <?php echo fn_UI::selected_or_not($currency->currency_id, $currency_id); ?>><?php echo $currency->ccode; ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif;?>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3" for="type">Tip:</label>
        <div class="col-lg-9">
            <select class="form-control" name="type" id="type">
                <option value="0">- toate - </option>
                <option value="<?php echo FN_OP_IN; ?>"  <?php echo fn_UI::selected_or_not(FN_OP_IN, $type); ?>>venit</option>
                <option value="<?php echo FN_OP_OUT; ?>" <?php echo fn_UI::selected_or_not(FN_OP_OUT, $type); ?>>cheltuieli</option>
            </select>
        </div>
    </div>
				
	<input type="hidden" name="sdate" id="sdate" value="<?php echo fn_UI::extract_get_val('sdate'); ?>" />
	<input type="hidden" name="edate" id="edate" value="<?php echo fn_UI::extract_get_val('edate'); ?>"/>

    <div class="form-group">
        <div class="col-lg-offset-5 col-lg-7">
            <button class="btn btn-primary" type="button" id="filterTransactionsBtn"> Filtreaza </button>
        </div>
   </div>

</form>
