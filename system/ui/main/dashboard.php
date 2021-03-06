<?php

if( !defined('FNPATH') ) exit;

use FinFlow\Session;
use FinFlow\UI;
use FinFlow\TaskAssistant;
use FinFlow\OP;
use FinFlow\Settings;
use FinFlow\Util;
use FinFlow\Currency;

//---- calculate balance ---//
$sdate   = (date('Y-m') . "-01");
$filters = array('startdate'=>$sdate);

$Total 		= OP::get_sum($filters);
$Income		= OP::get_sum(array_merge($filters, array('type'=>OP::TYPE_IN)));
$Outcome	= OP::get_sum(array_merge($filters, array('type'=>OP::TYPE_OUT
)));

$Balance = OP::get_balance();

$Thresholds = array(
    'red'    => Settings::get('balance_tsh_red', 0),
    'yellow' => Settings::get('balance_tsh_yellow', 0),
    'blue'   => Settings::get('balance_tsh_blue', 0),
    'green'  => Settings::get('balance_tsh_green', 0)
);

$threshold_color = Util::get_array_key_by_value_thresholds($Thresholds, $Balance);
$threshold_color = $threshold_color ? $threshold_color : '';

//---- calculate balance ---//

//---- get latest transactions ---//
$offset   = 0;
$per_page = FN_RESULTS_PER_PAGE;

$Transactions = OP::get_operations($filters, $offset, $per_page);

$Currency = Currency::get_default();
//---- get latest transactions ---//

//---- get cron jobs statuses ---//
$CronTasks = TaskAssistant::get_registered_tasks();
//---- get cron jobs statuses ---//

?>

<div class="row">

	<div class="col-lg-8">

        <div class="panel panel-default panel-balance balance treshold <?php echo $threshold_color; ?>">
            <div class="panel-heading">
                <h3>
                    Balan&#355;&#259;:
	                <span class="value pull-right">
		                <?php echo is_object($Currency) ? $Currency->csymbol : ''; ?> <?php echo Util::format_nr($Balance); ?>
	                </span>
                </h3>
            </div>
        </div>


        <?php if ( count($Transactions) ): ?>

        <div class="panel panel-default">
            <div class="panel-heading">Raport pentru <?php echo UI::translate_date(date(FN_MONTH_FORMAT)); ?></div>

			<table class="table table-responsive table-bordered list report">
				<tr>
					<td>Rulaj: </td>
					<td class="align-right"><?php echo $Currency->ccode; ?> <?php echo Util::format_nr($Total); ?></td>
				</tr>
				<?php if ( !isset($filters['type']) or $filters['type'] == OP::TYPE_IN): ?>
				<tr>
					<td>Venit: </td>
					<td class="align-right"><?php echo $Currency->ccode; ?> <?php echo Util::format_nr($Income); ?></td>
				</tr>
				<?php endif;?>
				<?php if ( !isset($filters['type']) or $filters['type'] == OP::TYPE_OUT): ?>
				<tr>
					<td>Cheltuieli: </td>
					<td class="align-right"><?php echo $Currency->ccode; ?> <?php echo Util::format_nr($Outcome); ?></td>
				</tr>
				<?php endif; ?>
				<?php if ( !isset($filters['type']) ): ?>
				<tr class="highlight">
					<td>Balan&#355;a: </td>
					<td class="align-right">
						<strong> <?php echo $Currency->ccode; ?> <?php echo Util::format_nr($Income - $Outcome); ?> </strong>
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

                <?php foreach ($Transactions as $transaction): $currency = Currency::get($transaction->currency_id); ?>
                    <tr>
                        <td>
                            <?php UI::transaction_icon($transaction->optype); ?>
                        </td>
                        <td><?php echo Util::format_nr( $transaction->value ); ?></td>
                        <td><?php echo $currency->ccode; ?></td>
                        <td><?php echo UI::translate_date( date(FN_DAY_FORMAT, strtotime($transaction->sdate)) ); ?></td>
                        <td>
                            <?php $labels = OP::get_labels($transaction->trans_id); $lc=0; if (count($labels))  foreach ($labels as $label): $lc++; ?>
                                <?php echo UI::esc_html($label->title); ?><?php if ( $lc < count($labels) ) echo ", "; ?>
                            <?php endforeach;?>
                        </td>
                        <td class="align-center">
                            <a class="btn btn-default" title="<?php echo UI::esc_attr( $transaction->comments ); ?>" onclick="fn_popup('<?php UI::url('transactions/detail', array('id'=>$transaction->trans_id)); ?>')">
                                <span class="fa fa-info-circle"></span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

        </div>

        <?php else:

            UI::msg(sprintf('Nu am g&#259;sit tranzac&#355;ii pentru luna curent&#259;. <a href="%s">Adaug&#259; tranzac&#355;ii &rarr;</a>', UI::url('/transactions/add', null, false)), UI::MSG_NOTE);

        endif; ?>

        <?php if( count($CronTasks) ): ?>

            <div class="panel panel-default">
                <div class="panel-heading">Ultimele actualiz&#259;ri f&#259;cute de cronjob-uri</div>

                <table class="table table-responsive list report">
	                <?php foreach ( $CronTasks as $task ) : ?>
	                <tr>
                        <td><?php echo UI::esc_html( $task->task_name ); ?></td>
                        <td class="align-right"><?php echo Util::nicetime( $task->last_activity ); ?></td>
                    </tr>
		            <?php endforeach ?>
                </table>

            </div>
        <?php endif; ?>

	</div>

	<?php UI::component('main/sidebar'); ?>

</div>