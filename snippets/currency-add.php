<?php if( !defined('FNPATH') ) exit;

include_once ( FNPATH . '/inc/exr-init.php' ); global $fnexr; $fnexr->setBaseCurrency( fn_Currency::get_default_cc() ) ?>

<?php if( !$fnexr->isCacheValid() ): ?>
    <div class="alert alert-warning">
        Fi&#351;ierul cache cu ratele de schimb trebuie actualizat. <a href="#" onclick="fn_popup('<?php echo FN_URL; ?>/inc/cron/cron.exchangerates.php');">Actualizeaz&#259; acum</a>
    </div>
<?php return; endif; ?>

<?php

$Currencies = array(); $ExrCurrencies = $fnexr->getAvailableCurrencies(); if( count($ExrCurrencies) ) foreach($ExrCurrencies as $ccode){

    $rate     = $fnexr->getExchangeRate($ccode);
    $details = fn_Currency::get_currency_details($ccode, ( FNPATH . '/setup/assets/currencies.json' ));
    $details = is_array($details) ? $details : array();

    if( empty($details) ) continue; //no details for the currency, maybe currency isn't supported

    $Currencies[] = array_merge($details, array('rate'=>$rate));

}

fn_UI::show_errors($errors); fn_UI::show_notes($notices); ?>

<form class="form form-horizontal" action="<?php fn_UI::page_url('currencies', array('t'=>'add'))?>" method="post" name="add-currency-form" id="addCurrencyForm" role="form">

    <div class="form-group">
        <label class="control-label col-lg-3" for="ccode">Cod:</label>
        <div class="col-lg-4">
            <?php if( count($Currencies) ): ?>
                <select class="form-control" name="ccode" id="ccode">
                    <?php foreach($Currencies as $currency): ?>
                        <option value="<?php echo $currency['code']; ?>" data-cname="<?php echo $currency['name']; ?>" data-symbol="<?php echo $currency['symbol']; ?>" data-rate="<?php echo $currency['rate']; ?>">
                            <?php echo $currency['code']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input class="form-control" type="text" size="45" maxlength="255" name="ccode" id="ccode" value="<?php echo fn_UI::extract_post_val('ccode'); ?>" />
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3" for="cname">Nume:</label>
        <div class="col-lg-4">
            <input class="form-control" type="text" size="45" maxlength="255" name="cname" id="cname" value="<?php echo fn_UI::extract_post_val('cname', "", TRUE); ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3" for="csymbol">Simbol:</label>
        <div class="col-lg-4">
            <input class="form-control" type="text" size="45" maxlength="255" name="csymbol" id="csymbol" value="<?php echo fn_UI::extract_post_val('csymbol', "", TRUE); ?>" />
            <span class="details">&nbsp;<a href="http://character-code.com/currency-html-codes.php" target="_blank">vezi lista de simboluri monetare &rarr;</a> </span>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3" for="cexchange">Rata de schimb:</label>
        <div class="col-lg-4">
            <input class="form-control" type="number" step="any" size="45" maxlength="255" name="cexchange" id="cexchange" value="<?php echo fn_UI::extract_post_val('cexchange'); ?>" />
            <span class="details">&nbsp;(raportat la moneda implicit&#259;)</span>
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-primary" type="submit">Adaug&#259;</button>
    </div>
</form>