<?php if ( !defined('FNPATH') ) exit();

use FinFlow\Util;
use FinFlow\UI;
use FinFlow\Currency;
use FinFlow\OP;
use FinFlow\OP_Pending;

$list_report = empty($list_report) ? 'yes' : $list_report;

if( count($Transactions) ):  ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>
                <em>P&#226;n&#259; la <?php echo UI::translate_date( date(FN_DAY_FORMAT, $endtime) ); ?></em>
            </h4>
        </div>

        <?php if( $list_report != 'no' ): ?>

            <table class="table table-responsive table-striped list report">
                <tr>
                    <td>Rulaj: </td>
                    <td class="align-right"><?php echo $Currency->ccode; ?> <?php echo Util::format_nr($Total); ?></td>
                </tr>
                <tr>
                    <td>Venit: </td>
                    <td class="align-right"><?php echo $Currency->ccode; ?> <?php echo Util::format_nr($Income); ?></td>
                </tr>
                <tr>
                    <td>Cheltuieli: </td>
                    <td class="align-right"><?php echo $Currency->ccode; ?> <?php echo Util::format_nr($Outcome); ?></td>
                </tr>
                <tr class="highlight">
                    <td>Balan&#355;a: </td>
                    <td class="align-right">
                        <strong> <?php echo $Currency->ccode; ?> <?php echo Util::format_nr($Income - $Outcome); ?> </strong>
                    </td>
                </tr>
            </table>

        <?php endif; ?>

    </div>

    <div class="panel panel-default">
        <table class="table table-responsive table-striped list transactions">
            <tr>
                <th>ID</th>
                <th>Tip</th>
                <th>Suma</th>
                <th>Moneda</th>
                <th>Data</th>
                <th>Etichete</th>
                <th>&nbsp;</th>
            </tr>
            <?php foreach ($Transactions as $transaction):  $currency = Currency::get($transaction->currency_id); $meta = @unserialize($transaction->metadata); ?>
                <tr>
                    <td>#<?php echo $transaction->trans_id; ?></td>
                    <td> <?php UI::transaction_icon( $transaction->optype ); ?></td>
                    <td>
                        <?php echo $transaction->period_instances; ?> x <?php echo Util::format_nr( $transaction->value ); ?>
                    </td>
                    <td><?php echo $currency->ccode; ?></td>
                    <td><?php echo UI::translate_date( date(FN_DAY_FORMAT, strtotime($transaction->fdate)) ); ?></td>
                    <td>
                        <?php $labels = isset($meta['labels']) ? $meta['labels'] : array(); $lc=0; if (count($labels))  foreach ($labels as $label_id):$label = Label::get_by($label_id, 'id');  $lc++; ?>
                            <?php echo UI::esc_html($label->title); ?><?php if ( $lc < count($labels) ) echo ", "; ?>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <a class="btn btn-default" href="#nwhr"  onclick="fn_popup('<?php echo (FN_URL . "/snippets/pending-confirm.php?id={$transaction->trans_id}"); ?>');" title="confirma">
                            <span class="fa fa-check"></span>
                        </a>
                        <a class="btn btn-default" href="#nwhr" title="vezi detalii" onclick="fn_popup('<?php echo (FN_URL . "/snippets/transaction-details.php?id={$transaction->trans_id}"); ?>&t=pending')">
                            <span class="fa fa-info-circle"></span>
                        </a>
                        <button class="btn btn-default" onclick="confirm_delete('<?php UI::page_url('transactions/pending', array_merge($_GET, array('del'=>$transaction->trans_id))); ?>')">
                            <span class="fa fa-remove"></span>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

<?php else: ?>
    <div class="alert alert-info">Nu am gasit tranzactii... .</div>
<?php endif; ?>