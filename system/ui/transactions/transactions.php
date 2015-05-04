<?php
/**
 * Renders the transactions template
 */

if ( !defined('FNPATH') ) exit;

use FinFlow\UI;
use FinFlow\OP;
use FinFlow\OP_Pending;
use FinFlow\Contacts;
use FinFlow\Accounts;
use FinFlow\Util;

$_section = ( $s = url_part(2) ) ? $s : 'list';
$errors   = $warnings = $notices = array();

if ( isset($_GET['del']) ){
	//--- remove a transaction ---//
	$removed = ( isset($_GET['t']) and ( $_GET['t'] == 'pending' ) ) ? OP_Pending::remove($_GET['del']) : OP::remove($_GET['del']);
	//--- remove a transaction ---//
}

//prepare transaction filter variables
include_once ( FNPATH . '/system/library/transfilter-vars.php');

//TODO using globals kills defined variables
$filters = isset($filters) ? $filters : array();
$start   = isset($start) ? $start : 0;
$count   = isset($count) ? $count : FN_RESULTS_PER_PAGE;

$pagevars = isset($pagevars) ? $pagevars : array();

$activetab            = array();
$activetab[$_section] = 'active';

//--- add the current report period to the Report menu label ---//
if( empty($filters['enddate']) or ( strtotime($filters['enddate']) > time() ) )
    $report_period = Util::nicetime($filters['startdate'], " pe ", array('m'=>'ultimul', 'f'=>'ultima'), array('m'=>'ultimii', 'f'=>'ultimele'));
else
    $report_period = '';
//--- add the current report period to the Report menu label ---//

?>

<div class="row">

	<div class="col-lg-8 col-md-8">

		<ol class="breadcrumb">
			<li><a href="<?php UI::url('/dashboard'); ?>"><i class="fa fa-home"></i></a></li>
			<li><a href="<?php UI::url('/transactions'); ?>">Transactions</a></li>
			<li class="active">Add transaction</li>
		</ol>
		
		<ul class="nav nav-justified nav-pills nav-page-menu" role="tablist">
			<li class="dropdown <?php echo av($activetab, 'list'); ?>">

				<a href="<?php UI::url('transactions', array('t'=>'list'))?>" class="dropdown-toggle" data-toggle="dropdown">
                    Raport <span class="caret"></span>
                </a>

				<ul class="dropdown-menu" role="menu">
                	<li>
		                <a href="<?php UI::url('transactions', array('sdate'=>$currmonthstart)); ?>">Luna aceasta</a>
	                </li>
                  	<li>
	                    <a href="<?php UI::url('transactions', array('sdate'=>Util::get_relative_time(0, 3, 0, $currmonthstart)) ); ?>">Ultimele 3 luni</a>
                    </li>
                  	<li>
	                    <a href="<?php UI::url('transactions', array('sdate'=>Util::get_relative_time(0, 6, 0, $currmonthstart)) ); ?>">Ultimele 6 luni</a>
                    </li>
                 	<li>
	                    <a href="<?php UI::url('transactions', array('sdate'=>Util::get_relative_time(0, 0, 1, $currmonthstart)) ); ?>">Ultimul an</a>
                    </li>
                 	<li>
	                    <a href="<?php UI::url('transactions', array('sdate'=>Util::get_relative_time(0, 0, 3, $currmonthstart)) ); ?>">Ultimii 3 ani</a>
                    </li>
                 	<li>
	                    <a href="<?php UI::url('transactions', array('sdate'=>Util::get_relative_time(0, 0, 5, $currmonthstart)) ); ?>">Ultimii 5 ani</a>
                    </li>
                 	<li>
	                    <a href="<?php UI::url('transactions', array('sdate'=>'1970-01-01')); ?>">Toate</a>
                    </li>
					<li class="divider"></li>
					<li>
						<a href="<?php UI::url('transactions/calendar'); ?>">Calendar</a>
					</li>
                </ul>
			</li>

			<li class="separator"></li>

			<li class="<?php echo av($activetab, 'generator'); ?>">

				<a href="<?php UI::url('transactions/report')?>"> Generator raport  </a>

			</li>

			<li class="separator"></li>

			<li class="dropdown <?php echo av($activetab, 'pending'); ?>">
                <a href="<?php UI::url('transactions/pending')?>"  class="dropdown-toggle" data-toggle="dropdown">
                    &#206;n a&#351;teptare <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" role="menu">
                    <li>
	                    <a href="<?php UI::url('transactions/pending', array('over'=>'overdue')); ?>">&#206;n &#238;ntarziere</a>
                    </li>
                    <li>
	                    <a href="<?php UI::url('transactions/pending', array('over'=>'30 days')); ?>">&#206;n urm&#259;toarele 30 de zile</a>
                    </li>
                    <li>
	                    <a href="<?php UI::url('transactions/pending', array('over'=>'3 months')); ?>">&#206;n urm&#259;toarele 3 luni</a>
                    </li>
                    <li>
	                    <a href="<?php UI::url('transactions/pending', array('over'=>'6 months')); ?>">&#206;n urm&#259;toarele 6 luni</a>
                    </li>
                    <li>
	                    <a href="<?php UI::url('transactions/pending', array('over'=>'12 months')); ?>">&#206;n urm&#259;toarele 12 luni</a>
                    </li>
                    <li class="divider"></li>
                    <li>
	                    <a href="<?php UI::url('transactions/pending', array('forecast'=>'1')); ?>">Prognoza</a>
                    </li>
                </ul>
            </li>

			<li class="separator"></li>

			<li class="<?php echo av($activetab, 'add'); ?>">
                <a href="<?php UI::url('transactions/add')?>"> Adaug&#259; </a>
            </li>
		</ul>


		<?php

			if ( $_section == 'list' )
				include_once( 'transactions-list.php' );

		?>

		<?php

			if ( $_section == 'pending' )
				include_once( 'pending.php' );

		?>

		<?php

			if ( $_section == 'generator' )
				//include_once( 'transactions-filter.php' );

		?>

		<?php

			if ( $_section == 'calendar' )
				include_once( 'calendar.php' );

		?>

		<?php

			if ( $_section == 'add' )
				include_once( 'transactions-add.php' );

		?>
		
	</div>
	
	<?php UI::component('main/sidebar'); ?>
	
</div>