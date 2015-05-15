<?php if ( !defined('FNPATH') ) exit();

use FinFlow\UI;
use FinFlow\OP_Pending;

//global $Currency, $filters, $start, $count, $pagevars;

if( isset( $_GET['forecast'] ) ):

    include_once 'pending-forecast.php';

else:

    $listmax= isset($_GET['over']) ? $_GET['over'] : 'overdue';


    if( $listmax == 'overdue' ){
        $endtime = time(); $starttime = time();
    }
    else{
        $timeplus = @explode(' ', $listmax); $tnumeric = intval( $timeplus[0] ); $tunit = trim(strtolower($timeplus[1]));

        $endtime  = @strtotime("+$tnumeric $tunit");
        $starttime = time();
    }

    $cfilters = array();

    $cfilters['enddate'] = date(FN_MYSQL_DATE, $endtime);

    $cfilters['start']  = 0;
    $cfilters['count'] = FN_RESULTS_PER_PAGE;

    $cfilters['root_id'] = '0';

    //-- figure out the startdate ---//

    $starttime = ( @strtotime( OP_Pending::get_min_date($cfilters) ) - 86400 );

    if( $starttime > time() )
	    $starttime = time();

    $cfilters['startdate'] = date(FN_MYSQL_DATE, $starttime);

    //-- figure out the startdate ---//

    $cfilters['order']     = 'ASC';
    $cfilters['orderby'] = 'fdate';

    $Transactions = OP_Pending::get_period($cfilters);
    $Total        = OP_Pending::get_period_sum($cfilters);

    $Income  = OP_Pending::get_period_sum( array_merge($cfilters, array('type'=>OP_Pending::TYPE_IN)) );
    $Outcome = OP_Pending::get_period_sum( array_merge($cfilters, array('type'=>OP_Pending::TYPE_OUT)) ); ?>

    <?php if( count($Transactions) ): ?>

        <?php include 'pending-period-list.php'; ?>

    <?php else: ?>

		<div class="alert alert-info">
			<p>Nu am gasit tranzac&#355;ii &#238;n a&#351;teptare pentru perioada selectat&#259;.</p>
		</div>

		<div class="panel panel-default">

			<div class="panel-heading">

				<h4>
					<em>P&#226;n&#259; la <?php echo UI::translate_date( date(FN_DAY_FORMAT, $endtime) ); ?></em>
				</h4>

			</div>

			<div class="panel-body">

				...

			</div>

		</div>


    <?php endif; ?>

<?php endif; ?>