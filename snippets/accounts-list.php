<?php if ( !defined('FNPATH') ) exit(); ?>

<?php if( count($Accounts) ): ?>
    <table class="list currencies" border="1">
        <tr>
            <th>Emitent</th>
            <th>Slug</th>
            <th>Moneda</th>
            <th>IBAN</th>
            <th>SWIFT</th>
            <th>Balan&#355;a</th>
            <th>&nbsp;</th>
        </tr>
        <?php foreach($Accounts as $account): $k++; $trclass= ( $k%2 == 0) ? 'even' : 'odd'; $currency= fn_Currency::get($account->account_currency_id); ?>
            <tr class="<?php echo $trclass; ?>">
                <td>
                    <a href="<?php fn_UI::page_url('transactions', array('accounts'=>$account->account_id, 'sdate'=>'1970-01-01')); ?>">
                        <?php echo fn_UI::esc_html($account->holder_name); ?>
                    </a>
                </td>
                <td><?php echo $account->account_slug; ?></td>
                <td><?php echo $currency->ccode; ?></td>
                <td><?php echo $account->account_iban; ?></td>
                <td><?php echo $account->holder_swift; ?></td>
                <td>
                    <?php echo $currency->csymbol; ?> <?php echo fn_Util::format_nr($account->balance + $account->balance_won - $account->balance_spent); ?>
                </td>
                <td class="align-center">

                    <button class="btn" onclick="window.location.href='<?php fn_UI::page_url('accounts', array('t'=> 'edit', 'id'=>$account->account_id)); ?>';">
                        <span class="icon-pencil"></span>
                    </button>

                    <button class="btn" onclick="confirm_delete('<?php fn_UI::page_url('accounts', array('del'=>$account->account_id)); ?>')">
                        <span class="icon-remove"></span>
                    </button>

                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else :?>
    <?php fn_UI::msg(sprintf("Nu sunt conturi predefinite! Po&#355;i ad&#259;uga conturi folosind <a href=\"%s\">formularul de ad&#259;ugare</a>.", 'index.php?p=accounts&t=add'), fn_UI::$MSG_WARN); ?>
<?php endif;?>