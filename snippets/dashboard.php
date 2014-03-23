<?php

if( !defined('FNPATH') ) exit;

include_once ( FNPATH . '/inc/class.cronassistant.php' );

//---- calculate balance ---//
$sdate = (date('Y-m') . "-01");
$filters = array('startdate'=>$sdate);

$Total 		= fn_OP::get_sum($filters);
$Income		= fn_OP::get_sum(array_merge($filters, array('type'=>FN_OP_IN)));
$Outcome	= fn_OP::get_sum(array_merge($filters, array('type'=>FN_OP_OUT)));

$Balance = fn_OP::get_balance();

$Thresholds = array(
    'red' => fn_Settings::get('balance_tsh_red', 0),
    'yellow' => fn_Settings::get('balance_tsh_yellow', 0),
    'blue' => fn_Settings::get('balance_tsh_blue', 0),
    'green' => fn_Settings::get('balance_tsh_green', 0)
);

$threshold_color = fn_Util::get_array_key_by_value_thresholds($Thresholds, $Balance);
$threshold_color = $threshold_color ? $threshold_color : '';

//---- calculate balance ---//

//---- get latest transactions ---//
$start = 0;
$count= FN_RESULTS_PER_PAGE;

$Transactions = fn_OP::get_operations($filters, $start, $count);

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

<div class="row content">
	<div class="span10">

        <h4 class="balance treshold <?php echo $threshold_color; ?>">
            Balan&#355;&#259;: <span class="value"> <?php echo $Currency->csymbol; ?> <?php echo fn_Util::format_nr($Balance); ?> </span>
        </h4>

		<h4 class="page-heading">Raport pentru <?php echo fn_UI::translate_date(date(FN_MONTH_FORMAT)); ?></h4>
		
		<?php if ( count($Transactions) ): ?>
			
			<table class="list report" border="1">
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
			
			<br class="clear"/>
			
			<table class="list transactions" border="1">
				<tr>
					<th>Tip</th>
					<th>Suma</th>
					<th>Moneda</th>
					<th>Data</th>
					<th>Etichete</th>
					<th>&nbsp;</th>
				</tr>
				<?php foreach ($Transactions as $transaction):  $k++; $trclass= ( $k%2 == 0) ? 'even' : 'odd'; $currency = fn_Currency::get($transaction->currency_id); ?>
				<tr class="<?php echo $trclass; ?>">
					<td>
                        <img src="images/<?php echo $transaction->optype; ?>.png" align="middle" title="<?php echo ($transaction->optype == FN_OP_IN) ? 'venit' : 'cheltuiala'; ?>" alt="<?php echo $transaction->optype; ?>"/>
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
						<button class="btn" onclick="confirm_delete('<?php fn_UI::page_url('transactions', array_merge($_GET, array('del'=>$transaction->trans_id))); ?>')">
							<span class="icon-remove"></span>
						</button>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
			
			<p>
				<a href="<?php fn_UI::page_url('transactions'); ?>">vezi toate tranzac&#355;iile &rarr;</a>
			</p>
			
			<?php else: 
			
				fn_UI::msg(sprintf('Nu am g&#259;sit tranzac&#355;ii pentru luna curent&#259;. <a href="%s">Adaug&#259; tranzac&#355;ii &rarr;</a>', 'index.php?p=transactions&t=add'), fn_UI::$MSG_NOTE);
				
			endif; ?>
		
		<br class="clear"/>

        <?php if( count($Cronupdates) ): ?>

		<h4 class="page-heading">Ultimele actualiz&#259;ri f&#259;cute de cronjob-uri</h4>
		
		<table class="list report" border="1">
			<tr>
				<td>Actualizare curs:</td>
				<td class="align-right"><?php echo $Cronupdates[0];  ?></td>
			</tr>
			<tr>
				<td>Actualizare tranzac&#355;ii prin email:</td>
				<td class="align-right"><?php echo $Cronupdates[1];  ?></td>
			</tr>
		</table>

        <?php endif; ?>
		
	</div>
	
	<?php include_once ( FNPATH . '/snippets/sidebar.php' ); ?>
	
</div>