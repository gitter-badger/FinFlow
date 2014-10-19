<?php if( !defined('FNPATH') ) exit; include_once ( FNPATH . '/inc/class.captcha.php' ); fn_Captcha::init(); $captcha_url = ( FN_URL . '/snippets/captcha.php?o=1' ); ?>
<div class="row">
	
	<div class="col-lg-5 col-lg-offset-3">
	
		<div class="inner-container container-login">

            <div class="panel panel-default">

			    <div class="panel-heading"> <h2 class="brand align-center">FinFlow</h2> </div>

                <div class="panel-body">
                    <?php

                        if (count($_POST)){

                            $errors 	= 	array();
                            $notice	=	array();

                            if ( empty($_POST['email']) )  $errors[] = "Email-ul lipse&#351;te.";
                            if ( empty($_POST['pass']) )    $errors[] = "Parola lipse&#351;te.";

                            if( !fn_Captcha::validate( $_POST['verify'] ) )
                                $errors[] = "Codul de verificare introdus este incorect.";

                            if ( empty($errors) ){

                                if ( fn_User::authenticate($_POST['email'], $_POST['pass']) )
                                    $notice[] = "Ai fost autentificat...";
                                else
                                    $errors[] = "Verific&#259; email-ul &#351;i parola. Cel pu&#355;in una dintre ele este gre&#351;it&#259;.";

                            }

                        }

                        fn_UI::show_errors($errors); fn_UI::show_notes($notes); ?>

                    <?php if (fn_User::is_authenticated() ): ?>
                        <script type="text/javascript">window.location.href='<?php fn_UI::page_url('dashboard'); ?>';</script>
                    <?php else:?>

                    <form class="form form-horizontal" target="_self" method="post" name="settings-form" id="settingsForm">

                        <div class="form-group">
                            <label class="control-label col-lg-3" for="email">Email:</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="text" size="45" maxlength="255" name="email" id="email" value="<?php echo fn_UI::extract_post_val('email');?>" />
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
                                    <?php if( fn_Captcha::supports_img() ): ?>
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


                        <div class="form-group align-center">
                            <div class="col-lg-12 form-submit">
                                <button class="btn submit btn-primary" type="submit">Autentificare</button>
                                <button class="btn" type="button" onclick="window.location.href='index.php?p=pwreset';">Am uitat parola...</button>
                            </div>
                        </div>

                    </form>

			    <?php endif; ?>

             </div>
			
		</div>
	</div>
</div>