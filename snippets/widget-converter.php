<?php if ( !defined('FNPATH') ) include_once '../inc/init.php';

if ( !fn_User::is_authenticated() ) exit();

if ( isset($_GET['convert']) ){
	
	//--- convert currency and return the result ---//
	$from_id = intval($_POST['from']);
	$to_id	 = intval($_POST['to']);
	
	$sum = floatval($_POST['sum']);

    if( $from_id != $to_id )
	    $converted = fn_Currency::convert($sum, $from_id, $to_id, 'id');
    else
        $converted = $sum;

    echo fn_Util::format_nr($converted, 4);

    exit();
}

if ( empty($Currencies) ) $Currencies = fn_Currency::get_all();

?>

<form name="widget-currency-converter-form" id="widgetCurrencyConverterForm" target="_self">
	<p>
		<label  for="cc_sum">Suma:</label>
		<input type="text" size="10" maxlength="255" name="sum" id="cc_sum" value=""/>
	</p>
	<p>
		<label  for="cc_from_id">Din:</label>
		<select name="from" id="cc_from_id">
			<?php foreach ($Currencies as $currency): ?>
			<option value="<?php echo $currency->currency_id?>"><?php echo $currency->ccode; ?></option>
			<?php endforeach;?>
		</select>
	</p>
	<p>
		<label  for="cc_to_id">&#206;n:</label>
		<select name="from" id="cc_to_id">
			<?php foreach ($Currencies as $currency): ?>
			<option value="<?php echo $currency->currency_id?>"><?php echo $currency->ccode; ?></option>
			<?php endforeach;?>
		</select>
	</p>
	<p>
		<input type="text" id="widgetCurrencyConverterResult" value="0.00"/>
	</p>
	<p class="align-center">
		<button class="btn" type="button" onclick="fn_cconvert('<?php echo FN_URL . '/snippets/widget-converter.php?convert=1' ?>', '#widgetCurrencyConverterResult')"> 
			Converte&#351;te 
		</button>
	</p>
</form>
