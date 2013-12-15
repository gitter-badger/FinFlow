<?php if( !defined('FNPATH') ) exit; global $Currencies;

include_once (FNPATH . '/inc/Highchart.php');

//--- plot currency history ---//

$categories  = array();
$series		 = array();

if(count($Currencies)) foreach ($Currencies as $currency){

    $CHistory = fn_Currency::get_currency_history($currency->currency_id);

    $data 		 = array();

    if ( count($CHistory) ) foreach ($CHistory as $histrecord){
        $data[] 	  = floatval($histrecord->rate);

        if ( count($categories) < count($CHistory) )
            $categories[] 	= fn_UI::translate_date( date('j/n/Y', strtotime($histrecord->date)) );
    }

    $series[] = array('name'=>$currency->ccode, 'data'=>$data);

}

$Chart = fn_Util::highchart('chartCurrenciesHistory', 'line', "Istoric rate de schimb", $categories, "Rata de schimb", $series);

$Chart->xAxis->labels->rotation 	= -45;
$Chart->xAxis->labels->align 		= "right";

$Chart->xAxis->labels->style->font = "normal 12px Verdana, sans-serif";

//--- plot currency history ---//

?>

<?php if( count($Currencies) ) : ?>

<script type="text/javascript" src="<?php echo FN_URL;?>/js/highcharts.js"></script>
<script type="text/javascript" src="<?php echo FN_URL;?>/js/highcharts-exporting.js"></script>

<div class="chart" id="chartCurrenciesHistory"></div>

<script type="text/javascript"><?php echo $Chart->render("chartCurrenciesHistory");  ?></script>

<?php else:

    fn_UI::msg(sprintf("Nu sunt monede predefinite. Verific&#259; set&#259;rile pentru <a href=\"%1s\">parserul automat al cursului valutar</a> sau <a href=\"%2s\">adaug&#259; c&#226;teva monede</a> folosind formularul de ad&#259;ugare.", 'index.php?p=settings&t=exrparser', 'index.php?p=currencies&t=add'), fn_UI::$MSG_WARN);

endif; ?>