<?php
/**
 * FinFlow 1.0 - Pending Transactions Edit file
 * @author Adrian S. (http://adrian.silimon.eu/)
 * @version 1.0
 */

if ( count($RootTransactions) ): ?>

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
            <th>ID</th>
            <th>Tip</th>
            <th>Suma</th>
            <th>Moneda</th>
            <th>Data</th>
            <th>Etichete</th>
            <th>&nbsp;</th>
        </tr>
        <?php foreach ($Transactions as $transaction):  $k++; $trclass= ( $k%2 == 0) ? 'even' : 'odd'; $currency = fn_Currency::get($transaction->currency_id); ?>
            <tr class="<?php echo $trclass; ?>">
                <td>#<?php echo $transaction->trans_id; ?></td>
                <td>
                    <img src="images/<?php echo $transaction->optype; ?>.png" title="<?php echo ($transaction->optype == FN_OP_IN) ? 'venit' : 'cheltuiala'; ?>" align="middle" alt="<?php echo $transaction->optype; ?>"/>
                </td>
                <td><?php echo fn_Util::format_nr( $transaction->value ); ?></td>
                <td><?php echo $currency->ccode; ?></td>
                <td><?php echo fn_UI::translate_date( date(FN_DAY_FORMAT, strtotime($transaction->sdate)) ); ?></td>
                <td>
                    <?php $labels = fn_OP::get_labels($transaction->trans_id); $lc=0; if (count($labels))  foreach ($labels as $label): $lc++; ?>
                        <?php echo fn_UI::esc_html($label->title); ?><?php if ( $lc < count($labels) ) echo ", "; ?>
                    <?php endforeach;?>
                </td>
                <td>
                    <a class="btn" href="#nwhr" title="<?php echo fn_UI::esc_attr( $transaction->comments ); ?>" onclick="fn_popup('<?php echo (FN_URL . "/snippets/transaction-details.php?id={$transaction->trans_id}"); ?>')">
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

    <div class="pagination">
        <?php $total = fn_OP::get_total($filters); if ( $total > $count ):?>
            <ul><?php fn_UI::pagination($total, $count, $_GET['pag'], fn_UI::page_url('transactions', $pagevars, FALSE)); ?></ul>
        <?php endif;?>
    </div>

<?php else: $month = fn_UI::get_translated_month($month); if ($day > 0) $month = ($day . " {$month}"); ?>

    <p class="msg note">
        Nu am gasit tranzac&#355;ii in asteptare. <a href="<?php fn_UI::page_url('transactions', array('t'=>'add')); ?>">Adaug&#259; &rarr;</a>
    </p>

<?php endif; ?>