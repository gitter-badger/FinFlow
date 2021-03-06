<?php if( !defined('FNPATH') ) exit;

use FinFlow\UI;
use FinFlow\Util;

UI::show_errors($errors); ?>

<?php if (count($defaultCurrency) and isset($defaultCurrency->ccode)):?>
    <div class="panel panel-default">
        <div class="panel-body">
            <h4><em>Default currency: <?php echo $defaultCurrency->ccode; ?></em></h4>
        </div>
    </div>
<?php else:

    UI::msg(

	        sprintf('Nu ai definit&#259; o moned&#259; implicit&#259;!
					 Verific&#259; set&#259;rile pentru <a href=\"%s\">parserul automat al cursului valutar</a>
					 sau adaug&#259; mai jos o moned&#259; cu rata de schimb 1.', UI::url('settings/parsers/exchange-rates', array(), false)
	        ),

	    UI::MSG_WARN

    );

endif;?>

<?php if( count($Currencies) ): $k = 0; ?>
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
                    <td><?php echo UI::esc_html($currency->cname); ?></td>
                    <td><?php echo $currency->csymbol; ?></td>
                    <td><?php echo $currency->ccode; ?></td>
                    <td><?php echo Util::format_nr($currency->cexchange, 4); ?></td>
                    <td class="align-center">
                        <button class="btn btn-default" onclick="confirm_delete('<?php UI::page_url('currencies', array('del'=>$currency->currency_id)); ?>')">
                            <span class="fa fa-remove"></span>
                        </button>
                    </td>
                </tr>
            <?php endforeach;?>
        </table>
    </div>
<?php else: ?>

	<div class="alert alert-warning">
		<p>
			<?php __e('There are no currencies to show. Check your settings for the <a href="%1s">exchange rates parser</a> or add some curencies using <a href="%2s">the form</a>.', '#TODO', UI::url('currencies/add/', array(), false)); //TODO add missing links ?>
		</p>
	</div>

<?php endif; ?>