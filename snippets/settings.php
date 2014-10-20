<?php if ( !defined('FNPATH') ) exit(); $tab = isset($_GET['t']) ? urldecode($_GET['t']) : 'mainccs'; $activetab = array(); $activetab[$tab] = 'active';

$errors 	= $notices = array();

if ( count($_POST) and ( $tab != 'myaccount' ) ){

    $data = $_POST;

	if ( isset($data['mup_password']) ) 
		$data['mup_password'] = fn_Util::s_encrypt($data['mup_password'], FN_PW_SALT);
	
	if ( isset($data['op_currencies_in']) ) 
		$data['op_currencies_in'] = implode(",", fn_Util::clean_array($data['op_currencies_in']));

    if( strlen($data['mup_user']) and empty($data['mup_password']) )
        unset($data['mup_password']);

	if( count($data) ) foreach ($data as $key=>$value) fn_Settings::set($key, $value);

}

if ( $tab == 'emailupdate' ){
	
	$Settings = array(
			'state'		=> trim(fn_Settings::get('mup_state')),
			'host'			=> trim(fn_Settings::get('mup_host')),
			'port'			=> intval(fn_Settings::get('mup_port')),
			'encryption'=> trim(fn_Settings::get('mup_encription')),
			'username'	=> trim(fn_Settings::get('mup_user')),
			'password'	=> trim(fn_Settings::get('mup_password'))
	);
	
}

if ($tab == 'mainccs'){

	$Currencies = fn_Currency::get_all(TRUE);
	$defaultCC  = fn_Currency::get_default();
	
	$accepted = fn_Settings::get('op_currencies_in');
	$accepted = @explode(",", $accepted);
}

if( $tab == 'exrparser' ) {
    include_once ( FNPATH . '/inc/exr-init.php' ); global $fnexr;
}

?>

