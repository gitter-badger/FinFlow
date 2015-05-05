<?php if ( !defined('FNPATH') ) exit();

use FinFlow\UI;
use FinFlow\Util;
use FinFlow\Accounts;
use FinFlow\Currency;
use FinFlow\Label;
use FinFlow\OP;

?>

<div class="panel panel-default">

	<div class="panel-heading">
		<h4>Filtreaza tranzactii</h4>
	</div>

	<div class="panel-body">

		<form class="form form-horizontal filter" action="<?php UI::url('transactions/list'); ?>" name="filter-transactions-form" id="filterTransactionsForm" method="get">

			<div class="form-group">
				<label class="control-label col-lg-3">Din: </label>
				<div class="col-lg-3">

					<?php if ( $month > 0 ): ?>
						<select class="form-control" id="sday">
							<?php foreach ($days as $d):?>
								<option value="<?php echo Util::add_leading_zeros($d); ?>" <?php echo UI::selected_or_not($d, intval($day)); ?>>
									<?php echo $d; ?>
								</option>
							<?php endforeach;?>
						</select>
					<?php endif; ?>

				</div>

				<div class="col-lg-3">

					<select class="form-control" id="smonth">
						<?php  foreach ($months as $m): if ($month == $m) $selected='selected';?>
							<option value="<?php echo Util::add_leading_zeros($m);?>" <?php echo UI::selected_or_not($m, $month); ?>>
								<?php echo UI::get_translated_month($m); ?>
							</option>
						<?php endforeach;?>
					</select>

				</div>

				<div class="col-lg-3">

					<select class="form-control" id="syear">
						<?php foreach ($years as $y): ?>
							<option value="<?php echo $y;?>" <?php echo UI::selected_or_not($y, $year); ?>>
								<?php echo $y; ?>
							</option>
						<?php endforeach;?>
					</select>

				</div>
			</div>

			<div class="form-group">

				<label class="control-label col-lg-3">P&#226;n&#259;:</label>

				<div class="col-lg-3">
					<?php if ( $month > 0 ): if (empty($eday)) $eday = $edays[count($edays)-1]; ?>
						<select class="form-control" id="eday">
							<?php  foreach ($edays as $d):?>
								<option value="<?php echo Util::add_leading_zeros($d); ?>" <?php echo UI::selected_or_not($d, $eday); ?>>
									<?php echo $d; ?>
								</option>
							<?php endforeach;?>
						</select>
					<?php endif; ?>
				</div>

				<div class="col-lg-3">

					<select class="form-control" id="emonth">
						<?php foreach ($months as $m): if ($month == $m) $selected='selected';?>
							<option value="<?php echo Util::add_leading_zeros($m); ?>" <?php echo UI::selected_or_not($m, $emonth); ?>>
								<?php echo UI::get_translated_month($m); ?>
							</option>
						<?php endforeach;?>
					</select>

				</div>

				<div class="col-lg-3">
					<select class="form-control" id="eyear">
						<?php foreach ($years as $y):?>
							<option value="<?php echo $y;?>" <?php echo UI::selected_or_not($y, $eyear); ?>>
								<?php echo $y; ?>
							</option>
						<?php endforeach;?>
					</select>
				</div>

			</div>

			<?php $Labels = Label::get_all(0, 999); if ( count($Labels) ): ?>
				<div class="form-group">
					<label class="control-label col-lg-3" for="labels">Etichete:</label>
					<div class="col-lg-9">
						<select class="form-control" name="labels" id="labels" size="5" multiple="multiple">
							<?php foreach ($Labels as $label): ?>
								<option value="<?php echo $label->label_id; ?>" <?php echo UI::selected_or_not($label->label_id, $labels); ?>>
									<?php echo UI::esc_html($label->title); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			<?php endif;?>

			<?php if( Accounts::has_accounts() ): ?>
				<div class="form-group">
					<label class="control-label col-lg-3" for="accounts">Conturi:</label>
					<div class="col-lg-9">
						<?php $Accounts = Accounts::get_all(0, 999); if ( count($Accounts) ): ?>
							<select class="form-control" name="accounts" id="accounts" size="5" multiple="multiple">
								<?php foreach ($Accounts as $account): ?>
									<option value="<?php echo $account->account_id; ?>" <?php echo UI::selected_or_not($account->account_id, $accounts); ?>>
										<?php echo UI::esc_html($account->holder_name); ?>
									</option>
								<?php endforeach; ?>
							</select>
						<?php endif;?>
					</div>
				</div>
			<?php endif; ?>

			<div class="form-group">
				<label class="control-label col-lg-3" for="currency_id">Moneda:</label>
				<div class="col-lg-9">
					<?php $Currencies = Currency::get_all(FALSE); if (count($Currencies)):?>
						<select class="form-control" name="currency_id" id="currency_id">
							<option value="0">- toate - </option>
							<?php foreach ($Currencies as $currency): ?>
								<option value="<?php echo $currency->currency_id; ?>" <?php echo UI::selected_or_not($currency->currency_id, $currency_id); ?>>
									<?php echo $currency->ccode; ?>
								</option>
							<?php endforeach; ?>
						</select>
					<?php endif;?>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3" for="type">Tip:</label>
				<div class="col-lg-9">
					<select class="form-control" name="type" id="type">
						<option value="0">- toate - </option>
						<option value="<?php echo OP::TYPE_IN; ?>"  <?php echo UI::selected_or_not(OP::TYPE_IN, $type); ?>>venit</option>
						<option value="<?php echo OP::TYPE_OUT; ?>" <?php echo UI::selected_or_not(OP::TYPE_OUT, $type); ?>>cheltuieli</option>
					</select>
				</div>
			</div>

			<input type="hidden" name="sdate" id="sdate" value="<?php echo UI::extract_get_val('sdate'); ?>" />
			<input type="hidden" name="edate" id="edate" value="<?php echo UI::extract_get_val('edate'); ?>"/>

			<div class="form-group">
				<div class="col-lg-offset-5 col-lg-7">
					<button class="btn btn-primary btn-submit" type="button" id="filterTransactionsBtn"> Filtreaza </button>
				</div>
			</div>

		</form>

	</div>

</div>