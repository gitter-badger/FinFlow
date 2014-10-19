<?php
/**
 * FinFlow 1.0 - Settings/Timezone
 * @author Adrian S. (adrian@finflow.org)
 * @version 1.0
 */

if ( !defined('FNPATH') ) exit; $timezone_identifiers = DateTimeZone::listIdentifiers(); ?>

<form class="form form-horizontal" target="_self" method="post" name="settings-form" id="settingsForm">
    <fieldset>

        <legend>Set&#259;ri pentru fusul orar</legend>

        <div class="form-group">
            <label class="control-label col-lg-3" for="timezone">Alege fus orar</label>
            <div class="col-lg-4">
                <select class="form-control" name="timezone" id="timezone">
                    <?php foreach($timezone_identifiers as $id=>$name): ?>
                        <option value="<?php echo $name ?>" <?php echo fn_UI::selected_or_not($name, FN_TIMEZONE); ?>>
                            <?php echo str_replace('_', ' ',$name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>


        <div class="form-group">
            <div class="col-lg-12 align-center form-submit">
                <button class="btn btn-primary" type="submit">Salveaz&#259;</button>
            </div>
        </div>

    </fieldset>

</form>