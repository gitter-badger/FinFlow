<?php if( !defined('FNPATH') ) exit;

include_once ( FNPATH . '/inc/exr-init.php' ); global $fnexr; ?>

<?php if( !$fnexr->isCacheValid() ): ?>
    <p class="msg note">Fi&#351;ierul cache-ul cu ratele de schimb trebuie actualizat. <a href="#" onclick="fn_popup('<?php echo FN_URL; ?>/inc/cron/cron.exchangerates.php');">Actualizeaz&#259; acum</a> </p>
<?php return; endif; ?>

<?php

$Currencies = array();

$ExrCurrencies = $fnexr->getAvailableCurrencies(); if( count($ExrCurrencies) ) foreach($ExrCurrencies as $ccode){

    $rate     = $fnexr->getExchangeRate($ccode);
    $details = fn_Currency::get_currency_details($ccode, ( FNPATH . '/setup/assets/currencies.json' ));
    $details = is_array($details) ? $details : array();

    $Currencies[] = array_merge($details, array('rate'=>$rate));

}

fn_UI::show_errors($errors); fn_UI::show_notes($notices);

?>

<form action="<?php fn_UI::page_url('currencies', array('t'=>'add'))?>" method="post" name="add-currency-form" id="addCurrencyForm">
    <p>
        <label for="ccode">Cod:</label>
        <?php if( count($Currencies) ): ?>
            <select name="ccode" id="ccode">
                <?php foreach($Currencies as $currency): ?>
                    <option value="<?php echo $currency['code']; ?>" data-cname="<?php echo $currency['name']; ?>" data-symbol="<?php echo $currency['symbol']; ?>" data-rate="<?php echo $currency['rate']; ?>">
                        <?php echo $currency['code']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <input type="text" size="45" maxlength="255" name="ccode" id="ccode" value="<?php echo fn_UI::extract_post_val('ccode'); ?>" />
        <?php endif; ?>
        <span class="required">*</span>
    </p>
    <p>
        <label for="cname">Nume:</label>
        <input type="text" size="45" maxlength="255" name="cname" id="cname" value="<?php echo fn_UI::extract_post_val('cname', "", TRUE); ?>" />
        <span class="required">*</span>
    </p>
    <p>
        <label for="csymbol">Simbol:</label>
        <input type="text" size="45" maxlength="255" name="csymbol" id="csymbol" value="<?php echo fn_UI::extract_post_val('csymbol', "", TRUE); ?>" />
        <span class="details">&nbsp;<a href="http://character-code.com/currency-html-codes.php" target="_blank">vezi lista de simboluri monetare &rarr;</a> </span>
    </p>
    <p>
        <label for="cexchange">Rata de schimb:</label>
        <input type="number" step="any" size="45" maxlength="255" name="cexchange" id="cexchange" value="<?php echo fn_UI::extract_post_val('cexchange'); ?>" />
        <span class="details">&nbsp;(raportat la moneda implicit&#259;)</span>
    </p>

    <p>
        <button class="btn btn-primary" type="submit">Adaug&#259;</button>
    </p>
</form>

<script type="text/javascript">
    $(document).ready(function(){
        $('#ccode').change(function(){

            var rate     = $(this).find('option:selected').data('rate');
            var symbol = $(this).find('option:selected').data('symbol');
            var cname   = $(this).find('option:selected').data('cname');

            if( symbol.length > 0 )$('#csymbol').val(symbol);
            if( cname.length > 0 )$('#cname').val(cname);
            if( rate > 0 ) $('#cexchange').val(rate);

        });

        $('#ccode').trigger('change');
    });
</script>