<div class="row">
	<div class="<?php fn_UI::main_container_grid_class(); ?>">
		
		<ul class="nav nav-tabs">
			<li class="<?php echo $activetab['mainccs']; ?>"><a href="index.php?p=settings&t=mainccs"> Conversii automate </a></li>
			<li class="<?php echo $activetab['exrparser']; ?>"><a href="index.php?p=settings&t=exrparser"> Actualizare curs </a></li>
			<li class="<?php echo $activetab['emailupdate']; ?>"><a href="index.php?p=settings&t=emailupdate"> Actualizare prin email </a></li>
            <li class="<?php echo $activetab['thresholds']; ?>"><a href="index.php?p=settings&t=thresholds"> Praguri  </a></li>
            <li class="<?php echo $activetab['timezone']; ?>"><a href="index.php?p=settings&t=timezone"> Personalizare  </a></li>
            <li class="<?php echo $activetab['myaccount']; ?>"><a href="index.php?p=settings&t=myaccount"> Acces  </a></li>
		</ul>
		
		<?php if ( $tab == 'mainccs' ): ?>
			
			<form class="form form-horizontal" target="_self" method="post" name="settings-form" id="settingsForm">
				<div class="form-group">
                    <label class="control-label col-lg-3" for="op_currencies_in">Monede IN:</label>
                    <div class="col-lg-4">
                        <select class="form-control" name="op_currencies_in[]" id="op_currencies_in" size="4" multiple>
                            <option value="0" selected>Moneda implicit&#259;</option>
                            <?php if ( count($Currencies) ) foreach ($Currencies as $currency): ?>
                                <option value="<?php echo $currency->currency_id; ?>" <?php echo fn_UI::selected_or_not($currency->currency_id, $accepted); ?>>
                                    <?php echo $currency->ccode; ?>
                                </option>
                            <?php endforeach;?>
                        </select>
                        <span class="help-block">
                            Monedele suplimentare &#238;n care se accepta tranzac&#355;ii IN (venit). <br/>
                            Tranzac&#355;iile care nu sunt in monedele selectate vor fi convertite la moneda implicit&#259;.
                        </span>
                    </div>
				</div>

                <div class="form-group">
                    <label class="control-label col-lg-3" for="op_ccs_out">Moneda OUT:</label>
                    <div class="col-lg-4">
                        <select class="form-control" name="op_ccs_out" id="op_ccs_out" disabled>
                            <?php if (count($defaultCC)): ?>
                                <option value="<?php echo $defaultCC->id; ?>"> <?php echo $defaultCC->ccode; ?> </option>
                            <?php endif; ?>
                        </select>
                        <span class="help-block">Toate tranzactiile OUT (cheltuieli) sunt convertite la aceasta moned&#259; (implicit&#259;).</span>
                    </div>
                </div>
				
				<div class="form-group">
                    <div class="col-lg-12 align-center form-submit">
                        <button class="btn" type="reset"> Anuleaz&#259; </button>
                        <button class="btn btn-primary" type="submit"> Salveaz&#259; </button>
                    </div>
				</div>
				
			</form>
			
		<?php endif; ?>
		
		<?php if ($tab == 'exrparser') : $defaultCC= fn_Currency::get_default(); ?>

			<form class="form form-horizontal" target="_self" method="post" name="settings-form" id="settingsForm">
				<fieldset>
					<legend>Set&#259;ri cron</legend>
					<div class="form-group">
                        <label class="control-label col-lg-3" for="exchange_rates_cron_url">URL API:</label>
                        <div class="col-lg-4">
                            <input class="form-control" type="text" id="exchange_rates_cron_url" readonly="readonly" size="45" maxlength="255" value="<?php echo $fnexr::EndpointURL; ?>" />
                        </div>
                        <div class="col-lg-2">
                            <a class="btn btn-default" href="<?php echo $fnexr::websiteURL; ?>" target="_blak"> verific&#259; </a>
                        </div>
					</div>

					<div class="form-group">
						<label class="control-label col-lg-3" for="exchange_rates_default_cc">Moneda implicit&#259;:</label>
						<div class="col-lg-4">
                            <input class="form-control" type="text" readonly="readonly" id="exchange_rates_default_cc" size="45" maxlength="255" value="<?php echo $defaultCC->ccode;?>" />
						</div>
                    </div>
					<div class="form-group">
                        <label class="control-label col-lg-3" for="cron_cmd_exr">Linie Cronjob:</label>
                        <div class="col-lg-9">
                            <code class="form-control cronjob">
                                <?php echo fn_Util::cronjob(FNPATH . '/inc/cron/cron.exchangerates.php', 7, 0, '*', '*', '1,2,3,4,5'); ?>
                            </code>
                        </div>
					</div>

					
					<div class="form-group">
                        <div class="col-lg-6">
                            <button class="btn" type="button" onclick="fn_popup('<?php echo FN_URL; ?>/inc/cron/cron.exchangerates.php?test=1');"> Testeaz&#259; script </button>
                        </div>
                        <div class="col-lg-6">
                            <button class="btn" type="button" onclick="fn_popup('<?php echo FN_URL; ?>/inc/cron/cron.exchangerates.php');"> Ruleaz&#259; acum </button>
                        </div>
					</div>

				</fieldset>
			</form>
		<?php endif; ?>
		
		
		<?php if ($tab == 'emailupdate'): ?>
		
			<?php fn_UI::show_errors( $errors ); fn_UI::show_notes( $notices )?>
		
			<form class="form form-horizontal" target="_self" method="post" name="settings-form" id="settingsForm">

				<fieldset>

					<legend>Set&#259;ri cron</legend>

					<div class="form-group">
                        <label class="control-label col-lg-3" for="mup_state_active">Status</label>
                        <div class="col-lg-4">

                            <label class="radio-inline">
                                <input type="radio" name="mup_state" id="mup_state_active" value="active" <?php echo fn_UI::checked_or_not('active', $Settings['state']); ?>/> Activat
                            </label>

                            <label class="radio-inline">
                                <input type="radio" name="mup_state" id="mup_state_deactive" value="inactive" <?php echo fn_UI::checked_or_not('inactive', $Settings['state']); ?>/> Dezactivat
                            </label>

                        </div>
					</div>

                    <div class="form-group">
						<label class="control-label col-lg-3" for="mup_host">Server mail:</label>
						<div class="col-lg-4">
                            <input class="form-control" type="text" size="45" maxlength="255" name="mup_host" id="mup_host" placeholder="mail.domeniu.com" value="<?php echo fn_UI::extract_post_val('mup_host', $Settings['host']);?>" />
						</div>
					</div>

                    <div class="form-group">
						<label class="control-label col-lg-3" for="mup_user">Utilizator:</label>
						<div class="col-lg-4">
                            <input class="form-control" type="text" size="45" maxlength="255" name="mup_user" id="mup_user" placeholder="utilizator@domeniu.com" value="<?php echo fn_UI::extract_post_val('mup_user', fn_Settings::get('mup_user'), TRUE);?>" />
						</div>
					</div>

                    <div class="form-group">
						<label class="control-label col-lg-3" for="mup_password">Parola:</label>
						<div class="col-lg-4">
                            <input class="form-control" type="password" size="45" maxlength="255" name="mup_password" id="mup_password" value="" />
                            <?php if ( strlen($Settings['password']) ): ?>
                                <span class="details"> parola este ascuns&#259;, completeaz&#259; doar dac&#259; vrei s&#259; o schimbi sau s&#259; testezi.</span>
                            <?php endif; ?>
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label col-lg-3" for="mup_encription">Criptare:</label>
						<div class="col-lg-4">
                            <select class="form-control" name="mup_encription" id="mup_encription">
                                <option value="tcp" <?php echo fn_UI::selected_or_not('tcp', $Settings["encryption"]); ?>>f&#259;r&#259;</option>
                                <option value="ssl" <?php echo fn_UI::selected_or_not('ssl', $Settings["encryption"]); ?>>SSL</option>
                                <option value="sslv2" <?php echo fn_UI::selected_or_not('sslv2', $Settings["encryption"]); ?>>SSLV2</option>
                                <option value="sslv3" <?php echo fn_UI::selected_or_not('sslv3', $Settings["encryption"]); ?>>SSLV3</option>
                                <option value="tls" <?php echo fn_UI::selected_or_not('tls', $Settings["encryption"]); ?>>TLS</option>
                            </select>
						</div>
                    </div>

                    <div class="form-group">
						<label class="control-label col-lg-3" for="mup_port">Port:</label>
						<div class="col-lg-2">
                            <select class="form-control" id="port_select" style="width: auto;">
                                <option value="0"> - alege - </option>
                                <option value="110" <?php echo fn_UI::selected_or_not('110', $Settings["port"]); ?>>POP</option>
                                <option value="995" <?php echo fn_UI::selected_or_not('995', $Settings["port"]); ?>>POP-TLS</option>
                            </select>
						</div>
                        <div class="col-lg-2">
                            <input class="form-control" type="text" size="45" maxlength="255" name="mup_port" id="mup_port" placeholder="995" value="<?php echo fn_UI::extract_post_val('mup_port', $Settings["port"]);?>" />
                        </div>
					</div>

                    <div class="form-group">
						<label class="control-label col-lg-3" for="cron_cmd">Linie Cronjob:</label>
						<div class="col-lg-9">
                            <code class="cronjob form-control"><?php echo fn_Util::cronjob(FNPATH . '/inc/cron/cron.readmail.php', 15, 0); ?></code>
                        </div>
                    </div>
					
					<div class="form-group">
                        <div class="col-lg-12 align-center form-submit">

                            <div class="clearfix"/>

                            <input type="hidden" name="mup_testonly" id="mup_testonly" value="0"/>

                            <button class="btn btn-default" type="submit" onclick="fn_submit_apopup('settingsForm', '<?php echo FN_URL; ?>/inc/cron/cron.readmail.php?test=1');">Testeaz&#259; conexiunea</button>
                            <button class="btn btn-primary" type="submit" onmouseover="fn_form_defaults('settingsForm')">Salveaz&#259;</button>
                            <button class="btn btn-default" type="button" onclick="fn_popup('<?php echo FN_URL; ?>/inc/cron/cron.readmail.php');">Ruleaz&#259; acum</button>
                        </div>
					</div>

					
				</fieldset>
				
			</form>
		
		<?php endif;?>

        <?php if ($tab == 'timezone') include_once 'settings-timezone.php'; ?>

        <?php if ($tab == 'thresholds') include_once 'settings-thresholds.php'; ?>

        <?php if ($tab == 'myaccount') include_once 'settings-access.php'; ?>

	</div>

	<?php include_once ( FNPATH . '/snippets/sidebar.php' ); ?>

</div>