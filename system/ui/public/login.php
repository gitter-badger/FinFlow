<?php
/**
 * Renders the log in form
 */

if( !defined('FNPATH') ) exit;

use FinFlow\UI;
use FinFlow\User;
use FinFlow\Captcha;

$errors = $notices = array();

if (count($_POST)){

	if ( empty($_POST['email']) )
		$errors[] = "Email-ul lipse&#351;te.";

	if ( empty($_POST['pass']) )
		$errors[] = "Parola lipse&#351;te.";

	//TODO activate captcha check
	//if( ! Captcha::validate( post('verify') ) )
	//	$errors[] = "Codul de verificare introdus este incorect.";

	if ( empty($errors) ){

		if ( User::authenticate($_POST['email'], $_POST['pass']) )
			$notices[] = "Ai fost autentificat...";
		else
			$errors[] = "Verific&#259; email-ul &#351;i parola. Cel pu&#355;in una dintre ele este gre&#351;it&#259;.";

	}

}

Captcha::init(); $captcha_url = ( FN_URL . '/captcha?o=1' ); ?>
<div class="row content content-login">

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

			<div class="col-lg-7 col-sm-8 col-xs-12 col-centered">

				<div class="panel panel-default">

					<div class="panel-body">

						<h3 class="align-center"><i class="fa fa-sign-in"></i> Log in</h3><hr/>

						<?php UI::show_warnings($errors); UI::show_notes($notices); ?>

						<?php if ( User::is_authenticated() ): ?>
							<script type="text/javascript">window.location.href='<?php UI::url('dashboard'); ?>';</script>
						<?php else:?>

							<form class="form form-horizontal" target="_self" method="post" name="settings-form" id="settingsForm">

								<div class="form-group">
									<label class="control-label col-lg-3" for="email">Email:</label>
									<div class="col-lg-9">
										<input class="form-control" type="text" size="45" maxlength="255" name="email" id="email" value="<?php echo UI::extract_post_val('email');?>" />
									</div>
								</div>

								<div class="form-group">
									<label class="control-label col-lg-3" for="pass">Parola:</label>
									<div class="col-lg-9">
										<input class="form-control" type="password" size="45" maxlength="255" name="pass" id="pass" value="" />
									</div>
								</div>

								<div class="form-group">
									<label class="control-label col-lg-3" for="verify">Verificare:</label>
									<div class="col-lg-9">
										<div class="input-group">
											<?php if( Captcha::supports_img() ): ?>
												<span class="input-group-addon captcha-img-addon">
                                                <img class="captcha-img" onclick="fn_popup('<?php echo $captcha_url; ?>&mag=1&htmlmag=1');" src="<?php echo $captcha_url; ?>" align="absmiddle" title="click pentru marire"/>
                                            </span>
												<input class="form-control" type="text" size="10" maxlength="255" name="verify" id="verify" value="" />
											<?php else: ?>
												<span class="input-group-addon"><?php fn_Captcha::output_math(); ?></span>
												<input class="form-control" type="text" size="10" maxlength="255" name="verify" id="verify" value="" />
											<?php endif;?>
										</div>
									</div>
								</div>

								<p>&nbsp;</p>

								<div class="form-group align-center">
									<div class="col-lg-12 form-submit">
										<button class="btn btn-primary btn-spaced-right" type="submit">Autentificare</button>
										<a class="btn btn-default" href="<?php UI::url('recover'); ?>" >Am uitat parola...</a>
									</div>
								</div>

							</form>

						<?php endif; ?>

					</div>

				</div>

			</div>

		</div>

	</div>

</div>