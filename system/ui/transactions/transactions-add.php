<?php
/**
 * Renders transaction add page
 */

if ( !defined('FNPATH') ) exit();

use FinFlow\UI;
use FinFlow\OP;
use FinFlow\Currency;
use FinFlow\Contacts;
use FinFlow\Label;
use FinFlow\Accounts;
use FinFlow\Util;

//TODO... move logic for adding a transaction here

$max_filesize     = Util::get_max_upload_filesize();
$max_filesize_fmt = Util::fmt_filesize( $max_filesize );

$unsafeBrowserFileWarn = 'Tipul de fi&#351;ier ales nu are suport nativ in navigatoarele web.';
$sizeLimiteExceedFile  = ( 'Fi&#351;ierul ales este prea mare pentru a fi &#238;ncarc&#259;t. Marimea maxima admisa este ' . $max_filesize_fmt . '.' );


UI::show_errors($errors); UI::show_notes($notices); UI::show_warnings($warnings); ?>

<div class="panel panel-default">

	<div class="panel-heading">
		<h4><i class="fa fa-plus-circle"></i> Adauga tranzactie</h4>
	</div>

	<div class="panel-body form-container">

		<form class="form form-horizontal" action="<?php UI::url('transactions/add'); ?>" method="post" name="add-transaction-form" id="addTransactionForm" enctype="multipart/form-data">

			<div class="form-group">
				<label class="control-label col-lg-3" for="date">Data:</label>
				<div class="col-lg-3">
					<input class="form-control" type="date" size="45" maxlength="255" name="date" id="date" value="<?php echo UI::extract_post_val('date'); ?>" />
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3" for="optype">Tip:</label>
				<div class="col-lg-3">
					<select class="form-control" name="optype" id="optype">
						<option value="<?php echo OP::TYPE_IN; ?>" <?php echo UI::selected_or_not(OP::TYPE_IN, post('optype')); ?>>venit</option>
						<option value="<?php echo OP::TYPE_OUT; ?>" <?php echo UI::selected_or_not(OP::TYPE_OUT, post('optype')); ?>>cheltuial&#259;</option>
					</select>
				</div>
				<div class="col-lg-4">
					<div class="checkbox">
						<label class="control-label" for="add_pending">
							<input type="checkbox" name="add_pending" id="add_pending" /> &#238;n a&#351;teptare
						</label>
					</div>
				</div>
			</div>

			<div class="form-group recurring-choices">
				<label class="control-label col-lg-3" for="value">Se repet&#259;:</label>
				<div class="col-lg-4">
					<select class="form-control" name="recurring" id="recurring">
						<option value="no">nu</option>
						<option value="daily">zilnic</option>
						<option value="monthly">lunar</option>
						<option value="yearly">anual</option>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3" for="value">Valoare:</label>
				<div class="col-lg-4">
					<input class="form-control" type="number" step="any" size="45" maxlength="255" name="value" id="value" value="<?php echo UI::extract_post_val('value'); ?>" />
				</div>
			</div>

			<?php $Currencies = Currency::get_all(false); if ( count($Currencies) ):?>
			<div class="form-group">
				<label class="control-label col-lg-3" for="currency_id">Moneda:</label>
				<div class="col-lg-4">
					<select class="form-control" name="currency_id" id="currency_id">
						<?php foreach ($Currencies as $currency): ?>
						<option value="<?php echo $currency->currency_id; ?>" <?php echo UI::selected_or_not($currency->currency_id, post('currency_id')); ?>>
							<?php echo $currency->ccode; ?>
						</option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<?php endif;?>

			<?php $Contacts = Contacts::get_all(0, 999); if ( count($Contacts) ): //TODO activate chosen for contacts  ?>
				<div class="form-group">
					<label class="control-label col-lg-3" for="contact_id">Contact:</label>
					<div class="col-lg-4">
						<select name="contact_id" id="contact_id">
							<?php foreach ($Contacts as $contact): ?>
								<option value="<?php echo $contact->contact_id; ?>" <?php echo UI::selected_or_not($contact->contact_id, post('contact_id')); ?>>
									<?php echo UI::esc_html("{$contact->first_name} {$contact->last_name} ({$contact->organization})"); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			<?php endif;?>


			<div class="form-group">

				<label class="control-label col-lg-3" for="labels">Etichete:</label>

				<?php $Labels = Label::get_parents(); if ( count($Labels) ): ?>

					<div class="col-lg-9">
						<select class="form-control" name="labels[]" id="labels" size="10" multiple="multiple">
							<?php foreach ($Labels as $label): $ChildrenLabels = Label::get_children($label->label_id); ?>

								<option value="<?php echo $label->label_id; ?>" <?php echo UI::selected_or_not($label->label_id, post('labels')); ?>>
									<?php echo UI::esc_html( $label->title ); ?>
								</option>

								<?php if( count($ChildrenLabels) ) foreach($ChildrenLabels as $child): ?>
									<option value="<?php echo $child->label_id; ?>" <?php echo UI::selected_or_not($child->label_id, post('labels')); ?>>
										&#150; <?php echo UI::esc_html( $child->title ); ?>
									</option>
								<?php endforeach; ?>

							<?php endforeach; ?>
						</select>
					</div>

				<?php else: ?>
					<div class="col-lg-9"><a class="btn btn-default" href="<?php UI::page_url('labels/add'); ?>">Adaug&#259; &rarr;</a></div>
				<?php endif;?>
			</div>

			<?php if( Accounts::has_accounts() ): ?>
				<div class="form-group">
					<label class="control-label col-lg-3" for="labels">Cont:</label>
					<div class="col-lg-4">
						<?php $Accounts = Accounts::get_all(0, 999); ?>
						<select class="form-control" name="account_id" id="account_id">
							<option value="0">- neasociat -</option>
							<?php foreach ($Accounts as $account): ?>
								<option value="<?php echo $account->account_id; ?>" <?php echo UI::selected_or_not($account->account_id, post('account_id')); ?>>
									<?php echo UI::esc_html($account->holder_name); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			<?php endif;?>

			<div class="form-group">

				<label class="control-label col-lg-3" for="attachment_1">Fi&#351;iere:</label>

				<div class="col-lg-9">
					<input class="form-control" type="file" size="45" maxlength="255" data-alerts="a1" name="attachment_1" onchange="fn_check_upload_file(this);" id="attachment_1" value="" />
				</div>

				<div class="file-alert file-alert-js file-alert-web-unsafe hidden">
					<?php UI::msg('<i class="fa fa-chevron-up"></i> ' . $unsafeBrowserFileWarn , UI::MSG_WARN); ?>
				</div>

				<div class="file-alert file-alert-js file-alert-upload-size-exceeded hidden">
					<?php UI::msg('<i class="fa fa-chevron-up"></i> ' . $sizeLimiteExceedFile, UI::MSG_WARN); ?>
				</div>
			</div>

			<div class="form-group">
				<div class="col-lg-9 col-lg-offset-3">
					<input class="form-control" type="file" size="45" maxlength="255" data-alerts="a2" name="attachment_2" onchange="fn_check_upload_file(this);" id="attachment_2" value="" />
				</div>
			</div>

			<div class="form-group">
				<div class="col-lg-9 col-lg-offset-3">
					<input class="form-control" type="file" size="45" maxlength="255" data-alerts="a3" name="attachment_3" onchange="fn_check_upload_file(this);" id="attachment_3" value="" />
				</div>

			</div>

			<div class="form-group">
				<label class="control-label col-lg-3" for="comments">Comentarii:</label>
				<div class="col-lg-9">
					<textarea class="form-control" cols="45" rows="3" name="comments" id="comments"><?php echo UI::extract_post_val('comments'); ?></textarea>
				</div>
			</div>

			<p>&nbsp;</p>

			<div class="form-group">
				<div class=" col-lg-5 col-lg-offset-5">
					<input type="hidden" name="add" value="yes" />
					<button class="btn btn-primary" type="submit">Adaug&#259;</button>
				</div>
			</div>

		</form>

	</div>
</div>

<script type="text/javascript" src="<?php UI::asset_url('assets/js/moment.min.js'); ?>"></script>
<script type="text/javascript" src="<?php UI::asset_url('assets/js/pikaday.min.js'); ?>"></script>
<script type="text/javascript">

    max_filesize = parseInt('<?php echo $max_filesize; ?>');

    var today  = new Date();
    var picker = new Pikaday({
		field: document.getElementById('date'),
        firstDay: 1,
        i18n: PikadayRO,
        format: 'YYYY-MM-DD',
        onSelect: function(date) {
            if( date > today ) {
                //---- is a pending transaction ---//
                $('#add_pending').prop('checked', true); $('.recurring-choices').slideDown('fast');
                //---- is a pending transaction ---//
            }
        }
     });
</script>