<?php if( !defined('FNPATH') ) exit;

$tab = isset($_GET['t']) ? urldecode($_GET['t']) : 'list'; $activetab = array(); $activetab[$tab] = 'active'; $errors = array();

if ( isset($_GET['del']) ){
	
	//--- delete a currency ---//

    $currency_id = intval($_GET['del']);

	if (  fn_Currency::has_transactions($currency_id) )
        $errors[] = "Exist&#259; &#238;nc&#259; tranzac&#355;ii &#238;n moneda selectat&#259; pentru a fi &#351;tears&#259;. ";
    else
        fn_Currency::remove($currency_id);
	
	//--- delete a currency ---//
	
}

$defaultCurrency = fn_Currency::get_default();

if ( isset($_POST['cname']) ){

	//--- adding a new currency ---//

	$notices= array();

	$exrate = floatval(str_replace(",", ".", $_POST['cexchange']));
	$csymbol= isset($_POST['csymbol']) ? fn_UI::esc_html($_POST['csymbol']) : "";
	$ccode	= strtoupper(trim($_POST['ccode']));

	if ( !fn_CheckValidityOf::stringlen($_POST['cname']) ) 	$errors[] = "Lipseste numele monedei.";
	if ( !fn_CheckValidityOf::stringlen($ccode) ) 				$errors[] = "Codul monedei este invalid.";

	if ( ( $exrate == 1 ) and count($defaultCurrency)  ) $errors[] = "Exista deja o moneda implicita, cu rata de schimb 1.";

	if ( $exrate <= 0 ) $errors[] = "Rata de schimb este gresita.";

	//--- check duplicate currency code ---//
	$duplicateCCode = fn_Currency::get_by_code($ccode, 'currency_id');
	if ( count($duplicateCCode) and isset($duplicateCCode->currency_id) ) $errors[] = "Exista deja o moneda cu codul {$ccode}.";
    //--- check duplicate currency code ---//

	if ( empty($errors) ){

        $saved = fn_Currency::add($_POST['cname'], $csymbol, $ccode, $exrate);

		if ( $saved )
			$notices[] = "Moneda a fost adaugat&#259;.";
		else
			$errors[] = "<strong>Eroare SQL:</strong> {$fndb->error}";

        $_POST = array();

	}

	//--- adding a new currency ---//

}

$Currencies = fn_Currency::get_all(TRUE);

?>

<div class="row content">
	<div class="span10">
		
		<ul class="nav nav-tabs">
			<li class="<?php echo $activetab['list']; ?>"><a href="<?php fn_UI::page_url('currencies', array('t'=>'list'))?>">Lista</a></li>
			<li class="<?php echo $activetab['history']; ?>"><a href="<?php fn_UI::page_url('currencies', array('t'=>'history'))?>">Istoric</a></li>
			<li class="<?php echo $activetab['add']; ?>"><a href="<?php fn_UI::page_url('currencies', array('t'=>'add'))?>"> Adaug&#259; moned&#259; </a></li>
		</ul>
		
		<?php if ( $tab == 'list' ) include_once 'currencies-list.php'; ?>
		
		<?php if ( $tab == 'add' ) include_once 'currency-add.php'; ?>
		
		<?php if ( $tab == 'history' ) include_once 'currency-history.php'; ?>
		
	</div>
	<?php include_once ( FNPATH . '/snippets/sidebar.php' ); ?>
</div>