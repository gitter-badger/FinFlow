<?php

if ( !defined('FNPATH') ) exit();

use FinFlow\OP;
use FinFlow\Util;
use FinFlow\UI;
use FinFlow\Currency;

$Total 	= OP::get_sum($filters);

$Income	= OP::get_sum(array_merge($filters, array('type'=>OP::TYPE_IN)));
$Outcome= OP::get_sum(array_merge($filters, array('type'=>OP::TYPE_OUT)));

$Transactions = OP::get_operations($filters, $start, $count);

?>

<?php if ( count($Transactions) ): ?>

    <div class="panel panel-default">

        <div class="panel-heading">
	        <h4>
                Raport <?php echo $report_period; ?> <?php echo OP::get_filter_readable_string($filters); ?>
                <a href="#"  data-toggle="collapse" data-target="#reportInfo" class="pull-right hide-panel-table"><b class="fa fa-chevron-up"></b></a>
	        </h4>
        </div>

	    <table class="table table-striped list report" id="reportInfo">
		    <tr>
			    <td>Rulaj: </td>
			    <td class="align-right"><?php echo $Currency->ccode; ?> <?php echo Util::format_nr($Total); ?></td>
		    </tr>
		    <?php if ( ! isset($filters['type']) or $filters['type'] == OP::TYPE_IN): ?>
			    <tr>
				    <td>Venit: </td>
				    <td class="align-right"><?php echo $Currency->ccode; ?> <?php echo Util::format_nr($Income); ?></td>
			    </tr>
		    <?php endif;?>
		    <?php if ( ! isset($filters['type']) or $filters['type'] == OP::TYPE_OUT): ?>
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
        <table class="table table-striped list transactions" id="transactionsList">
            <tr>
                <th><?php __e('ID'); ?></th>
                <th><?php __e('Type'); ?></th>
                <th><?php __e('Value'); ?></th>
                <th><?php __e('Currency'); ?></th>
                <th><?php __e('Date'); ?></th>
                <th><?php __e('Labels'); ?></th>
                <th class="td-col-actions">&nbsp;</th>
            </tr>
            <?php foreach ($Transactions as $transaction):

	            $currency = Currency::get($transaction->currency_id);
	            $labels   = ! empty($transaction->labels) ? @explode(',', $transaction->labels) : array();

	        ?>
                <tr>
                    <td>#<?php echo $transaction->trans_id; ?></td>
                    <td><?php UI::transaction_icon( $transaction->optype ) ?></td>
                    <td><?php echo Util::format_nr( $transaction->value ); ?></td>
                    <td><?php echo $currency->ccode; ?></td>
                    <td><?php echo UI::translate_date( date(FN_DAY_FORMAT, strtotime($transaction->sdate)) ); ?></td>
                    <td>
                        <?php $lc=0; if ( count($labels) ) foreach ($labels as $label): $lc++; ?>
                            <?php echo UI::esc_html($label); ?><?php if ( $lc < count($labels) ) echo ", "; ?>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <a class="btn btn-default" onclick="fn_popup('<?php UI::url('transactions/detail', array('id'=>$transaction->trans_id)); ?>')">
                            <span class="fa fa-info-circle"></span>
                        </a>
                        <button class="btn btn-default" onclick="confirm_delete('<?php UI::url('transactions', array_merge($_GET, array('del'=>$transaction->trans_id))); ?>')">
                            <span class="fa fa-remove"></span>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

    </div>

    <div class="pagination-bottom">
        <?php $total = OP::get_total($filters); if ( $total > $count ): ?>
            <ul class="pagination"><?php UI::pagination($total, $count, $start, UI::page_url('transactions', $pagevars, false)); ?></ul>
        <?php endif;?>
    </div>

<?php else: $month = UI::get_translated_month($month); if ($day > 0) $month = ($day . " {$month}"); ?>

    <div class="alert alert-info">
        Nu am gasit tranzac&#355;ii pentru <?php echo $month; ?> <?php echo $year; ?>.
        <a href="<?php UI::url('transactions/add'); ?>">Adaug&#259; &rarr;</a> sau <a href="#">importa tranzactii</a>.
    </div>

<?php endif; ?>