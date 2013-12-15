<?php if ( !defined('FNPATH') ) exit(); ?>

<?php fn_UI::show_errors($errors); fn_UI::show_notes($notices); ?>

<form action="<?php fn_UI::page_url('accounts', array('t'=>'add'))?>" method="post" name="add-account-form" id="addAccountForm">
    <p>
        <label for="holder_name">Banca/Emitent:</label>
        <input type="text" size="45" maxlength="255" name="holder_name" id="holder_name" list="listHolders"  value="<?php echo fn_UI::extract_post_val('holder_name', "", TRUE); ?>" />
        <span class="required">*</span>

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

    </p>

    <p>
        <label for="account_currency_id">Moneda:</label>
        <select name="account_currency_id" id="account_currency_id">
            <?php foreach ($Currencies as $currency): ?>
                <option value="<?php echo $currency->currency_id; ?>" <?php echo fn_UI::selected_or_not($currency->currency_id, $_POST['account_currency_id']); ?>>
                    <?php echo $currency->ccode; ?>
                </option>
            <?php endforeach;?>
        </select>
    </p>

    <p>
        <label for="holder_swift">Cod SWIFT:</label>
        <input type="text" size="45" maxlength="255" name="holder_swift" id="holder_swift"  value="<?php echo fn_UI::extract_post_val('holder_swift', "", TRUE); ?>" />
    </p>

    <p>
        <label for="account_iban">IBAN:</label>
        <input type="text" size="45" maxlength="255" name="account_iban" id="account_iban"  value="<?php echo fn_UI::extract_post_val('account_iban', "", TRUE); ?>" />
    </p>

    <p>
        <label for="balance">Balan&#355;a:</label>
        <input type="number" size="45" maxlength="255" placeholder="100000" name="balance" id="balance"  value="<?php echo fn_UI::extract_post_val('balance', "", TRUE); ?>" />

        <span class="details"> &nbsp;Suma disponibil&#259; in cont. </span>

    </p>

    <p>
        <button class="btn btn-primary" type="submit">Adaug&#259;</button>
    </p>
</form>