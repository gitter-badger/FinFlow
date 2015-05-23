<?php

use FinFlow\UI;
use FinFlow\User;
use FinFlow\OP;
use FinFlow\OP_Pending;
use FinFlow\Currency;
use FinFlow\Util;

if ( ! User::is_authenticated() )
	exit();

$trans_id   = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_pending = isset($_GET['pending']) ? true : false;

if( $is_pending ) {
    $Transaction = OP_Pending::get($trans_id); $is_pending= true;
}else
    $Transaction = OP::get($trans_id);

if( count($Transaction) and isset($Transaction->trans_id) ){

    $labels		 = $is_pending ? OP_Pending::get_labels( $Transaction ) : OP::get_labels($trans_id);
    $account     = $is_pending ? OP_Pending::get_account( $Transaction ) : OP::get_account($trans_id);
    $currency	 = Currency::get( $Transaction->currency_id );

    $details    = $is_pending ? OP_Pending::get_metdata($Transaction, 'details') : OP::get_metadata($trans_id, 'details');
    $comments   = $is_pending ? OP_Pending::get_metdata($Transaction, 'comments') : $Transaction->comments;
    $attachments= $is_pending ? OP_Pending::get_metdata($Transaction, 'attachments') : OP::get_metadata($trans_id, 'attachments');

    if( ( is_array($attachments) and count($attachments) ) or strlen($attachments) ){

        $attachmentsNames = $is_pending ? OP_Pending::get_metdata($Transaction, 'attachments_names', $attachments) : OP::get_metadata($trans_id, 'attachments_names', $attachments);

        $attachments      = is_array($attachments) ? $attachments : @unserialize($attachments);
        $attachmentsNames = is_array($attachmentsNames) ? $attachmentsNames : @unserialize($attachmentsNames);

        $a=0;
    }

}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>FinFlow | Detalii tranzac&#355;ie #<?php echo $Transaction->trans_id; ?></title>
	<meta name="generator" content="FinFlow <?php echo FN_VERSION; ?>" />
	<meta id="view" name="viewport" content="width=device-width, maximum-scale=1.0, minimum-scale=1.0, initial-scale=1"/>
	<link rel="shortcut icon" href="<?php UI::asset_url('/images/favicon.png'); ?>" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?php UI::asset_url('/assets/css/bootstrap.min.css'); ?>"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?php UI::asset_url('/assets/css/font-awesome.min.css'); ?>"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?php UI::asset_url('/assets/css/styles.css'); ?>"/>
</head>
<body id="page-transaction-details" class="transaction-details logged-in" role="document">

<div class="wrapper">

	<?php if ( count($Transaction) ): ?>

		<div class="navbar navbar-default navbar-fixed-top" role="navigation">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand">Tranzactie #<?php echo $Transaction->trans_id;?></a>
				</div>
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">
						<li><a href="#"><i class="fa fa-chevron-left"></i></a></li>
						<li><a href="#"><i class="fa fa-chevron-right"></i></a></li>
						<li><a href="#" onclick="window.print();"><i class="fa fa-print"></i></a></li>
						<li><a href="#">&nbsp;</a></li>
					</ul>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div class="container-fluid">

		<div class="row">

			<div class="col-lg-12">


				<?php if (count($Transaction)): ?>

					<h4 class="visible-print">Tranzactie #<?php echo $Transaction->trans_id;?></h4>

					<div class="panel panel-default">

						<table class="table table-striped list details">

							<tr>
								<td>Data:</td>
								<td>
									<?php echo UI::translate_date( date(FN_DATETIME_FORMAT, strtotime($Transaction->sdate)) ); ?>
								</td>
							</tr>
							<tr>
								<td>Valoare:</td>
								<td><?php echo $currency->ccode; ?> <?php echo Util::format_nr( $Transaction->value, 4 ); ?></td>
							</tr>

							<?php if (count($labels) ): ?>
								<tr>
									<td> Etichete: </td>
									<td>
										<?php $dlabels=array(); foreach ($labels as $label) $dlabels[] = $label->title; echo @implode(", ", $dlabels); ?>
									</td>
								</tr>
							<?php endif; ?>

							<?php if ( $account ): ?>
								<tr>
									<td> Cont: </td>
									<td>
										<?php echo UI::esc_html( $account->holder_name ); ?>
									</td>
								</tr>
							<?php endif; ?>

							<?php if ( !empty( $comments ) ): ?>
								<tr>
									<td>Comentarii: </td>
									<td><?php echo UI::esc_html( $comments ); ?></td>
								</tr>
							<?php endif;?>

							<?php if( is_array($attachments) and count($attachments) ): ?>
								<tr>
									<td>Fi&#351;iere ata&#351;ate:</td>
									<td>
										<ul style="list-style-type: square;">
											<?php foreach($attachments as $attachment): $apath = OP::get_attachment_path($attachment); $aname = $attachmentsNames[$a]; ?>
												<li>
													<a href="<?php echo UI::get_file_preview_url($apath, array('name'=>$aname)); ?>" target="_self" title="previzualizare">
														<?php echo $aname; ?>
													</a>
													&nbsp;&nbsp;&nbsp;
													<small>
														<a href="<?php echo fn_UI::get_file_download_url($apath, false, $aname); ?>"><span class="icon-download"></span> descarc&#259;</a>
													</small>
												</li>
												<?php $a++; endforeach; ?>
										</ul>
									</td>
								</tr>
							<?php endif;?>

						</table>
					</div>

					<?php if ( !empty($details) ): ?>
						<div class="panel panel-default">
							<div class="panel-body">
								<?php echo UI::esc_html( nl2br( strip_tags($details) ) ); ?>
							</div>
						</div>
					<?php endif; ?>

				<?php else: ?>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12">
								<div class="alert alert-warning">Tranzactia nu exist&#259; sau a fost &#351;tears&#259;.</div>
								<p>
									<a class="btn btn-default" onclick="window.history.back();return false;"><i class="fa fa-arrow-left"></i> inapoi</a>
								</p>
							</div>
						</div>
					</div>
				<?php endif; ?>

			</div>
		</div>
	</div>

</div>

<script type="text/javascript" src="<?php UI::asset_url('assets/js/jquery.min.js'); ?>"></script>
<script type="text/javascript" src="<?php UI::asset_url('assets/js/bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php UI::asset_url('assets/js/fn.js'); ?>"></script>

</body>
</html>