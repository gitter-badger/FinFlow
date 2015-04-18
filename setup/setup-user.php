<?php

include_once 'header.php';

$enabled = true; $saved = false; $errors = $notes = array();

$Users = fn_User::get_all(); if( count($Users) ) $enabled = false;

if ($enabled and isset($_POST['email']) and isset($_POST['pass']) ){

    $errors = array();

    if( fn_CheckValidityOf::email( $_POST['email'] ) and fn_CheckValidityOf::stringlen(trim($_POST['pass']), 3) ){

        $saved = fn_User::add(array('email'=>$_POST['email'], 'password'=>$_POST['pass']));

        if($saved){
            fn_Settings::set('db_version', FN_DB_VERSION); //fn_Installer::clean();
        }

    }
    else $errors[] = "Adresa de email sau parola aleas&#259; este invalid&#259;.";

}

?>

<h2 align="center" class="brand">FinFlow</h2>

<?php if( $enabled ): fn_UI::show_errors($errors); fn_UI::show_notes($notes); ?>

    <?php if( $saved ): ?>

        <p class="msg note">
            Utilizatorul <em><?php echo $_POST['email']; ?></em> a fost creat. Acum te po&#355;i <a href="<?php fn_UI::page_url('login'); ?>">autentifica</a>.
        </p>

    <?php else : ?>

        <?php if( empty($_POST) ): ?>

            <p style="padding: 10px; text-align: center;">Alege un email si o parol&#259;, pentru conectare.</p>

        <?php endif; ?>

            <form target="_self" method="post" name="settings-form" id="settingsForm">
                <p align="center">
                    <label for="email">Email:</label>
                    <input type="text" size="45" maxlength="255" name="email" id="email" value="<?php echo fn_UI::extract_post_val('email');?>" />
                </p>
                <p align="center">
                    <label for="pass">Parola:</label>
                    <input type="password" size="45" maxlength="255" name="pass" id="pass" value="" />
                </p>

                <p align="center">
                    <label for="pass2">Repet&#259; parola:</label>
                    <input type="password" size="45" maxlength="255" name="pass2" id="pass2" value="" />
                </p>

                <p class="submit" align="center">
                    <button class="btn submit btn-primary" type="submit">Creeaz&#259; utilizator</button>
                </p>
            </form>
        <?php endif ?>

    <?php else: ?>

        <script type="text/javascript"> window.location.href = '<?php echo FN_URL; ?>'; </script>

    <?php endif; ?>

<?php include_once 'footer.php'; ?>