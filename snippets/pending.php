<?php if ( !defined('FNPATH') ) exit(); global $Currency, $filters, $start, $count, $pagevars; ?>

<?php

if( $_GET['roots'] ):

    echo 'Showing roots';//TODO

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

    $starttime = ( @strtotime( fn_OP_Pending::get_min_date($cfilters) ) - 86400 );

    if( $starttime > time() ) $starttime = time();

    $cfilters['startdate'] = date(FN_MYSQL_DATE, $starttime);

    //-- figure out the startdate ---//

    $cfilters['order']     = 'ASC';
    $cfilters['orderby'] = 'fdate';

    $Transactions    = fn_OP_Pending::get_period($cfilters);
    $Total              = fn_OP_Pending::get_period_sum($cfilters);

    $Income   = fn_OP_Pending::get_period_sum( array_merge($cfilters, array('type'=>FN_OP_IN)) );
    $Outcome = fn_OP_Pending::get_period_sum( array_merge($cfilters, array('type'=>FN_OP_OUT)) ); ?>

    <?php if( count($Transactions) ): ?>

        <h4 class="form-label-normal" style="margin-top: 20px;">
            <em>P&#226;n&#259; la <?php echo fn_UI::translate_date( date(FN_DAY_FORMAT, $endtime) ); ?></em>
        </h4>

        <?php include 'pending-period-list.php'; ?>

    <?php else: ?>
        <h4 class="form-label-normal" style="margin-top: 20px;">
            <em>P&#226;n&#259; la <?php echo fn_UI::translate_date( date(FN_DAY_FORMAT, $endtime) ); ?></em>
        </h4>
        <p class="msg note">
            Nu am gasit tranzac&#355;ii &#238;n a&#351;teptare pentru perioada selectat&#259;.
        </p>
    <?php endif; ?>

<?php endif; ?>