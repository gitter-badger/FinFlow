<?php if( !defined('FNPATH') ) exit;

use FinFlow\UI;
use FinFlow\Util;
use FinFlow\Currency;
use FinFlow\CanValidate;

$_section = ( $s = url_part(2) ) ? $s : 'list';

$activetab            = array();
$activetab[$_section] = 'active';

$errors = $notices= array();

if ( isset($_GET['del']) ){
	
	//--- delete a currency ---//

    $currency_id = intval($_GET['del']);

	if ( Currency::has_transactions($currency_id) )
        $errors[] = "Exist&#259; &#238;nc&#259; tranzac&#355;ii &#238;n moneda selectat&#259; pentru a fi &#351;tears&#259;. ";
    else
        Currency::remove($currency_id);
	
	//--- delete a currency ---//
	
}

$defaultCurrency = Currency::get_default();

if ( isset($_POST['cname']) ){

	//--- adding a new currency ---//

	$exrate = floatval( str_replace(",", ".", post('cexchange')) );
	$csymbol= isset($_POST['csymbol']) ? UI::esc_html($_POST['csymbol']) : '';
	$ccode	= strtoupper( trim( post('ccode') ) );

	if ( ! CanValidate::stringlen( post('cname') ) )
		$errors[] = "Lipseste numele monedei.";

	if ( ! CanValidate::stringlen($ccode) )
		$errors[] = "Codul monedei este invalid.";

	if ( ( $exrate == 1 ) and count($defaultCurrency)  )
		$errors[] = "Exista deja o moneda implicita, cu rata de schimb 1.";

	if ( $exrate <= 0 )
		$errors[] = "Rata de schimb este gresita.";

	//--- check duplicate currency code ---//
	$duplicateCCode = Currency::get_by_code($ccode, 'currency_id');

	if ( count($duplicateCCode) and isset($duplicateCCode->currency_id) )
		$errors[] = "Exista deja o moneda cu codul {$ccode}.";
    //--- check duplicate currency code ---//

	if ( empty($errors) ){

        $saved = Currency::add(post('cname'), $csymbol, $ccode, $exrate);

		if ( $saved )
			$notices[] = "Moneda a fost adaugat&#259;.";
		else
			$errors[] = "<strong>Eroare SQL:</strong> {$fndb->error}";

        $_POST = array();

	}

	//--- adding a new currency ---//

}

$Currencies = Currency::get_all(TRUE); ?>

<div class="row">
	<div class="col-lg-8 col-md-8">
		
		<ul class="nav nav-justified nav-pills nav-page-menu" role="tablist">
			<li class="<?php echo av($activetab, 'list'); ?>">
				<a href="<?php UI::url('currencies')?>">Lista</a>
			</li>
			<li class="separator"></li>
			<li class="<?php echo av($activetab, 'history'); ?>">
				<a href="<?php UI::url('currencies/history')?>">Istoric</a>
			</li>
			<li class="separator"></li>
			<li class="<?php echo av($activetab, 'add'); ?>">
				<a href="<?php UI::url('currencies/add')?>"> Adaug&#259; moned&#259; </a>
			</li>
		</ul>
		
		<?php

			if ( $_section == 'list' )
				include_once 'currencies-list.php';

		?>
		
		<?php

			if ( $_section == 'add' )
				include_once 'currency-add.php';

		?>
		
		<?php

			if ( $_section == 'history' )
				include_once 'currency-history.php';

		?>
		
	</div>

	<?php UI::component('main/sidebar'); ?>

</div>