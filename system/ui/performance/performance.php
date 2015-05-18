<?php if( !defined('FNPATH') ) exit;

use FinFlow\UI;
use FinFlow\Util;
use FinFlow\OP;
use FinFlow\Currency;

//prepare transaction filter variables
include_once ( FNPATH . '/system/library/transfilter-vars.php');

$Currency = Currency::get_default();

$_section = ( $s = url_part(2) ) ? $s : 'list';
$activetab = array();
$activetab[$_section] = 'active';

$span = isset($_POST['span']) ? trim($_POST['span']) : "monthly";

//--- build charts ---//
if ( empty($_GET['sdate']) ){ //sdate is set at default
	$sdate 				 	= Util::get_relative_time(0, 3, 0, $currmonthstart);
	$filters['startdate'] 	= $sdate;
}

$Chart = null;

//--- build charts ---//

if( $_section =='list' ){ //totals performance

    $Sums = OP::get_timely_sum($filters, $span);

    if ( count($Sums) ){

        $vars  = Util::highchart_prepare_data($Sums, $Currency->ccode); @extract($vars);
        $Chart = Util::highchart('chartTransactionsGrowth', 'column', "Performanta Rulaj: {$chartdtspan}", $categories, "Suma ({$Currency->ccode})", $series, TRUE);


        $Chart->chart->zoomType = 'x'; //enable zoom on x-axis

        $Chart->xAxis->labels->rotation 	= -45;
        $Chart->xAxis->labels->align 		= "right";

        $Chart->xAxis->labels->style->font = "normal 14px Verdana, sans-serif";
    }

}

if( $_section == 'list2' ){

    //--- evolutie balanta ---//

    $Incomes  = OP::get_timely_sum(array_merge($filters, array('type'=>OP::TYPE_IN)), $span);
    $Outcomes = OP::get_timely_sum(array_merge($filters, array('type'=>OP::TYPE_OUT)), $span);

    $Sums = array();

    //add incomes to sum
    if( count($Incomes) ) foreach($Incomes as $dt=>$sum){
        $elem = $sum;

        if( isset($Outcomes[$dt]) and ( $Outcomes[$dt]['sum'] > 0 ) ){
            $elem['sum'] = floatval($elem['sum'] - $Outcomes[$dt]['sum']);
        }
        else $elem['sum'] = floatval($elem['sum']);

        $Sums[$dt] = $elem;
    }


    //add outcomes to sum
    if( count($Outcomes) ) foreach($Outcomes as $dt=>$sum){
        $elem = $sum; $elem['sum'] = (-1 * floatval($elem['sum'])); if( !isset($Incomes[$dt]) ) $Sums[$dt] = $elem;
    }

    if( count($Sums) ){

        ksort($Sums);

        $vars  = Util::highchart_prepare_data($Sums, $Currency->ccode); @extract($vars);
        $Chart = Util::highchart('chartBalanceEvolution', 'line', "Evolutie balanta: {$chartdtspan}", $categories, "Suma ({$Currency->ccode})", $series, TRUE);

        $Chart->chart->zoomType         = 'x';
        $Chart->xAxis->labels->rotation 	= -45;
        $Chart->xAxis->labels->align 		= "right";

        $Chart->xAxis->labels->style->font = "normal 14px Verdana, sans-serif";

    }

    //--- evolutie balanta ---//
}

UI::enqueue_js('assets/js/highcharts.js');
UI::enqueue_js('assets/js/highcharts-exporting.js');

if( ! isset($_POST['span']) or empty($_POST['span']) ) $_POST['span'] = null; ?>

