<?php if ( !defined('FNPATH') ) exit(); if( ! isset( $_POST['account_currency_id']) ) $_POST['account_currency_id'] = 0; ?>

<?php fn_UI::show_errors($errors); fn_UI::show_notes($notices); ?>

<form class="form form-horizontal" action="<?php fn_UI::page_url('accounts', array('t'=>'add'))?>" method="post" name="add-account-form" id="addAccountForm" role="form">

    <div class="form-group">
        <label class="control-label col-lg-3" for="holder_name">Banca/Emitent:</label>
        <div class="col-lg-4">

            <input class="form-control" type="text" size="45" maxlength="255" name="holder_name" id="holder_name" list="listHolders"  value="<?php echo fn_UI::extract_post_val('holder_name', "", TRUE); ?>" />

            <!--- A list of banks and online wallet providers --->
            <datalist id="listHolders">
                <option value="lichizi"/>
                <option value="RBS"/>
                <option value="HSBC"/>
                <option value="NationWide"/>
                <option value="Citibank"/>
                <option value="OTP Bank"/>
                <option value="Piraeus Bank"/>
                <option value="Volksbank"/>
                <option value="CEC"/>
                <option value="ING"/>
                <option value="BCR"/>
                <option value="UniCredit"/>
                <option value="Alpha Bank"/>
                <option value="PayPal"/>
                <option value="AlertPay"/>
                <option value="2Checkout"/>
                <option value="Google Wallet"/>
                <option value="Moneybookers"/>
                <option value="SagePay"/>
                <option value="Amazon Payments"/>
                <option value="Authorize.net"/>
                <option value="CCBill"/>
                <option value="World Pay"/>
                <option value="Intuit Payment"/>
                <option value="Cyber Source"/>
                <option value="PayPoint"/>
                <option value="ClickBank"/>
                <option value="Plimus"/>
            </datalist>
            <!--- A list of banks and online wallet providers --->
        </div>

   </div>

    <div class="form-group">
        <label class="control-label col-lg-3" for="account_currency_id">Moneda:</label>
        <div class="col-lg-4">
            <select class="form-control" name="account_currency_id" id="account_currency_id">
                <?php foreach ($Currencies as $currency): ?>
                    <option value="<?php echo $currency->currency_id; ?>" <?php echo fn_UI::selected_or_not($currency->currency_id, $_POST['account_currency_id']); ?>>
                        <?php echo $currency->ccode; ?>
                    </option>
                <?php endforeach;?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3" for="holder_swift">Cod SWIFT:</label>
        <div class="col-lg-4">
            <input class="form-control" type="text" size="45" maxlength="255" name="holder_swift" id="holder_swift"  value="<?php echo fn_UI::extract_post_val('holder_swift', "", TRUE); ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3" for="account_iban">IBAN:</label>
        <div class="col-lg-4">
            <input class="form-control" type="text" size="45" maxlength="255" name="account_iban" id="account_iban"  value="<?php echo fn_UI::extract_post_val('account_iban', "", TRUE); ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3" for="balance">Balan&#355;a:</label>
        <div class="col-lg-4">
            <input class="form-control" type="number" size="45" step="any" maxlength="255" placeholder="100000.45" name="balance" id="balance"  value="<?php echo fn_UI::extract_post_val('balance', "", TRUE); ?>" />
            <span class="details"> &nbsp;Suma disponibil&#259; in cont. </span>
        </div>
    </div>

    <div class="form-group">
        <div class="col-lg-4 col-lg-offset-5">
            <button class="btn btn-primary" type="submit">Adaug&#259;</button>
        </div>
    </div>

</form>