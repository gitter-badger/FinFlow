<?php
/**
 * FinFlow 1.0 - Database connection/install file
 * @author Adrian S. (https://www.amsquared.co.uk/)
 * @version 1.0
 */

include_once 'header.php';

$errors = array(); $success = false;

if( count($_POST) ){

    if( fn_Installer::check_db_connection($_POST['dbhost'], $_POST['dbuser'], $_POST['dbpassword'], $_POST['dbname']) ){

        if( fn_Installer::create_cfg_file($_POST) ){

            if( fn_Installer::install_db($_POST['dbhost'], $_POST['dbuser'], $_POST['dbpassword'], $_POST['dbname']) )
                $success = true;

            else  $errors[] = "Nu se poate finaliza instalarea bazei de date.
                                     Verific&#259; dac&#259;  datele de conectare &#351;i numele bazei de date este corect &#351;i &#238;ncearc&#259; din nou.";

        }
        else $errors[] = "Fi&#351;ierul de configurare nu poate fi salvat.
                                Verific&#259; permisiunile asupra directorului aplica&#355;iei &#351;i &#238;ncearc&#259; din nou.";

    }
    else $errors[] = "Nu se poate realiza conexiunea la baza de date {$_POST['dbname']} pe {$_POST['dbhost']}.
                            Verific&#259; dac&#259;  datele de conectare &#351;i numele bazei de date este corect &#351;i &#238;ncearc&#259; din nou.";

}

?>

    <p align="center" class="logo">
        <img src="<?php echo FN_URL; ?>/images/finflow-logo.png" width="50" align="absmiddle" alt="FinFlow"/>
        <br/>FinFlow
    </p>

    <h3 align="center">Configurarea bazei de date</h3>

    <?php fn_UI::show_errors($errors); ?>

    <?php if( !$success ): ?>
        <form name="setup-create-cfg" id="setupCreateCfg" method="post" target="_self">
            <p>
                <label for="dbhost">Gazda (Host):</label>
                    <input type="text" name="dbhost" id="dbhost" value="<?php echo fn_UI::extract_post_val('dbhost', 'localhost'); ?>"/>
            </p>
            <p>
                <label for="dbuser">Utilizator:</label>
                <input type="text" name="dbuser" id="dbuser" value="<?php echo fn_UI::extract_post_val('dbuser'); ?>"/>
            </p>
            <p>
                <label for="dbpassword">Parol&#259;:</label>
                <input type="password" name="dbpassword" id="dbpassword" value=""/>
            </p>
            <p>
                <label for="dbname">Baza de date:</label>
                <input type="text" name="dbname" id="dbname" value="<?php echo fn_UI::extract_post_val('dbname'); ?>"/>
            </p>

            <p style="text-align: center;">
                <button class="btn" type="button" onclick="window.location.href='requirements.php';"> <span class="icon-arrow-left"></span> &#206;napoi</button>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <button class="btn btn-success" type="submit">
                    Continu&#259; <span class="icon-arrow-right"></span>
                </button>
            </p>

        </form>
    <?php else: ?>

        <p class="msg note">
            Fi&#351;ierul de configurare a fost creat cu succes.
        </p>
        <script type="text/javascript">
            window.location.href = '<?php echo FN_URL; ?>/setup/setup-exchangerate-parser.php';
        </script>

    <?php endif; ?>

    <hr/>


<?php include_once 'footer.php'; ?>