<div class="row">
	<div class="col-lg-8 col-md-8">

		<ul class="nav nav-justified nav-pills nav-page-menu" role="menubar">
			<li class="dropdown <?php echo av($activetab, 'list'); ?>">
				<a href="<?php UI::page_url('performance/list'); ?>" class="dropdown-toggle" data-toggle="dropdown">Rulaj <b class="caret"></b></a>
				<ul class="dropdown-menu">
                  	<li><a href="<?php UI::page_url('performance/list', array('sdate'=>Util::get_relative_time(0, 3, 0, $currmonthstart)) ); ?>">Ultimele 3 luni</a></li>
                  	<li><a href="<?php UI::page_url('performance/list', array('sdate'=>Util::get_relative_time(0, 6, 0, $currmonthstart)) ); ?>">Ultimele 6 luni</a></li>
                 	<li><a href="<?php UI::page_url('performance/list', array('sdate'=>Util::get_relative_time(0, 0, 1, $currmonthstart)) ); ?>">Ultimul an</a></li>
                 	<li><a href="<?php UI::page_url('performance/list', array('sdate'=>Util::get_relative_time(0, 0, 3, $currmonthstart)) ); ?>">Ultimii 3 ani</a></li>
                 	<li><a href="<?php UI::page_url('performance/list', array('sdate'=>Util::get_relative_time(0, 0, 5, $currmonthstart)) ); ?>">Ultimii 5 ani</a></li>
                 	<li><a href="<?php UI::page_url('performance/list', array('sdate'=>'1970-01-01')); ?>">Toate</a></li>
                </ul>
			</li>

			<li class="separator"></li>

			<li class="dropdown <?php echo av($activetab, 'list2'); ?>">
				<a href="<?php UI::page_url('performance/list2'); ?>" class="dropdown-toggle" data-toggle="dropdown">Evolu&#355;ie balan&#355;&#259; <b class="caret"></b></a>
				<ul class="dropdown-menu">
                  	<li><a href="<?php UI::page_url('performance/list2', array('sdate'=>Util::get_relative_time(0, 3, 0, $currmonthstart)) ); ?>">Ultimele 3 luni</a></li>
                  	<li><a href="<?php UI::page_url('performance/list2', array('sdate'=>Util::get_relative_time(0, 6, 0, $currmonthstart)) ); ?>">Ultimele 6 luni</a></li>
                 	<li><a href="<?php UI::page_url('performance/list2', array('sdate'=>Util::get_relative_time(0, 0, 1, $currmonthstart)) ); ?>">Ultimul an</a></li>
                 	<li><a href="<?php UI::page_url('performance/list2', array('sdate'=>Util::get_relative_time(0, 0, 3, $currmonthstart)) ); ?>">Ultimii 3 ani</a></li>
                 	<li><a href="<?php UI::page_url('performance/list2', array('sdate'=>Util::get_relative_time(0, 0, 5, $currmonthstart)) ); ?>">Ultimii 5 ani</a></li>
                 	<li><a href="<?php UI::page_url('performance/list2', array('sdate'=>'1970-01-01')); ?>">Toate</a></li>
                </ul>
			</li>

            <!---
            <li class="<?php echo av($activetab, 'generator'); ?>"><a href="<?php UI::page_url('transactions', array('t'=>'generator'))?>"> Generator raport  </a></li>
            --->

		</ul>

        <?php if( is_object($Chart) and is_object($Chart->series) ) : ?>

            <?php if ($_section == 'list'): ?>

                <div class="chart" id="chartTransactionsGrowth"></div>
		        <?php UI::enqueue_inline( $Chart->render("chartTransactionsGrowth" ), 'js'); ?>

            <?php endif;?>

            <?php if ($_section == 'list2'): ?>

                <div class="chart" id="chartBalanceEvolution"></div>
		        <?php UI::enqueue_inline($Chart->render("chartBalanceEvolution"), 'js'); ?>

            <?php endif;?>

            <br class="clear"/>

            <form class="form form-horizontal" id="granularitySelectForm" method="post" target="_self">
                <div class="form-group">
                    <label class="control-label col-lg-3" for="span">E&#351;antionare:</label>
                    <div class="col-lg-3">
                        <select name="span" id="span" class="form-control" onchange="document.getElementById('granularitySelectForm').submit();">
                            <option value="monthly" <?php echo UI::selected_or_not('monthly', $_POST['span']); ?>>lunar</option>
                            <option value="yearly" <?php echo UI::selected_or_not('yearly', $_POST['span']); ?>>anual</option>
                            <option value="daily" <?php echo UI::selected_or_not('daily', $_POST['span']); ?>>zilnic</option>
                        </select>
                    </div>
                </div>
            </form>

        <?php else: ?>
            <div class="alert alert-warning">
	            <?php __e("There's insufficient data to display the charts.") ?>
            </div>
        <?php endif; ?>

	</div>

	<?php UI::component('main/sidebar'); ?>
	
</div>