<?php
/**
 * Renders the 500 error details
 */

use FinFlow\UI;
use FinFlow\Util;
use FinFlow\User;

?>
<div class="row content content-error">
	<div class="col-lg-12">

		<div class="row">
			<div class="col-md-6 col-centered align-center">
				<a class="brand">
					<img src="<?php UI::asset_url('/assets/images/finflow-logo.png'); ?>">
				</a>
			</div>
		</div>

		<div class="row">&nbsp;</div>

		<div class="row">
			<div class="col-lg-10 col-centered">
				<div class="alert alert-warning">
					<h4 class="error-heading">
						<strong>Ops! The server made a boo boo!</strong>
					</h4>
					<p class="msg">details about the error go here.</p>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-8 col-centered align-center">

				<a class="btn btn-default btn-spaced-right" onclick="window.history.back();">
					<i class="fa fa-chevron-left"></i> inapoi
				</a>

			</div>
		</div>

	</div>
</div>
