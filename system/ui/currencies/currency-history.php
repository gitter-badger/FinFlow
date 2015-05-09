<?php if( !defined('FNPATH') ) exit;

use FinFlow\UI;
use FinFlow\Util;
use FinFlow\Currency;

//--- plot currency history ---//

$categories  = array();
$series		 = array();

if( count($Currencies) ) foreach ($Currencies as $currency){

    $CHistory = Currency::get_currency_history($currency->currency_id);
    $data     = array();

    if ( count($CHistory) ) foreach ($CHistory as $histrecord){

        $data[] = floatval($histrecord->rate);

        if ( count($categories) < count($CHistory) )
            $categories[] 	= UI::translate_date( date('j/n/Y', strtotime($histrecord->date)) );
    }

    $series[] = array('name'=>$currency->ccode, 'data'=>$data);

}

$Chart = Util::highchart('chartCurrenciesHistory', 'line', "Currency rates history", $categories, "Exchange rate", $series);

$Chart->xAxis->labels->rotation = -45;
$Chart->xAxis->labels->align 	= "right";

$Chart->xAxis->labels->style->font = "normal 12px Verdana, sans-serif";

//--- plot currency history ---//

UI::enqueue_js('assets/js/highcharts.js');
UI::enqueue_js('assets/js/highcharts-exporting.js'); ?>

<?php if( count($Currencies) ) : ?>

	<div class="panel panel-default">

		<div class="panel-heading">
			<h4>History of currency rates</h4>
		</div>

		<div class="panel panel-body">
			<div class="chart" id="chartCurrenciesHistory"></div>
			<?php UI::enqueue_inline( $Chart->render("chartCurrenciesHistory"), 'js' ); ?>
		</div>

	</div>

<?php else:

    UI::msg(

	    sprintf('There are no predefined currencies.
	    		 Check your settings for the <a href="%1s">automatic currency rates parser</a>
	    		 or <a href="%2s">add a few currencies</a> using the <em>Add currency</em> form.',

		    UI::url('settings/parsers/exchange-rates'), UI::url('curencies/add')
	    ),

	    UI::MSG_WARN
    );

endif; ?>