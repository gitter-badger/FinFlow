<?php

if( !defined('FNPATH') ) exit;

include_once ( FNPATH . '/system/library/class.cronassistant.php' );

//---- calculate balance ---//
$sdate = (date('Y-m') . "-01");
$filters = array('startdate'=>$sdate);

$Total 		= fn_OP::get_sum($filters);
$Income		= fn_OP::get_sum(array_merge($filters, array('type'=>FN_OP_IN)));
$Outcome	= fn_OP::get_sum(array_merge($filters, array('type'=>FN_OP_OUT)));

$Balance = fn_OP::get_balance();

$Thresholds = array(
    'red'    => fn_Settings::get('balance_tsh_red', 0),
    'yellow' => fn_Settings::get('balance_tsh_yellow', 0),
    'blue'   => fn_Settings::get('balance_tsh_blue', 0),
    'green'  => fn_Settings::get('balance_tsh_green', 0)
);

$threshold_color = fn_Util::get_array_key_by_value_thresholds($Thresholds, $Balance);
$threshold_color = $threshold_color ? $threshold_color : '';

//---- calculate balance ---//

//---- get latest transactions ---//
$offset   = 0;
$per_page = FN_RESULTS_PER_PAGE;

$Transactions = fn_OP::get_operations($filters, $offset, $per_page);

$Currency = fn_Currency::get_default();
//---- get latest transactions ---//

//---- get cron jobs statuses ---//
$Cronjobs 		= array('/inc/cron/cron.exchangerates.php', '/inc/cron/cron.readmail.php');
$Cronupdates	= array();

foreach ($Cronjobs as $job){
	$timestamp = fn_CronAssistant::get_lastrun( FNPATH . $job ); if( $timestamp ) $Cronupdates[] = fn_UI::translate_date(date(FN_DATETIME_FORMAT, $timestamp));
}
//---- get cron jobs statuses ---//

?>

<div class="row">

	<div class="<?php fn_UI::main_container_grid_class(); ?>">

        <div class="panel panel-default">
            <div class="panel-body">
                <h3 class="balance treshold <?php echo $threshold_color; ?>">
                    Balan&#355;&#259;: <span class="value"> <?php echo is_object($Currency) ? $Currency->csymbol : ''; ?> <?php echo fn_Util::format_nr($Balance); ?> </span>
                </h3>
            </div>
        </div>


        <?php if ( count($Transactions) ): ?>

        <div class="panel panel-default">
            <div class="panel-heading">Raport pentru <?php echo fn_UI::translate_date(date(FN_MONTH_FORMAT)); ?></div>

			<table class="table table-responsive table-bordered list report">
				<tr>
					<td>Rulaj: </td>
					<td class="align-right"><?php echo $Currency->ccode; ?> <?php echo fn_Util::format_nr($Total); ?></td>
				</tr>
				<?php if ( !isset($filters['type']) or $filters['type'] == FN_OP_IN): ?>
				<tr>
					<td>Venit: </td>
					<td class="align-right"><?php echo $Currency->ccode; ?> <?php echo fn_Util::format_nr($Income); ?></td>
				</tr>
				<?php endif;?>
				<?php if ( !isset($filters['type']) or $filters['type'] == FN_OP_OUT): ?>
				<tr>
					<td>Cheltuieli: </td>
					<td class="align-right"><?php echo $Currency->ccode; ?> <?php echo fn_Util::format_nr($Outcome); ?></td>
				</tr>
				<?php endif; ?>
				<?php if ( !isset($filters['type']) ): ?>
				<tr class="highlight">
					<td>Balan&#355;a: </td>
					<td class="align-right">
						<strong> <?php echo $Currency->ccode; ?> <?php echo fn_Util::format_nr($Income - $Outcome); ?> </strong>
					</td>
				</tr>
				<?php endif; ?>
			</table>


		</div>

        <div class="panel panel-default">
            <div class="panel-heading">Ultimele tranzactii</div>

            <table class="table table-responsive table-striped list transactions">

                <tr>
                    <th>Tip</th>
                    <th>Suma</th>
                    <th>Moneda</th>
                    <th>Data</th>
                    <th>Etichete</th>
                    <th>&nbsp;</th>
                </tr>

                <?php foreach ($Transactions as $transaction): $currency = fn_Currency::get($transaction->currency_id); ?>
                    <tr>
                        <td>
                            <?php fn_UI::transaction_icon($transaction->optype); ?>
                        </td>
                        <td><?php echo fn_Util::format_nr( $transaction->value ); ?></td>
                        <td><?php echo $currency->ccode; ?></td>
                        <td><?php echo fn_UI::translate_date( date(FN_DAY_FORMAT, strtotime($transaction->sdate)) ); ?></td>
                        <td>
                            <?php $labels = fn_OP::get_labels($transaction->trans_id); $lc=0; if (count($labels))  foreach ($labels as $label): $lc++; ?>
                                <?php echo fn_UI::esc_html($label->title); ?><?php if ( $lc < count($labels) ) echo ", "; ?>
                            <?php endforeach;?>
                        </td>
                        <td class="align-center">
                            <a class="btn btn-default" title="<?php echo fn_UI::esc_attr( $transaction->comments ); ?>" onclick="fn_popup('<?php echo (FN_URL . "/snippets/transaction-details.php?id={$transaction->trans_id}"); ?>')">
                                <span class="fa fa-info-circle"></span>
                            </a>
                            <button class="btn btn-default" onclick="confirm_delete('<?php fn_UI::page_url('transactions', array_merge($_GET, array('del'=>$transaction->trans_id))); ?>')">
                                <span class="fa fa-remove"></span>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

        </div>

        <?php else:

            fn_UI::msg(sprintf('Nu am g&#259;sit tranzac&#355;ii pentru luna curent&#259;. <a href="%s">Adaug&#259; tranzac&#355;ii &rarr;</a>', 'index.php?p=transactions&t=add'), fn_UI::$MSG_NOTE);

        endif; ?>

        <?php if( count($Cronupdates) ): ?>

            <div class="panel panel-default">
                <div class="panel-heading">Ultimele actualiz&#259;ri f&#259;cute de cronjob-uri</div>

                <table class="table table-responsive list report">
                    <tr>
                        <td>Actualizare curs:</td>
                        <td class="align-right"><?php echo isset($Cronupdates[0]) ? $Cronupdates[0] : '-';  ?></td>
                    </tr>
                    <tr>
                        <td>Actualizare tranzac&#355;ii prin email:</td>
                        <td class="align-right"><?php echo isset($Cronupdates[1]) ? $Cronupdates[1] : '-';  ?></td>
                    </tr>
                </table>

            </div>
        <?php endif; ?>
		
	</div>
	
	<?php include_once ( FNPATH . '/snippets/sidebar.php' ); ?>
	
</div>