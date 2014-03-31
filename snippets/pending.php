<?php if ( !defined('FNPATH') ) exit(); global $Currency, $filters, $start, $count, $pagevars; ?>

<?php

if( $_GET['roots'] ):

    echo 'Showing roots';//TODO

else:

    $listmax= isset($_GET['over']) ? $_GET['over'] : 'overdue';


    if( $listmax == 'overdue' ){
        $endtime = time(); $starttime = @strtotime( fn_OP_Pending::get_min_date($cfilters) );
    }
    else{
        $timeplus = @explode(' ', $listmax); $tnumeric = intval( $timeplus[0] ); $tunit = trim(strtolower($timeplus[1]));

        $endtime  = @strtotime("+$tnumeric $tunit");
        $starttime = time();
    }

    $cfilters = array();

    $cfilters['startdate'] = date(FN_MYSQL_DATE, $starttime);
    $cfilters['enddate'] = date(FN_MYSQL_DATE, $endtime);

    $cfilters['start']  = 0;
    $cfilters['count'] = FN_RESULTS_PER_PAGE;

    $cfilters['root_id'] = '0';

    $cfilters['order']     = 'ASC';
    $cfilters['orderby'] = 'fdate';

    $Transactions    = fn_OP_Pending::get_all($cfilters);
    $Total              = fn_OP_Pending::get_compound_sum($cfilters);

    $Income   = fn_OP_Pending::get_compound_sum( array_merge($cfilters, array('type'=>FN_OP_IN)) );
    $Outcome = fn_OP_Pending::get_compound_sum( array_merge($cfilters, array('type'=>FN_OP_OUT)) );

    $days     = fn_Util::date_diff($cfilters['startdate'], $cfilters['enddate'], 'days');      //number of days in the timespan
    $months = fn_Util::date_diff($cfilters['startdate'], $cfilters['enddate'], 'months');  //number of months in the timespan
    $years    = fn_Util::date_diff($cfilters['startdate'], $cfilters['enddate'], 'years');    //number of years in the timespan

    ?>

    <?php if( count($Transactions) ): ?>

        <h4 class="form-label-normal" style="margin-top: 20px;">
            <em>P&#226;n&#259; la <?php echo fn_UI::translate_date( date(FN_DAY_FORMAT, $endtime) ); ?></em>
        </h4>

        <?php include 'pending-list.php'; ?>

    <?php elseif( $listonly == '12months' ): ?>
        <h4 class="form-label-normal" style="margin-top: 20px;"><em>6 months to 12 months</em></h4>
        <p class="msg note">
            Nu am gasit tranzac&#355;ii in asteptare. <a href="<?php fn_UI::page_url('transactions', array('t'=>'add')); ?>">Adaug&#259; &rarr;</a>
        </p>
    <?php endif; ?>

<?php endif; ?>