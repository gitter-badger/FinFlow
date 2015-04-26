<?php if( !defined('FNPATH') ) exit;

use FinFlow\UI;
use FinFlow\User;
use FinFlow\CheckValidityOf;
use FinFlow\Captcha;

$Errors   = array();
$Success  = false;

$user      = null;
$reset_key = get('key');

if( $reset_key ) $user = User::get_by($reset_key, 'pw_reset_key');

if( count($_POST) ){

    if( empty($reset_key) ) {

        //--- step 1, request a reset key ---//
        if( ! CheckValidityOf::email( $_POST['email'] ) )
            $Errors[] = "Adresa de email este invalid&#259;.";

        if( ! Captcha::validate( $_POST['verify'] ) )
            $Errors[] = "Codul de verificare introdus este incorect.";

        if( empty($Errors) ){
            $user = User::get_by($_POST['email'], 'email');

            if( $user and isset($user->user_id) ){
               if( User::send_reset_pw_link($user->user_id) )
                    $Success = true;
               else
                   $Errors[] = "Emailul nu poate fi trimis din cauza unei erori tehnice. Te rugam sa &#238;ncerci din nou peste c&#226;teva minute.";
            }
            else $Errors[] = "Adresa de email nu este inregistrat&#259;.";

        }
        //--- step 1, request a reset key ---//
    }
    else{

	    //--- step 2, reset the password ---//
        if( $user and isset($user->user_id) ){

            if( ! CheckValidityOf::stringlen(post('password'), 8) )
                $Errors[] = "Parola trebuie sa aib&#259; minim 8 caractere";

            if( post('password') != post('password2') )
                $Errors[] = "Parola aleas&#259; este diferit&#259; de cea confirmat&#259;.";

            if( empty($Errors) ){

                $changed = User::update($user->user_id, array('email'=>$user->email, 'password'=>post('password'), 'pw_reset_key'=>''));

                if($changed)
                    $Success = true;
                else
                    $Errors[] = "Parola nu poate fi schimbat&#259; din cauza unei erori tehnice. Te rug&#259;m s&#259; &#238;ncerci din nou peste c&#226;teva minute.";
            }

        }
        else
	        $Errors[] = "Codul de reset este invalid.";
	    //--- step 2, reset the password ---//

    }

}

Captcha::init(); $captcha_url = ( FN_URL . '/captcha/?o=1' ); ?>

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

						<h3 class="align-center">Recover your password</h3><hr/>

						<?php UI::show_errors($Errors); ?>

						<?php if( empty($reset_key) ): ?>

							<?php if($Success): ?>

								<div class="alert alert-info">
									Un email cu informatii pentru resetarea parolei a fost trimis la
									<em><?php echo $_POST['email']; ?></em>.<br/><br/>
									Verific&#259;-&#355;i dosarul Inbox, iar dac&#259; nu e acolo verific&#259; &#351;i
									dosarele de Spam &#351;i Trash.
								</div>

							<?php else: ?>

								<form class="form form-horizontal" target="_self" method="post" name="request-pw-form" id="requestPwForm">
									<div class="form-group">
										<label class="control-label col-lg-4" for="email">Email:</label>
										<div class="col-lg-8">
											<input class="form-control" type="email" size="45" maxlength="255" name="email" id="email" value="<?php echo UI::extract_post_val('email');?>" />
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" for="verify">Verificare:</label>
										<div class="col-lg-8">
											<div class="input-group">
												<?php if( Captcha::supports_img() ): ?>
													<span class="input-group-addon captcha-img-addon">
                                                <img id="captchaImg" onclick="fn_popup('<?php echo $captcha_url; ?>&mag=1&htmlmag=1');" src="<?php echo $captcha_url; ?>" align="absmiddle"/>
                                            </span>
													<input class="form-control" type="text" size="10" maxlength="255" name="verify" id="verify" value=""/>
												<?php else: ?>
													<span class="input-group-addon"><?php fn_Captcha::output_math(); ?></span>
													<input class="form-control" type="text" size="10" maxlength="255" name="verify" id="verify" value=""/>
												<?php endif;?>
											</div>
										</div>
									</div>

									<p>&nbsp;</p>

									<div class="form-group align-center">
										<div class="col-lg-12 form-submit">
											<button class="btn btn-primary" type="submit">Trimite link de reset</button>
										</div>
									</div>
								</form>

							<?php endif;?>

						<?php else: ?>

							<?php if( $user and isset($user->user_id) ): ?>

								<p class="align-center"> Email: <em><?php echo $user->email; ?></em> </p>

								<div class="clearfix"></div>

								<?php if( $Success ): ?>

									<div class="alert alert-info">
										Parola a fost schimbata. <br/>
										Pentru a te autentifica <a href="<?php UI::url('/login'); ?>">click aici</a>.
									</div>

								<?php else: ?>

									<form class="form form-horizontal" target="_self" method="post" name="reset-pw-form" id="resetPwForm">
										<div class="form-group">
											<label class="control-label col-lg-3" for="password">Parola:</label>
											<div class="col-lg-8">
												<input class="form-control" type="password" size="45" maxlength="255" name="password" id="password" value="" />
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-lg-4" for="password2">Confirmare:</label>
											<div class="col-lg-8">
												<input class="form-control" type="password" size="45" maxlength="255" name="password2" id="password2" value="" />
											</div>
										</div>

										<div class="form-group">
											<div class="col-lg-12 align-center"><button class="btn btn-primary" type="submit">Reseteaz&#259; parola</button></div>
										</div>
									</form>

								<?php endif; ?>

							<?php else: ?>

								<div class="alert alert-warning">
									Codul de resetare al parolei este incorect sau a fost deja utilizat. <br/>
									Pentru a ob&#355;ine un nou cod acceseaz&#259; <a href="<?php UI::url('recover'); ?>">formularul de resetare al parolei</a> .
								</div>

							<?php endif; ?>

						<?php endif; ?>

					</div>
				</div>

			</div>

		</div>

	</div>

</div>