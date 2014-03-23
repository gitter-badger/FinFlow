<?php
/**
 * FinFlow 1.0 - Exchange Rates testing using various APIs
 * @author Adrian S. (https://www.amsquared.co.uk/)
 * @version 1.0
 */

include_once '../inc/init.php';

include_once ( FNPATH .  '/inc/class.exr-bnr.php' );
include_once ( FNPATH .  '/inc/class.exr-oer.php' );
include_once ( FNPATH .  '/inc/class.exr-oerfree.php' );

//Add your OpenExchangeRates.com APP ID
define('FN_OER_APP_ID', '735dab333f984ce1b954d75938fea1d1');

//Get the desired testing API

$api = isset($_GET['api']) ? strtolower( $_GET['api'] ) :false;
$value = 1;


if( empty($api) ){
    ?>
    Alege un API:
    <ul>
        <li><a href="?api=bnr&to=GBP"><?php echo BNR_ExchangeRateParser::name; ?></a></li>
        <li><a href="?api=freeopenexchangerates&from=RON&to=GBP"><?php echo OERFree_ExchangeRateParser::name; ?></a></li>
        <li><a href="?api=openexchangerates&to=GBP"><?php echo OER_ExchangeRateParser::name; ?></a></li>
    </ul>
    <?php exit;
}

$from = isset($_GET['from']) ? strtoupper($_GET['from']) : null;
$to     = isset($_GET['to'])    ? strtoupper($_GET['to']) : 'EUR';

if( $api == 'bnr' ){
    $Exchanger = new BNR_ExchangeRateParser();
}

if( $api == 'openexchangerates' ) {
    $Exchanger = new OER_ExchangeRateParser( FN_OER_APP_ID );
}

if( $api == 'freeopenexchangerates' ) {
    $Exchanger = new OERFree_ExchangeRateParser();
}

if( $from ) $Exchanger->setBaseCurrency( $from );


$Currencies = $Exchanger->getAvailableCurrencies();
$from         = empty($from) ? $Exchanger->origCurrency : $from;

?>

<h2>Folosim API-ul <em><a href="<?php echo( $Exchanger::websiteURL ); ?>" target="_blank"><?php echo( $Exchanger::name ); ?></em></a> </h2>
<h3>Moneda implicita este <?php echo $from; ?></h3>
<h3> <?php echo ( $Exchanger->getExchangeRate( $to ) .' ' . $from ); ?> = <?php echo ( $value .' ' . $to ); ?> </h3>

<pre>


Monede disponibile (<?php echo count($Currencies); ?>) :
<?php print_r($Currencies); ?>

