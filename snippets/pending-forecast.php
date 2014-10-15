<?php
/**
 * FinFlow 1.0 - Pending transactions forecast
 * @author Adrian S. (http://www.gridwave.co.uk/)
 * @version 1.0
 */

$_GET['unit'] = isset($_GET['unit']) ? $_GET['unit'] : 'months';

$unit           = isset($_GET['unit']) ? trim($_GET['unit']) : 'months';
$unit_val     = isset($_GET['unit_value']) ? intval($_GET['unit_value']) : 0;
$exclude     = isset($_GET['exclude']) ? trim($_GET['exclude']) : null;

if( $unit and $unit_val ){

    $timespan = "{$unit_val} {$unit}";

    $timeplus = @explode(' ', $timespan); $tnumeric = intval( $timeplus[0] ); $tunit = trim(strtolower($timeplus[1]));

    $endtime  = @strtotime("+$tnumeric $tunit");
    $starttime = time();

    $cfilters = array('enddate'=>date(FN_MYSQL_DATE, $endtime), 'root_id'=>'0');

    //-- figure out the startdate ---//

    $starttime = ( @strtotime( fn_OP_Pending::get_min_date($cfilters) ) - 86400 );

    if( $starttime > time() ) $starttime = time();

    $cfilters['startdate'] = date(FN_MYSQL_DATE, $starttime);

    //-- figure out the startdate ---//

    $Income = $Outcome = 0;

    if( $exclude !=  FN_OP_IN )     $Income   = fn_OP_Pending::get_period_sum( array_merge($cfilters, array('type'=>FN_OP_IN)) );
    if( $exclude !=  FN_OP_OUT ) $Outcome = fn_OP_Pending::get_period_sum( array_merge($cfilters, array('type'=>FN_OP_OUT)) );

    $BalancePending = ( $Income - $Outcome );
    $BalanceCurrent = fn_OP::get_balance();

    $Balance = ( $BalanceCurrent + $BalancePending );

    $TotalIncome    = $BalanceCurrent + $Income;
    $TotalOutcome = $Outcome;

    $Total = ( $TotalIncome + abs($Outcome) );

    $percent = 100;

}

?>

<?php if( empty($unit_val) ):  ?>

<form class="form form-horizontal forecast" action="<?php fn_UI::page_url('transactions', array('t'=>'pending', 'forecast'=>1)); ?>" name="forecast-balance-form" id="forecastBalanceForm" method="get">

    <?php fn_UI::form_hidden_fields($_GET, array('unit_value', 'unit', 'exclude')) ?>

    <div class="form-group">
        <label class="col-lg-3 control-label" for="unit_value">Perioada:</label>
        <div class="col-lg-2">
            <input type="number" name="unit_value" id="unit_value" maxlength="4" class="form-control" min="1" placeholder="12" value="<?php echo fn_UI::extract_post_val('unit_value', 6); ?>"/>
        </div>
        <div class="col-lg-4">
            <select class="form-control" name="unit" id="unit">
                <option value="days" <?php echo fn_UI::selected_or_not('days', $_GET['unit']); ?>>zile</option>
                <option value="months" <?php echo fn_UI::selected_or_not('months', $_GET['unit']); ?>>luni</option>
                <option value="years" <?php echo fn_UI::selected_or_not('years', $_GET['unit']); ?>>ani</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3" for="exclude">Exclude:</label>
        <div class="col-lg-6">
            <select class="form-control" name="exclude" id="exclude">
                <option value="none"> - f&#259;ra excluderi - </option>
                <option value="<?php echo FN_OP_IN; ?>" <?php echo fn_UI::selected_or_not(FN_OP_IN, $_GET['exclude']); ?>>venit</option>
                <option value="<?php echo FN_OP_OUT; ?>" <?php echo fn_UI::selected_or_not(FN_OP_OUT, $_GET['exclude']); ?>>cheltuieli</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <div class="col-lg-offset-5 col-lg-7"> <button class="btn btn-primary" type="submit"> Aplica </button> </div>
    </div>

</form>

<?php else: $Currency = fn_Currency::get_default(); ?>

    <h4>
        <em>
            Prognoza p&#226;n&#259; la <?php echo fn_UI::translate_date( date(FN_DAY_FORMAT, $endtime) ); ?>

            <?php if( $exclude and ( $exclude != 'none' ) ): ?>
                f&#259;ra <?php echo($exclude == FN_OP_IN) ? 'venit' : 'cheltuieli'; ?>
            <?php endif; ?>
        </em>
    </h4>

    <div class="balance-forecast" id="balanceForecast">
        <div class="forecast-wrap">

            <?php if( $Balance > 0 ): $percent = $Outcome > 0 ? floatval( ( $Outcome * 100 ) / $TotalIncome  ) : 0; ?>

                <h4 class="trans-readable-filter">
                    Cheltuielile reprezint&#259; <?php echo fn_UI::format_nr($percent); ?>% din balan&#355;&#259;
                </h4>

                <div class="balance-available">
                    <?php if( $percent > 0 ): ?>
                        <div class="balance-required" style="width: <?php echo($percent) ?>%">
                            <div class="indicator-title">Necesari: <?php echo fn_UI::format_money( $Outcome ); ?></div>
                        </div>
                    <?php endif; ?>
                    <div class="indicator-title">
                        Disponibili: <?php echo fn_UI::format_money( $TotalIncome ); ?>
                    </div>
                </div>
            <?php else: $percent = $Outcome > 0 ? floatval( ( $TotalIncome * 100 ) / $TotalOutcome ) : 0; ?>

                <h4 class="trans-readable-filter">
                    Balan&#355;a acoper&#259; <?php echo fn_UI::format_nr($percent); ?>% din cheltuieli
                </h4>

                <div class="balance-required">
                    <?php if( $percent > 0 ): ?>
                        <div class="balance-available" style="width: <?php echo($percent) ?>%">
                            <div class="indicator-title">Disponibili: <?php echo fn_UI::format_money( $TotalIncome ); ?></div>
                        </div>
                    <?php endif; ?>
                    <div class="indicator-title">
                        Necesari: <?php echo fn_UI::format_money( $Outcome ); ?>
                    </div>
                </div>
            <?php endif; ?>

            <br class="clearfix"/>

        </div>

        <br class="clearfix"/>

    </div>

    <table class="table table-striped list report">
        <tr>
            <td>Rulaj: </td>
            <td class="align-right"><?php echo fn_UI::format_money( $Total ); ?></td>
        </tr>
        <?php if( $exclude !=  FN_OP_IN ): ?>
            <tr>
                <td>Venit: </td>
                <td class="align-right"><?php echo fn_UI::format_money( $Income ); ?></td>
            </tr>
        <?php endif;?>
        <?php if ( $exclude !=  FN_OP_OUT ): ?>
            <tr>
                <td>Cheltuieli: </td>
                <td class="align-right"><?php echo fn_UI::format_money($TotalOutcome); ?></td>
            </tr>
        <?php endif; ?>
        <tr class="highlight">
            <td>Balan&#355;a: </td>
            <td class="align-right">
                <strong><?php echo fn_UI::format_money( $Balance ); ?> </strong>
            </td>
        </tr>
    </table>

<?php endif;?>

