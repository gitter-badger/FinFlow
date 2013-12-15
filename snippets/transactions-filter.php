<?php if ( !defined('FNPATH') ) exit(); ?>
<form action="<?php fn_UI::page_url('transactions', array('t'=>'list')); ?>" class="filter" name="filter-transactions-form" id="filterTransactionsForm" method="get">
	
	<p>
	
		<label>Din: </label>
					
		<?php if ( $month > 0 ): ?>
			<select id="sday" style="width: auto;">
				<?php foreach ($days as $d):?>
				<option value="<?php echo fn_Util::add_leading_zeros($d); ?>" <?php echo fn_UI::selected_or_not($d, intval($day)); ?>><?php echo $d; ?></option>
				<?php endforeach;?>
			</select>
		<?php endif; ?>
					
		<select id="smonth">
			<?php  foreach ($months as $m): if ($month == $m) $selected='selected';?>
			<option value="<?php echo fn_Util::add_leading_zeros($m);?>" <?php echo fn_UI::selected_or_not($m, $month); ?>><?php echo fn_UI::get_translated_month($m); ?></option>
			<?php endforeach;?>
		</select>
					
		<select id="syear">
			<?php foreach ($years as $y): ?>
			<option value="<?php echo $y;?>" <?php echo fn_UI::selected_or_not($y, $year); ?>><?php echo $y; ?></option>
			<?php endforeach;?>
		</select>
				
	</p>
	
	<p>			
		<label>P&#226;n&#259;: </label>
					
		<?php if ( $month > 0 ): if (empty($eday)) $eday = $edays[count($edays)-1]; ?>
			<select id="eday" style="width: auto;">
				<?php  foreach ($edays as $d):?>
				<option value="<?php echo fn_Util::add_leading_zeros($d); ?>" <?php echo fn_UI::selected_or_not($d, $eday); ?>><?php echo $d; ?></option>
				<?php endforeach;?>
			</select>
		<?php endif; ?>
					
		<select id="emonth">
			<?php foreach ($months as $m): if ($month == $m) $selected='selected';?>
			<option value="<?php echo fn_Util::add_leading_zeros($m); ?>" <?php echo fn_UI::selected_or_not($m, $emonth); ?>><?php echo fn_UI::get_translated_month($m); ?></option>
			<?php endforeach;?>
		</select>
					
		<select id="eyear">
			<?php foreach ($years as $y):?>
			<option value="<?php echo $y;?>" <?php echo fn_UI::selected_or_not($y, $eyear); ?>><?php echo $y; ?></option>
			<?php endforeach;?>
		</select>
	</p>

    <?php $Labels = fn_Label::get_all(0, 999); if ( count($Labels) ): ?>
	<p>
		<label for="labels">Etichete:</label>
		<select name="labels" id="labels" size="3" multiple="multiple">
			<?php foreach ($Labels as $label): ?>
			<option value="<?php echo $label->label_id; ?>" <?php echo fn_UI::selected_or_not($label->label_id, $labels); ?>>
                <?php echo fn_UI::esc_html($label->title); ?>
            </option>
			<?php endforeach; ?>
		</select>
	</p>
    <?php endif;?>

    <?php if( fn_Accounts::has_accounts() ): ?>
    <p>
        <label for="accounts">Conturi:</label>
        <?php $Accounts = fn_Accounts::get_all(0, 999); if ( count($Accounts) ): ?>
            <select name="accounts" id="accounts" size="3" multiple="multiple">
                <?php foreach ($Accounts as $account): ?>
                    <option value="<?php echo $account->account_id; ?>" <?php echo fn_UI::selected_or_not($account->account_id, $accounts); ?>>
                        <?php echo fn_UI::esc_html($account->holder_name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endif;?>
    </p>
    <?php endif; ?>
	
	<p>
		<label for="currency_id">Moneda:</label>
					
		<?php $Currencies = fn_Currency::get_all(FALSE); if (count($Currencies)):?>
		<select name="currency_id" id="currency_id">
			<option value="0">- toate - </option>
			<?php foreach ($Currencies as $currency): ?>
			<option value="<?php echo $currency->currency_id; ?>" <?php echo fn_UI::selected_or_not($currency->currency_id, $currency_id); ?>><?php echo $currency->ccode; ?></option>
			<?php endforeach; ?>
		</select>
		<?php endif;?>
					
	</p>
	
	<p>
		<label for="type">Tip:</label>
				
		<select name="type" id="type">
			<option value="0">- toate - </option>
			<option value="<?php echo FN_OP_IN; ?>"  <?php echo fn_UI::selected_or_not(FN_OP_IN, $type); ?>>venit</option>
			<option value="<?php echo FN_OP_OUT; ?>" <?php echo fn_UI::selected_or_not(FN_OP_OUT, $type); ?>>cheltuieli</option>
		</select>
					
	</p>
				
	<input type="hidden" name="sdate" id="sdate" value="<?php echo fn_UI::extract_get_val('sdate'); ?>" />
	<input type="hidden" name="edate" id="edate" value="<?php echo fn_UI::extract_get_val('edate'); ?>"/>
	
	<p>			
		<button class="btn" type="button" id="filterTransactionsBtn"> Filtreaza </button>
	</p>
</form>
