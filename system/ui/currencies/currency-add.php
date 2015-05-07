<?php if( !defined('FNPATH') ) exit;

use FinFlow\UI;
use FinFlow\Util;
use FinFlow\Currency;


//TODO add support for parser and caching of currencies
//include_once ( FNPATH . '/system/library/exr-init.php' );
/*
$fnexr->setBaseCurrency( Currency::get_default_cc() );

if( !$fnexr->isCacheValid() ): ?>
    <div class="alert alert-warning">
        Fi&#351;ierul cache cu ratele de schimb trebuie actualizat.
	    <a href="#" onclick="fn_popup('<?php echo FN_URL; ?>/inc/cron/cron.exchangerates.php');">Actualizeaz&#259; acum</a>
    </div>
<?php return; endif; ?>

<?php
*/

//$Currencies    = array();
//$ExrCurrencies = $fnexr->getAvailableCurrencies();

$ExrCurrencies = array();
$Currencies    = array();

if( count($ExrCurrencies) ) foreach($ExrCurrencies as $ccode){

    $rate    = $fnexr->getExchangeRate($ccode);
    $details = Currency::get_currency_details($ccode, ( FNPATH . '/setup/assets/currencies.json' ));
    $details = is_array($details) ? $details : array();

    if( empty($details) ) continue; //no details for the currency, maybe currency isn't supported

    $Currencies[] = array_merge($details, array('rate'=>$rate));

}

UI::show_errors($errors); UI::show_notes($notices); ?>

<div class="panel panel-default">

	<div class="panel-heading">
		<h4> Adauga moneda </h4>
	</div>

	<div class="panel-body form-container">

		<form class="form form-horizontal" action="<?php UI::page_url('currencies/add'); ?>" method="post" name="add-currency-form" id="addCurrencyForm" role="form">

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
						<input class="form-control" type="text" size="45" maxlength="255" name="ccode" id="ccode" value="<?php echo UI::extract_post_val('ccode'); ?>" />
					<?php endif; ?>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3" for="cname">Nume:</label>
				<div class="col-lg-4">
					<input class="form-control" type="text" size="45" maxlength="255" name="cname" id="cname" value="<?php echo UI::extract_post_val('cname', "", TRUE); ?>" />
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3" for="csymbol">Simbol:</label>
				<div class="col-lg-4">
					<input class="form-control" type="text" size="45" maxlength="255" name="csymbol" id="csymbol" value="<?php echo UI::extract_post_val('csymbol', "", TRUE); ?>" />
					<p class="help-block">
						<a href="http://character-code.com/currency-html-codes.php" target="_blank">
							simboluri monetare <i class="fa fa-external-link"></i>
						</a>
					</p>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3" for="cexchange">Rata de schimb:</label>
				<div class="col-lg-4">
					<input class="form-control" type="number" step="any" size="45" maxlength="255" name="cexchange" id="cexchange" value="<?php echo UI::extract_post_val('cexchange'); ?>" />
					<span class="help-block"> (raportat la moneda implicit&#259;)</span>
				</div>
			</div>

			<p>&nbsp;</p>

			<div class="form-group">
				<div class="col-lg-12 align-center">
					<button class="btn btn-primary btn-submit" type="submit">Adaug&#259;</button>
				</div>
			</div>
		</form>

	</div>

</div>