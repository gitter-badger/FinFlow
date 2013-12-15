<?php if( !defined('FNPATH') ) exit;

//--- get the list of available currencies ---//
include_once (FNPATH . '/inc/class.exrbnr.php');

$Exchange   = new exchangeRatesBNR(FN_EXCHANGERATES_XML_URL);
$Currencies = $Exchange->currency;

fn_UI::show_errors($errors); fn_UI::show_notes($notices);

?>

<form action="<?php fn_UI::page_url('currencies', array('t'=>'add'))?>" method="post" name="add-currency-form" id="addCurrencyForm">
    <p>
        <label for="cname">Nume:</label>
        <input type="text" size="45" maxlength="255" name="cname" id="cname" value="<?php echo fn_UI::extract_post_val('cname', "", TRUE); ?>" />
        <span class="required">*</span>
    </p>
    <p>
        <label for="ccode">Cod:</label>
        <?php if( count($Currencies) ): ?>
            <select name="ccode" id="ccode">
                <?php foreach($Currencies as $currency): $ccode = strtoupper($currency['name']); $rate = $currency['value'];  ?>
                    <option value="<?php echo $ccode; ?>" data-rate="<?php echo $rate; ?>"><?php echo $ccode; ?></option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <input type="text" size="45" maxlength="255" name="ccode" id="ccode" value="<?php echo fn_UI::extract_post_val('ccode'); ?>" />
        <?php endif; ?>
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
        $('#ccode').change(function(){ var rate = $(this).find('option:selected').data('rate'); if( rate > 0 ) $('#cexchange').val(rate); console.log(rate); });
        $('#ccode').trigger('change');
    });
</script>