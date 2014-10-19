<?php if ( !defined('FNPATH') ) exit;

$Settings = array(
    'state'                        => trim(fn_Settings::get('beal_state')),
    'balance_tsh_green'    => trim(fn_Settings::get('balance_tsh_green')),
    'balance_tsh_blue'      => trim(fn_Settings::get('balance_tsh_blue')),
    'balance_tsh_yellow'  => trim(fn_Settings::get('balance_tsh_yellow')),
    'balance_tsh_red'      => trim(fn_Settings::get('balance_tsh_red'))
);

$defaultCC = fn_Currency::get_default(); fn_UI::show_errors($Errors); fn_UI::show_notes($Notices); ?>

<form class="form form-horizontal" target="_self" method="post" name="settings-form" id="settingsForm">
    <fieldset>

        <legend>Configurare praguri pentu balanta</legend>

        <div class="form-group">
            <label class="control-label col-lg-3" for="beal_state_active">Alerte prin email</label>
            <div class="col-lg-4">
                <label class="radio-inline">
                    <input type="radio" name="beal_state" id="beal_state_active" value="active" <?php echo fn_UI::checked_or_not('active', $Settings['state']); ?>/> Activate
                </label>
                <label class="radio-inline">
                    <input type="radio" name="beal_state" id="beal_state_inactive" value="inactive" <?php echo fn_UI::checked_or_not('inactive', $Settings['state']); ?>/> Dezactivate
                </label>
            </div>
        </div>


        <div class="form-group">
            <label for="balance_tsh_red" class="threshold red control-label col-lg-3">Rosu sub:</label>
            <div class="col-lg-4">
                <div class="input-group">
                    <input class="form-control" type="number" id="balance_tsh_red" name="balance_tsh_red"  size="45" maxlength="255" placeholder="100" value="<?php echo $Settings['balance_tsh_red']; ?>" />
                    <span class="input-group-addon"><?php echo $defaultCC->ccode; ?></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="balance_tsh_yellow" class="threshold yellow control-label col-lg-3">Galben sub:</label>
            <div class="col-lg-4">
                <div class="input-group">
                    <input class="form-control" type="number" id="balance_tsh_yellow" name="balance_tsh_yellow"  size="45" maxlength="255" placeholder="300" value="<?php echo $Settings['balance_tsh_yellow']; ?>" />
                    <span class="input-group-addon"><?php echo $defaultCC->ccode; ?></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="balance_tsh_blue" class="threshold blue control-label col-lg-3">Albastru sub:</label>
            <div class="col-lg-4">
                <div class="input-group">
                    <input class="form-control" type="number" id="balance_tsh_blue" name="balance_tsh_blue"  size="45" maxlength="255" placeholder="550" value="<?php echo $Settings['balance_tsh_blue']; ?>" />
                    <span class="input-group-addon"><?php echo $defaultCC->ccode; ?></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="balance_tsh_green" class="threshold green control-label col-lg-3">Verde peste:</label>
            <div class="col-lg-4">
                <div class="input-group">
                    <input class="form-control" type="number" id="balance_tsh_green" name="balance_tsh_green" size="45" maxlength="255" placeholder="750" value="<?php echo $Settings['balance_tsh_green']; ?>" />
                    <span class="input-group-addon"><?php echo $defaultCC->ccode; ?></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-lg-12 align-center">
                <button class="btn btn-primary" type="submit">Salveaz&#259;</button>
            </div>
        </div>

    </fieldset>
</form>