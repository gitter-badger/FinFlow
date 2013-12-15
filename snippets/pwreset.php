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

fn_Captcha::init(); $captcha_url = (FN_URL . '/snippets/captcha.php?o=1');

?>
<div class="row content">
    <div class="span2">&nbsp;</div>

    <div class="span8">

        <div class="inner-container container-login">
            <h2 align="center" class="brand">FinFlow</h2>

                <?php fn_UI::show_errors($Errors); ?>

                <?php if( empty($reset_key) ): ?>

                    <?php if($Success): ?>

                        <p class="msg note">
                            Un email cu informatii pentru resetarea parolei a fost trimis la <em><?php echo $_POST['email']; ?></em>.<br/><br/>
                            Verific&#259;-&#355;i dosarul Inbox, iar dac&#259; nu e acolo verific&#259; &#351;i dosarele de Spam &#351;i Trash.
                        </p>

                    <?php else: ?>

                        <form target="_self" method="post" name="request-pw-form" id="requestPwForm">
                            <p align="center">
                                <label for="email">Email:</label>
                                <input type="email" size="45" maxlength="255" name="email" id="email" value="<?php echo fn_UI::extract_post_val('email');?>" />
                            </p>
                            <p align="center">
                                <label for="verify">Verificare:</label>

                                <?php if( fn_Captcha::supports_img() ): ?>
                                    <input type="text" style="width: 100px;" size="10" maxlength="255" name="verify" id="verify" value="" />
                                    <img id="captchaImg" onclick="fn_popup('<?php echo $captcha_url; ?>&mag=1&htmlmag=1');" src="<?php echo $captcha_url; ?>" align="absmiddle" height="30"/>
                                <?php else: ?>
                                    <?php fn_Captcha::output_math(); ?> &nbsp;
                                    <input type="text" size="10" style="width: 27%;" maxlength="255" name="verify" id="verify" value="" />
                                <?php endif;?>
                            </p>

                            <p class="submit" align="center">
                                <button class="btn btn-primary" type="submit">Trimite link de reset</button>
                            </p>
                        </form>

                    <?php endif;?>

                <?php else: ?>

                    <?php if( $user and isset($user->user_id) ): ?>

                        <p class="align-center"> Email: <em><?php echo $user->email; ?></em> </p><br class="clear"/>

                        <?php if( $Success ): ?>

                               <p class="msg note">
                                   Parola a fost schimbata. <br/> Pentru a te autentifica <a href="<?php fn_UI::page_url('index', array('p'=>'login')); ?>">click aici</a>.
                               </p>

                        <?php else: ?>

                            <form target="_self" method="post" name="reset-pw-form" id="resetPwForm">
                                <p align="center">
                                    <label for="password">Parola:</label>
                                    <input type="password" size="45" maxlength="255" name="password" id="password" value="" />
                                </p>
                                <p align="center">
                                    <label for="password2">Confirmare:</label>
                                    <input type="password" size="45" maxlength="255" name="password2" id="password2" value="" />
                                </p>

                                <p class="submit" align="center">
                                    <button class="btn btn-primary" type="submit">Reseteaz&#259; parola</button>
                                </p>
                            </form>

                        <?php endif; ?>

                    <?php else: ?>

                        <p class="msg warning">
                            Codul de resetare al parolei este incorect sau a fost deja utilizat. <br/>
                            Pentru a ob&#355;ine un nou cod acceseaz&#259; <a href="<?php fn_UI::page_url('index', array('p'=>'pwreset')); ?>">formularul de resetare al parolei</a> .
                        </p>

                    <?php endif; ?>

                <?php endif; ?>

        </div>
    </div>
    <div class="span2">&nbsp;</div>
</div>