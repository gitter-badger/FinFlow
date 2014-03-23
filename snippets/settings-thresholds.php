<?php if ( !defined('FNPATH') ) exit;

$Settings = array(
    'state'                        => trim(fn_Settings::get('beal_state')),
    'balance_tsh_green'    => trim(fn_Settings::get('balance_tsh_green')),
    'balance_tsh_blue'      => trim(fn_Settings::get('balance_tsh_blue')),
    'balance_tsh_yellow'  => trim(fn_Settings::get('balance_tsh_yellow')),
    'balance_tsh_red'      => trim(fn_Settings::get('balance_tsh_red'))
);

$defaultCC = fn_Currency::get_default();

?>

<?php fn_UI::show_errors($Errors); ?>

<?php fn_UI::show_notes($Notices); ?>

<form target="_self" method="post" name="settings-form" id="settingsForm">
    <fieldset>
        <legend>Configurare praguri pentu balanta</legend>
        <p>
            <label for="beal_state_active">Alerte prin email</label>
            <label class="wrap-input">
                <input type="radio" name="beal_state" id="beal_state_active" value="active" <?php echo fn_UI::checked_or_not('active', $Settings['state']); ?>/> Activate
            </label>
            <label class="wrap-input wrap-last">
                <input type="radio" name="beal_state" id="beal_state_inactive" value="inactive" <?php echo fn_UI::checked_or_not('inactive', $Settings['state']); ?>/> Dezactivate
            </label>
            <br class="clear"/>
        </p>

        <p>
            <label for="balance_tsh_red" class="treshold red">&nbsp;&nbsp;Rosu sub:</label>
            <input type="number" id="balance_tsh_red" name="balance_tsh_red"  size="45" maxlength="255" placeholder="100" value="<?php echo $Settings['balance_tsh_red']; ?>" /> <?php echo $defaultCC->ccode; ?>
        </p>

        <p>
            <label for="balance_tsh_yellow" class="treshold yellow">&nbsp;&nbsp;Galben sub:</label>
            <input type="number" id="balance_tsh_yellow" name="balance_tsh_yellow"  size="45" maxlength="255" placeholder="300" value="<?php echo $Settings['balance_tsh_yellow']; ?>" /> <?php echo $defaultCC->ccode; ?>
        </p>

        <p>
            <label for="balance_tsh_blue" class="treshold blue">&nbsp;&nbsp;Albastru sub:</label>
            <input type="number" id="balance_tsh_blue" name="balance_tsh_blue"  size="45" maxlength="255" placeholder="550" value="<?php echo $Settings['balance_tsh_blue']; ?>" /> <?php echo $defaultCC->ccode; ?>
        </p>

        <p>
            <label for="balance_tsh_green" class="treshold green">&nbsp;&nbsp;Verde peste:</label>
            <input type="number" id="balance_tsh_green" name="balance_tsh_green"  size="45" maxlength="255" placeholder="750" value="<?php echo $Settings['balance_tsh_green']; ?>" /> <?php echo $defaultCC->ccode; ?>
        </p>

        <p>
            <button class="btn btn-primary" type="submit">Salveaz&#259;</button>
        </p>

    </fieldset>
</form>