<?php if( !defined('FNPATH') ) exit;

global $fndb, $fnsql;

include_once ( FNPATH . '/inc/transfilter-vars.php');
include_once (FNPATH . '/inc/Highchart.php');

global $filters;

$tab   = isset($_GET['t']) ? urldecode($_GET['t']) : 'list'; $activetab = array(); $activetab[$tab] = 'active';
$span = isset($_POST['span']) ? trim($_POST['span']) : "monthly";

//--- build charts ---//
if ( empty($_GET['sdate']) ){ //sdate is set at default
	$sdate 				 	= fn_Util::get_relative_time(0, 3, 0, $currmonthstart);
	$filters['startdate'] 	= $sdate;
}

$Chart = null;

//--- build charts ---//

if( $tab =='list' ){ //totals performance

    $Sums = fn_OP::get_timely_sum($filters, $span);

    if ( count($Sums) ){

        $vars = fn_Util::highchart_prepare_data($Sums); @extract($vars);

        $Chart = fn_Util::highchart('chartTransactionsGrowth', 'column', "Performanta Rulaj: {$chartdtspan}", $categories, "Suma (RON)", $series, TRUE);

        $Chart->xAxis->labels->rotation 	= -45;
        $Chart->xAxis->labels->align 		= "right";

        $Chart->xAxis->labels->style->font = "normal 14px Verdana, sans-serif";
    }

}

if( $tab == 'list2' ){
    $Incomes    = fn_OP::get_timely_sum(array_merge($filters, array('type'=>FN_OP_IN)), $span);
    $Outcomes = fn_OP::get_timely_sum(array_merge($filters, array('type'=>FN_OP_OUT)), $span);

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

    ksort($Sums);

    $vars = fn_Util::highchart_prepare_data($Sums); @extract($vars);

    $Chart = fn_Util::highchart('chartBalanceEvolution', 'line', "Evolutie balanta: {$chartdtspan}", $categories, "Suma (RON)", $series, TRUE);

    $Chart->xAxis->labels->rotation 	= -45;
    $Chart->xAxis->labels->align 		= "right";

    $Chart->xAxis->labels->style->font = "normal 14px Verdana, sans-serif";
}

?>

<div class="row content">
	<div class="span10">

		<ul class="nav nav-tabs">
			<li class="dropdown <?php echo $activetab['list']; ?>">
				<a href="<?php fn_UI::page_url('performance', array('t'=>'list')); ?>" class="dropdown-toggle" data-toggle="dropdown">Rulaj <b class="caret"></b></a>
				<ul class="dropdown-menu">
                  	<li><a href="<?php fn_UI::page_url('performance', array('sdate'=>fn_Util::get_relative_time(0, 3, 0, $currmonthstart)) ); ?>">Ultimele 3 luni</a></li>
                  	<li><a href="<?php fn_UI::page_url('performance', array('sdate'=>fn_Util::get_relative_time(0, 6, 0, $currmonthstart)) ); ?>">Ultimele 6 luni</a></li>
                 	<li><a href="<?php fn_UI::page_url('performance', array('sdate'=>fn_Util::get_relative_time(0, 0, 1, $currmonthstart)) ); ?>">Ultimul an</a></li>
                 	<li><a href="<?php fn_UI::page_url('performance', array('sdate'=>fn_Util::get_relative_time(0, 0, 3, $currmonthstart)) ); ?>">Ultimii 3 ani</a></li>
                 	<li><a href="<?php fn_UI::page_url('performance', array('sdate'=>fn_Util::get_relative_time(0, 0, 5, $currmonthstart)) ); ?>">Ultimii 5 ani</a></li>
                 	<li><a href="<?php fn_UI::page_url('performance', array('sdate'=>'1970-01-01')); ?>">Toate</a></li>
                </ul>
			</li>

			<li class="dropdown <?php echo $activetab['list2']; ?>">
				<a href="<?php fn_UI::page_url('performance', array('t'=>'list2')); ?>" class="dropdown-toggle" data-toggle="dropdown">Evolu&#355;ie balan&#355;&#259; <b class="caret"></b></a>
				<ul class="dropdown-menu">
                  	<li><a href="<?php fn_UI::page_url('performance', array('t'=>'list2', 'sdate'=>fn_Util::get_relative_time(0, 3, 0, $currmonthstart)) ); ?>">Ultimele 3 luni</a></li>
                  	<li><a href="<?php fn_UI::page_url('performance', array('t'=>'list2', 'sdate'=>fn_Util::get_relative_time(0, 6, 0, $currmonthstart)) ); ?>">Ultimele 6 luni</a></li>
                 	<li><a href="<?php fn_UI::page_url('performance', array('t'=>'list2', 'sdate'=>fn_Util::get_relative_time(0, 0, 1, $currmonthstart)) ); ?>">Ultimul an</a></li>
                 	<li><a href="<?php fn_UI::page_url('performance', array('t'=>'list2', 'sdate'=>fn_Util::get_relative_time(0, 0, 3, $currmonthstart)) ); ?>">Ultimii 3 ani</a></li>
                 	<li><a href="<?php fn_UI::page_url('performance', array('t'=>'list2', 'sdate'=>fn_Util::get_relative_time(0, 0, 5, $currmonthstart)) ); ?>">Ultimii 5 ani</a></li>
                 	<li><a href="<?php fn_UI::page_url('performance', array('t'=>'list2', 'sdate'=>'1970-01-01')); ?>">Toate</a></li>
                </ul>
			</li>

            <!---
            <li class="<?php echo $activetab['generator']; ?>"><a href="<?php fn_UI::page_url('transactions', array('t'=>'generator'))?>"> Generator raport  </a></li>
            --->

		</ul>

        <?php if( is_object($Chart) and is_object($Chart->series) ) : ?>

            <script type="text/javascript" src="<?php echo FN_URL;?>/js/highcharts.js"></script>
            <script type="text/javascript" src="<?php echo FN_URL;?>/js/highcharts-exporting.js"></script>

            <?php if ($tab == 'list'): ?>

                <div class="chart" id="chartTransactionsGrowth"></div><script type="text/javascript"><?php echo $Chart->render("chartTransactionsGrowth");  ?></script>

            <?php endif;?>

            <?php if ($tab == 'list2'): ?>

                <div class="chart" id="chartBalanceEvolution"></div><script type="text/javascript"><?php echo $Chart->render("chartBalanceEvolution");  ?></script>

            <?php endif;?>

            <br class="clear"/>

            <form class="form-inline" id="granularitySelectForm" method="post" target="_self">
                <label form="span">E&#351;antionare:</label>
                <select name="span" id="span" onchange="document.getElementById('granularitySelectForm').submit();">
                    <option value="monthly" <?php echo fn_UI::selected_or_not('monthly', $_POST['span']); ?>>lunar</option>
                    <option value="yearly" <?php echo fn_UI::selected_or_not('yearly', $_POST['span']); ?>>anual</option>
                    <option value="daily" <?php echo fn_UI::selected_or_not('daily', $_POST['span']); ?>>zilnic</option>
                </select>
            </form>

        <?php else: ?>
            <p class="msg note">  Nu sunt date suficiente pentru afi&#351;area graficului.   </p>
        <?php endif; ?>

	</div>
	
	<?php include_once ( FNPATH . '/snippets/sidebar.php' ); ?>
	
</div>