<?php if( !defined('FNPATH') ) exit; global $Currencies, $errors; fn_UI::show_errors($errors); ?>

<?php if (count($defaultCurrency) and isset($defaultCurrency->ccode)):?>
    <div class="panel panel-default">
        <div class="panel-body">
            <h4><em>Moneda implicit&#259;: <?php echo $defaultCurrency->ccode; ?></em></h4>
        </div>
    </div>
<?php else:
    fn_UI::msg(sprintf("Nu ai definit&#259; o moned&#259; implicit&#259;! Verific&#259; set&#259;rile pentru <a href=\"%s\">parserul automat al cursului valutar</a> sau adaug&#259; mai jos o moned&#259; cu rata de schimb 1.", 'index.php?p=settings&t=exrparser'), fn_UI::$MSG_WARN);
endif;?>

<?php if( count($Currencies) ): $k = 0;?>
    <div class="panel panel-default">
        <table class="table table-responsive table-striped list currencies"">
            <tr>
                <th>Moneda</th>
                <th>Simbol</th>
                <th>Cod</th>
                <th>Curs</th>
                <th>&nbsp;</th>
            </tr>
            <?php foreach ($Currencies as $currency): ?>
                <tr>
                    <td><?php echo fn_UI::esc_html($currency->cname); ?></td>
                    <td><?php echo $currency->csymbol; ?></td>
                    <td><?php echo $currency->ccode; ?></td>
                    <td><?php echo fn_Util::format_nr($currency->cexchange, 4); ?></td>
                    <td class="align-center">
                        <button class="btn btn-default" onclick="confirm_delete('<?php fn_UI::page_url('currencies', array('del'=>$currency->currency_id)); ?>')">
                            <span class="fa fa-remove"></span>
                        </button>
                    </td>
                </tr>
            <?php endforeach;?>
        </table>
    </div>
<?php else:

        fn_UI::msg(sprintf("Nu sunt monede predefinite. Verific&#259; set&#259;rile pentru <a href=\"%1s\">parserul automat al cursului valutar</a> sau <a href=\"%2s\">adaug&#259; c&#226;teva monede</a> folosind formularul de ad&#259;ugare.", 'index.php?p=settings&t=exrparser', 'index.php?p=currencies&t=add'), fn_UI::$MSG_WARN);

endif; ?>