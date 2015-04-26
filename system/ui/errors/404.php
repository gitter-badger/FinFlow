<?php
/**
 * Renders the 404 error details
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
						<strong>Eroare 404</strong> - Pagina <em><?php echo Util::xss_filter($_SERVER['REQUEST_URI']); ?></em> nu exist&#259;... .
					</h4>
					<p class="msg">Cel mai probabil ai tiparit gre&#351;it adresa URL a paginii dorite, sau pagina a fost &#351;tears&#259;.</p>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-8 col-centered align-center">

				<a class="btn btn-default btn-spaced-right" onclick="window.history.back();"><i class="fa fa-chevron-left"></i> inapoi</a>

				<?php if( User::is_authenticated() ): ?>
					<a class="btn btn-default" href="<?php UI::url('/dashboard'); ?>">catre dashboard <i class="fa fa-chevron-right"></i></a>
				<?php else: ?>
					<a class="btn btn-default" href="<?php UI::url('/login'); ?>">catre log in <i class="fa fa-chevron-right"></i></a>
				<?php endif; ?>

			</div>
		</div>

	</div>
</div>
