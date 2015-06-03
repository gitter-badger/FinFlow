<?php
/**
 * Renders transaction add page
 */

if ( !defined('FNPATH') ) exit();

use FinFlow\UI;
use FinFlow\OP;
use FinFlow\OP_Pending;
use FinFlow\Currency;
use FinFlow\Contacts;
use FinFlow\Label;
use FinFlow\Accounts;
use FinFlow\Util;

if ( isset($_POST['add']) ){

	//--- add a transaction ---//

	$value = floatval( post('value') );
	$date  = post('date');

	$account_id = post('account_id') ? intval($_POST['account_id']) : 0;
	$contact_id = isset($_POST['contact_id']) ? intval($_POST['contact_id']) : 0;

	if ( $value <= 0 )
		$errors[] = __t('Please enter a value!');

	if( strtotime($date) === false)
		$errors[] = __t('Please enter a date!');

	if ( !in_array(post('optype'), OP::getTypesArray()) )
		$errors[] = __t('Please select the transaction type!');

	if( ! isset($_POST['add_pending']) and (strtotime($date) > time() ) )
		$errors[] = "Data tranzactiei este in viitor. Foloseste caracteristica &quot;in asteptare&quot; pentru a adauga tranzactii in viitor.";

	if ( empty($errors) ){

		if( post('add_pending') ){

			//--- add a pending transaction ---//

			$trans_id = OP_Pending::add(post('optype'), $value, post('currency_id'), post('recurring'), array(), $date);

			if( $trans_id ){

				$metadata = array('labels'=>array(), 'files'=>array(), 'account_id'=>0, 'comments'=>trim( post('comments') ));

				//--- add the metadata ---//
				if ( post('labels') and count($_POST['labels']) )
					foreach ($_POST['labels'] as $label_id) $metadata['labels'][] = $label_id;

				if( $account_id )
					$metadata['account_id'] = $account_id;

				if( $contact_id )
					$metadata['contact_id'] = $contact_id;

				//--- upload files if any ---//
				if( count( $_FILES ) ) {

					if( strlen($_FILES['attachment_1']['name']) ){

						$attached = OP_Pending::add_file($trans_id, $_FILES['attachment_1']);

						if( strpos($attached, '@') === false )
							$errors[] = $attached;
						else {
							$filename = $_FILES['attachment_1']['name']; $metadata['files'][$filename] = trim($attached, '@');
						}

					}

					if( strlen($_FILES['attachment_2']['name']) ){

						$attached = OP_Pending::add_file($trans_id, $_FILES['attachment_2']);

						if( strpos($attached, '@') === false )
							$errors[] = $attached;
						else{
							$filename = $_FILES['attachment_2']['name']; $metadata['files'][$filename] = trim($attached, '@');
						}
					}

					if( strlen($_FILES['attachment_3']['name']) ){

						$attached = OP_Pending::add_file($trans_id, $_FILES['attachment_3']);

						if( strpos($attached, '@') === false )
							$errors[] = $attached;
						else {
							$filename = $_FILES['attachment_3']['name']; $metadata['files'][$filename] = trim($attached, '@');
						}
					}

				}
				//--- upload files if any ---//

				$metadata = @serialize($metadata); OP_Pending::update($trans_id, array('metadata'=>$metadata));

				if( $_POST['recurring'] != 'no' )
					$children = OP_Pending::add_children($trans_id); //Add a children as instance of the recurring transaction
				else
					$children = $trans_id;

				//--- add the metadata ---//

				//--- create first child transaction ---//
				if( $children )
					$notices[] = "Tranzac&#355;ia a fost adaugat&#259;.";
				else
					$errors[] =  "Eroare SQL: {$fndb->error} .";
				//--- create first child transaction ---//

			}
			else
				$errors[] = "Eroare SQL: {$fndb->error} .";

			//--- add a pending transaction ---//

		}
		else{

			//--- add a normal transaction ---//

			$trans_id = OP::add(post('optype'), $value, post('currency_id'), post('comments'), $date);

			if ( $trans_id ){

				//--- associate to an account (if any selected) ---//
				if( $account_id )
					Accounts::add_trans($account_id, $trans_id);
				//--- associate to an account (if any selected) ---//


				if ( post('labels', false) and count($_POST['labels']) ) {
					foreach ($_POST['labels'] as $label_id)
						OP::associate_label($trans_id, $label_id);
				}
				else
					$warnings[] = __t('No labels have been associated to the transaction');

				//--- upload files if any ---//
				if( count( $_FILES ) ) {

					if( strlen($_FILES['attachment_1']['name']) ){
						$attached = OP::add_attachment($trans_id, $_FILES['attachment_1']); if($attached != OP::$attached ) $errors[] = $attached;
					}

					if( strlen($_FILES['attachment_2']['name']) ){
						$attached = OP::add_attachment($trans_id, $_FILES['attachment_2']); if($attached != OP::$attached ) $errors[] = $attached;
					}

					if( strlen($_FILES['attachment_3']['name']) ){
						$attached = OP::add_attachment($trans_id, $_FILES['attachment_3']); if($attached != OP::$attached ) $errors[] = $attached;
					}

				}
				//--- upload files if any ---//

				//--- associate with a contact (if any selected) ---//
				if( $contact_id )
					Contacts::assoc_trans($contact_id, $trans_id);
				//--- associate with a contact (if any selected) ---//

				$notices[] = __t('Transaction #%s has been saved.', $trans_id);

			}
			else
				$errors[] = __t('<strong>Ops! SQL Error:</strong> %s', $fndb->error);

			//--- add a normal transaction ---//
		}


	}

	//--- add a transaction ---//
}

