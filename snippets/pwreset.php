<?php if( !defined('FNPATH') ) exit; include_once ( FNPATH . '/inc/class.captcha.php' );

$Errors    = array();
$Success  = false;

$user = null;

$reset_key = isset($_GET['key']) ? trim($_GET['key']) : false;

if( $reset_key ) $user = fn_User::get_by($reset_key, 'pw_reset_key');

if( count($_POST) ){

    if( empty($reset_key) ) {

        //--- step 1, request a reset key ---//
        if( !fn_CheckValidityOf::email( $_POST['email'] ) )
            $Errors[] = "Adresa de email este invalid&#259;.";

        if( !fn_Captcha::validate( $_POST['verify'] ) )
            $Errors[] = "Codul de verificare introdus este incorect.";

        if( empty($Errors) ){
            $user = fn_User::get_by($_POST['email'], 'email');

            if( $user and isset($user->user_id) ){
               if( fn_User::send_reset_pw_link($user->user_id) )
                    $Success = true;
               else
                   $Errors[] = "Emailul nu poate fi trimis din cauza unei erori tehnice. Te rugam sa &#238;ncerci din nou peste c&#226;teva minute.";
            }
            else $Errors[] = "Adresa de email nu este inregistrat&#259;.";

        }
        //--- step 1, request a reset key ---//
    }
    else{

        if( $user and isset($user->user_id) ){

            if( !fn_CheckValidityOf::stringlen($_POST['password'], 6) )
                $Errors[] = "Parola trebuie sa aib&#259; minim 6 caractere";

            if( $_POST['password'] != $_POST['password2'] )
                $Errors[] = "Parola aleas&#259; este diferit&#259; de cea confirmat&#259;.";

            if( empty($Errors) ){
                $changed = fn_User::update($user->user_id, array('email'=>$user->email, 'password'=>$_POST['password'], 'pw_reset_key'=>""));

                if($changed)
                    $Success = true;
                else
                    $Errors[] = "Parola nu poate fi schimbat&#259; din cauza unei erori tehnice. Te rug&#259;m s&#259; &#238;ncerci din nou peste c&#226;teva minute.";
            }

        }
        else $Errors[] = "Codul de reset este invalid.";

    }

}

fn_Captcha::init(); $captcha_url = (FN_URL . '/snippets/captcha.php?o=1'); ?>

<div class="row content">

    <div class="col-lg-5 col-lg-offset-3">

        <div class="inner-container container-login">

            <div class="panel panel-default">

                <div class="panel-heading"> <h2 align="center" class="brand">FinFlow</h2></div>

                <div class="panel-body">

                    <?php fn_UI::show_errors($Errors); ?>

                    <?php if( empty($reset_key) ): ?>

                        <?php if($Success): ?>

                            <div class="alert alert-info">
                                Un email cu informatii pentru resetarea parolei a fost trimis la <em><?php echo $_POST['email']; ?></em>.<br/><br/>
                                Verific&#259;-&#355;i dosarul Inbox, iar dac&#259; nu e acolo verific&#259; &#351;i dosarele de Spam &#351;i Trash.
                            </div>

                        <?php else: ?>

                            <form class="form form-horizontal" target="_self" method="post" name="request-pw-form" id="requestPwForm">
                                <div class="form-group">
                                    <label class="control-label col-lg-4" for="email">Email:</label>
                                    <div class="col-lg-8">
                                        <input class="form-control" type="email" size="45" maxlength="255" name="email" id="email" value="<?php echo fn_UI::extract_post_val('email');?>" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-4" for="verify">Verificare:</label>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <?php if( fn_Captcha::supports_img() ): ?>
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
                                    Parola a fost schimbata. <br/> Pentru a te autentifica <a href="<?php fn_UI::page_url('index', array('p'=>'login')); ?>">click aici</a>.
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
                                Pentru a ob&#355;ine un nou cod acceseaz&#259; <a href="<?php fn_UI::page_url('index', array('p'=>'pwreset')); ?>">formularul de resetare al parolei</a> .
                            </div>

                        <?php endif; ?>

                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>