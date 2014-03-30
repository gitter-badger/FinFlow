<?php if ( !defined('FNPATH') ) exit(); global $Currency;

if( count($Transactions) ): ?>

    <?php if( $list_report != 'no' ): ?>
        <table class="list report" border="1">
            <tr>
                <td>Rulaj: </td>
                <td class="align-right"><?php echo $Currency->ccode; ?> <?php echo fn_Util::format_nr($Total); ?></td>
            </tr>
            <tr>
                <td>Venit: </td>
                <td class="align-right"><?php echo $Currency->ccode; ?> <?php echo fn_Util::format_nr($Income); ?></td>
            </tr>
            <tr>
                <td>Cheltuieli: </td>
                <td class="align-right"><?php echo $Currency->ccode; ?> <?php echo fn_Util::format_nr($Outcome); ?></td>
            </tr>
            <tr class="highlight">
                <td>Balan&#355;a: </td>
                <td class="align-right">
                    <strong> <?php echo $Currency->ccode; ?> <?php echo fn_Util::format_nr($Income - $Outcome); ?> </strong>
                </td>
            </tr>
        </table>

        <br class="clear"/>

    <?php endif; ?>

    <table class="list transactions" border="1">
        <tr>
            <th>ID</th>
            <th>Tip</th>
            <th>Suma</th>
            <th>Moneda</th>
            <th>Data</th>
            <th>Etichete</th>
            <th>&nbsp;</th>
        </tr>
        <?php foreach ($Transactions as $transaction):  $k++; $trclass= ( $k%2 == 0) ? 'even' : 'odd'; $currency = fn_Currency::get($transaction->currency_id); $meta = @unserialize($transaction->metadata); ?>
            <tr class="<?php echo $trclass; ?>">
                <td>#<?php echo $transaction->trans_id; ?></td>
                <td>
                    <img src="images/<?php echo $transaction->optype; ?>.png" title="<?php echo ($transaction->optype == FN_OP_IN) ? 'venit' : 'cheltuiala'; ?>" align="middle" alt="<?php echo $transaction->optype; ?>"/>
                </td>
                <td>
                    <?php
                        $trans_value = fn_Util::format_nr( $transaction->value ); echo isset($cfilters['enddate']) ? fn_OP_Pending::recurring_multiplier_display($transaction->recurring, $trans_value, $days, $months, $years): $trans_value ; ?>
                </td>
                <td><?php echo $currency->ccode; ?></td>
                <td><?php echo fn_UI::translate_date( date(FN_DAY_FORMAT, strtotime($transaction->fdate)) ); ?></td>
                <td>
                    <?php $labels = $meta['labels']; $lc=0; if (count($labels))  foreach ($labels as $label_id):$label = fn_Label::get_by($label_id, 'id');  $lc++; ?>
                        <?php echo fn_UI::esc_html($label->title); ?><?php if ( $lc < count($labels) ) echo ", "; ?>
                    <?php endforeach; ?>
                </td>
                <td>
                    <a class="btn" href="#nwhr"  onclick="fn_popup('<?php echo (FN_URL . "/snippets/pending-confirm.php?id={$transaction->trans_id}"); ?>');" title="confirma">
                        <span class="icon-ok"></span>
                    </a>
                    &nbsp;&nbsp;
                    <a class="btn" href="#nwhr" title="vezi detalii" onclick="fn_popup('<?php echo (FN_URL . "/snippets/transaction-details.php?id={$transaction->trans_id}"); ?>&t=pending')">
                        <span class="icon-info-sign"></span>
                    </a>
                    &nbsp;&nbsp;
                    <button class="btn" onclick="confirm_delete('<?php fn_UI::page_url('transactions', array_merge($_GET, array('del'=>$transaction->trans_id))); ?>')">
                        <span class="icon-remove"></span>
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p class="msg note">Nu am gasit tranzactii... .</p>
<?php endif; ?>