$max_filesize     = Util::get_max_upload_filesize();
$max_filesize_fmt = Util::fmt_filesize( $max_filesize );

$unsafeBrowserFileWarn = 'Tipul de fi&#351;ier ales nu are suport nativ in navigatoarele web.';
$sizeLimiteExceedFile  = ( 'Fi&#351;ierul ales este prea mare pentru a fi &#238;ncarc&#259;t. Marimea maxima admisa este ' . $max_filesize_fmt . '.' );


UI::show_errors($errors); UI::show_success($notices); UI::show_warnings($warnings); ?>

<div class="panel panel-default">

	<div class="panel-heading">
		<h4><i class="fa fa-plus-circle"></i> Adauga tranzactie</h4>
	</div>

	<div class="panel-body form-container">

		<form class="form form-horizontal" action="<?php UI::url('transactions/add'); ?>" method="post" name="add-transaction-form" id="addTransactionForm" enctype="multipart/form-data">

			<div class="form-group">
				<label class="control-label col-lg-3" for="date">Data:</label>
				<div class="col-lg-3">
					<input class="form-control datepicker" type="text" size="45" maxlength="255" name="date" id="date" value="<?php echo UI::extract_post_val('date', date('Y-m-d')); ?>" />
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3" for="optype">Tip:</label>
				<div class="col-lg-4">

					<div class="input-group">
					<label class="radio-inline radio-inline-spaced-right">
						<input name="optype" type="radio"" value="<?php echo OP::TYPE_IN; ?>" <?php echo UI::checked_or_not(OP::TYPE_IN, post('optype')); ?>/> venit
					</label>

					<label class="radio-inline">
						<input name="optype" type="radio" value="<?php echo OP::TYPE_OUT; ?>" <?php echo UI::checked_or_not(OP::TYPE_OUT, post('optype')); ?>/> cheltuiala
					</label>
					</div>

				</div>
				<div class="col-lg-3">
					<label class="checkbox-inline" for="add_pending">
						<input type="checkbox" name="add_pending" id="add_pending" /> &#238;n a&#351;teptare
					</label>
				</div>
			</div>

			<div class="form-group recurring-choices">
				<label class="control-label col-lg-3" for="value">Se repet&#259;:</label>
				<div class="col-lg-4">
					<select class="form-control" name="recurring" id="recurring">
						<option value="no">nu</option>
						<option value="daily">zilnic</option>
						<!-- <option value="weekly">saptamanal</option> --->
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


			<?php $Labels = Label::get_parents(); if ( count($Labels) ): ?>
			<div class="form-group">

				<label class="control-label col-lg-3" for="labels">Etichete:</label>

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

			</div>
			<?php endif; ?>

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
				<div class=" col-lg-12 align-center">
					<input type="hidden" name="add" value="yes" />
					<button class="btn btn-primary btn-submit" type="submit">Adaug&#259;</button>
				</div>
			</div>

		</form>

	</div>
</div>

<?php

$onclose = <<<JS
function xdtcheck(){

	var today 		 = new Date();
	var selectedDate = new Date( $('#date').val() );

	if( ( selectedDate > today ) && ! $('#add_pending').is(':checked') ){
		$('#add_pending').trigger('click');
	}
	else if( $('#add_pending').is(':checked') ){
		$('#add_pending').trigger('click');
	}

}
JS;

UI::component('extras/datepicker', array('theme'=>'classic', 'onclose'=>$onclose));

?>
<script type="text/javascript">max_filesize = <?php echo $max_filesize; ?>;</script>