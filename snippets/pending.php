<?php if ( !defined('FNPATH') ) exit(); global $Currency, $filters, $start, $count, $pagevars; ?>

<?php

$listmax= isset($_GET['over']) ? $_GET['over'] : false;

if( empty( $listmax )  ):

    //--- list overdue ---//

    $Transactions    = fn_OP_Pending::get_overdue($filters, $start, $count);
    $Total              = fn_OP_Pending::get_overdue_sum($filters);

    $Income   = fn_OP_Pending::get_overdue_sum(array('type'=>FN_OP_IN));
    $Outcome = fn_OP_Pending::get_overdue_sum(array('type'=>FN_OP_OUT));

    if( count($Transactions) ): ?>

        <h4 class="form-label-normal"><em>Overdue</em></h4>

        <?php include 'pending-list.php'; ?>

    <?php endif;

    //--- list 1 to 30 days ---//
    $cfilters = array();

    $cfilters['startdate'] = date(FN_MYSQL_DATE, @strtotime('+1 day'));
    $cfilters['enddate'] = date(FN_MYSQL_DATE, @strtotime('+30 days'));

    $cfilters['start']  = 0;
    $cfilters['count'] = FN_RESULTS_PER_PAGE;

    $cfilters['recurring'] = 'no';

    $Transactions    = fn_OP_Pending::get_all($cfilters);
    $Total              = fn_OP_Pending::get_sum($cfilters);

    $Income   = fn_OP_Pending::get_sum( array_merge($cfilters, array('type'=>FN_OP_IN)) );
    $Outcome = fn_OP_Pending::get_sum( array_merge($cfilters, array('type'=>FN_OP_OUT)) );

    if( count($Transactions) ): ?>

        <h4 class="form-label-normal" style="margin-top: 20px;"><em>In the next 30 days</em></h4>

        <?php include 'pending-list.php'; ?>

    <?php elseif( $listonly == '30days' ): ?>
        <h4 class="form-label-normal" style="margin-top: 20px;"><em>In the next 30 days</em></h4>
        <p class="msg note">
            Nu am gasit tranzac&#355;ii in asteptare. <a href="<?php fn_UI::page_url('transactions', array('t'=>'add')); ?>">Adaug&#259; &rarr;</a>
        </p>
    <?php endif;


    //--- list 30 days to 3 months ---//
    $cfilters = array();

    $cfilters['startdate'] = date(FN_MYSQL_DATE, @strtotime('+30 days') + 86400);
    $cfilters['enddate'] = date(FN_MYSQL_DATE, @strtotime('+3 months'));


    $cfilters['start']  = 0;
    $cfilters['count'] = FN_RESULTS_PER_PAGE;

    $cfilters['recurring'] = 'no';

    $Transactions    = fn_OP_Pending::get_all($cfilters);
    $Total              = fn_OP_Pending::get_sum($cfilters);

    $Income   = fn_OP_Pending::get_sum( array_merge($cfilters, array('type'=>FN_OP_IN)) );
    $Outcome = fn_OP_Pending::get_sum( array_merge($cfilters, array('type'=>FN_OP_OUT)) );

    if( count($Transactions) ): ?>

        <h4 class="form-label-normal" style="margin-top: 20px;"><em>30 days to 3 months</em></h4>

        <?php include 'pending-list.php'; ?>

    <?php elseif( $listonly == '3months' ): ?>
        <h4 class="form-label-normal" style="margin-top: 20px;"><em>30 days to 3 months</em></h4>
        <p class="msg note">
            Nu am gasit tranzac&#355;ii in asteptare. <a href="<?php fn_UI::page_url('transactions', array('t'=>'add')); ?>">Adaug&#259; &rarr;</a>
        </p>
    <?php endif;

    //--- list 3 months to 6 months ---//
    $cfilters = array();

    $cfilters['startdate'] = date(FN_MYSQL_DATE, @strtotime('+3 months') + 86400);
    $cfilters['enddate'] = date(FN_MYSQL_DATE, @strtotime('+6 months'));


    $cfilters['start']  = 0;
    $cfilters['count'] = FN_RESULTS_PER_PAGE;

    $cfilters['recurring'] = 'no';

    $Transactions    = fn_OP_Pending::get_all($cfilters);
    $Total              = fn_OP_Pending::get_sum($cfilters);

    $Income   = fn_OP_Pending::get_sum( array_merge($cfilters, array('type'=>FN_OP_IN)) );
    $Outcome = fn_OP_Pending::get_sum( array_merge($cfilters, array('type'=>FN_OP_OUT)) );

    if( count($Transactions) ): ?>

        <h4 class="form-label-normal" style="margin-top: 20px;"><em>3 months to 6 months</em></h4>

        <?php include 'pending-list.php'; ?>

    <?php elseif( $listonly == '6months' ): ?>
        <h4 class="form-label-normal" style="margin-top: 20px;"><em>3 months to 6 months</em></h4>
        <p class="msg note">
            Nu am gasit tranzac&#355;ii in asteptare. <a href="<?php fn_UI::page_url('transactions', array('t'=>'add')); ?>">Adaug&#259; &rarr;</a>
        </p>
    <?php endif;

    //--- list 6 months to 12 months ---//
    $cfilters = array();

    $cfilters['startdate'] = date(FN_MYSQL_DATE, @strtotime('+6 months') + 86400);
    $cfilters['enddate'] = date(FN_MYSQL_DATE, @strtotime('+12 months'));


    $cfilters['start']  = 0;
    $cfilters['count'] = FN_RESULTS_PER_PAGE;

    $cfilters['recurring'] = 'no';

    $Transactions    = fn_OP_Pending::get_all($cfilters);
    $Total              = fn_OP_Pending::get_sum($cfilters);

    $Income   = fn_OP_Pending::get_sum( array_merge($cfilters, array('type'=>FN_OP_IN)) );
    $Outcome = fn_OP_Pending::get_sum( array_merge($cfilters, array('type'=>FN_OP_OUT)) ); ?>

    <?php if( count($Transactions) ): ?>

        <h4 class="form-label-normal" style="margin-top: 20px;"><em>6 months to 12 months</em></h4>

        <?php include 'pending-list.php'; ?>

    <?php elseif( $listonly == '12months' ): ?>
        <h4 class="form-label-normal" style="margin-top: 20px;"><em>6 months to 12 months</em></h4>
        <p class="msg note">
            Nu am gasit tranzac&#355;ii in asteptare. <a href="<?php fn_UI::page_url('transactions', array('t'=>'add')); ?>">Adaug&#259; &rarr;</a>
        </p>
    <?php endif; ?>

<?php else:

    //--- custom timespan ---//
    if( $listmax == 'overdue' ){
        $endtime = time();
    }
    else{
        $timeplus = @explode(' ', $listmax); $tnumeric = intval( $timeplus[0] ); $tunit = trim(strtolower($timeplus[1])); $endtime = @strtotime("+$tnumeric $tunit");
    }

    $cfilters = array();

    $cfilters['startdate'] = date(FN_MYSQL_DATE, 0);
    $cfilters['enddate'] = date(FN_MYSQL_DATE, $endtime);

    $cfilters['start']  = 0;
    $cfilters['count'] = FN_RESULTS_PER_PAGE;

    $cfilters['root_id'] = '0';

    $cfilters['order']     = 'ASC';
    $cfilters['orderby'] = 'fdate';

    $mindate = fn_OP_Pending::get_min_date($cfilters);

    $cfilters['startdate'] = date(FN_MYSQL_DATE, @strtotime($mindate));

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

<?php endif;?>

