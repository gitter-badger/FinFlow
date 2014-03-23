<?php if ( !defined('FNPATH') ) exit();

$tab = isset($_GET['t']) ? urldecode($_GET['t']) : 'mainccs'; $activetab = array(); $activetab[$tab] = 'active';

if ( count($_POST) and ( $tab != 'myaccount' ) ){

	$errors 	= array();
	$notices = array();

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
			'state'			=> trim(fn_Settings::get('mup_state')),
			'host'			=> trim(fn_Settings::get('mup_host')),
			'port'			=> intval(fn_Settings::get('mup_port')),
			'encryption'	=> trim(fn_Settings::get('mup_encription')),
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

<div class="row content">
	<div class="span10">
		
		<ul class="nav nav-tabs">
			<li class="<?php echo $activetab['mainccs']; ?>"><a href="index.php?p=settings&t=mainccs"> Conversii automate </a></li>
			<li class="<?php echo $activetab['exrparser']; ?>"><a href="index.php?p=settings&t=exrparser"> Actualizare curs </a></li>
			<li class="<?php echo $activetab['emailupdate']; ?>"><a href="index.php?p=settings&t=emailupdate"> Actualizare prin email </a></li>
            <li class="<?php echo $activetab['thresholds']; ?>"><a href="index.php?p=settings&t=thresholds"> Praguri  </a></li>
            <li class="<?php echo $activetab['timezone']; ?>"><a href="index.php?p=settings&t=timezone"> Fus orar  </a></li>
            <li class="<?php echo $activetab['myaccount']; ?>"><a href="index.php?p=settings&t=myaccount"> Acces  </a></li>
		</ul>
		
		<?php if ( $tab == 'mainccs' ): ?>
			
			<form target="_self" method="post" name="settings-form" id="settingsForm">
				<p>
					<label for="op_currencies_in">Monede IN:</label>
					<select name="op_currencies_in[]" id="op_currencies_in" size="4" multiple>
                        <option value="0" selected>Moneda implicit&#259;</option>
						<?php if ( count($Currencies) ) foreach ($Currencies as $currency): ?>
						<option value="<?php echo $currency->currency_id; ?>" <?php echo fn_UI::selected_or_not($currency->currency_id, $accepted); ?>> 
							<?php echo $currency->ccode; ?> 
						</option>
						<?php endforeach;?>
					</select>
					<span class="details btn-top" style="display: inline-block;">
						Monedele suplimentare &#238;n care se accepta tranzac&#355;ii IN (venit). <br/>
						Tranzac&#355;iile care nu sunt in monedele selectate vor fi convertite la moneda implicit&#259;.
					</span>
				</p>
				
				<p>
					<label for="op_ccs_out">Moneda OUT:</label>
					<select name="op_ccs_out" id="op_ccs_out" disabled>
						<?php if (count($defaultCC)): ?>
						<option value="<?php echo $defaultCC->id; ?>"> <?php echo $defaultCC->ccode; ?> </option>
						<?php endif; ?>
					</select>
					<span class="details">
						Toate tranzactiile OUT (cheltuieli) sunt convertite la aceasta moned&#259; (implicit&#259;).
					</span>
				</p>
				
				<p>
                    <button class="btn" type="reset"> Anuleaz&#259; </button>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<button class="btn btn-primary" type="submit"> Salveaz&#259; </button>
				</p>
				
			</form>
			
		<?php endif; ?>
		
		<?php if ($tab == 'exrparser'): $defaultCC= fn_Currency::get_default(); ?>
			<form target="_self" method="post" name="settings-form" id="settingsForm">
				<fieldset>
					<legend>Set&#259;ri cron</legend>
					<p>
						<label for="exchange_rates_cron_url">URL:</label>
						<input type="text" id="exchange_rates_cron_url" readonly="readonly" size="45" maxlength="255" value="<?php echo $fnexr::EndpointURL; ?>" />
						<a class="btn btn-top" href="<?php echo $fnexr::websiteURL; ?>" target="_blak"> verific&#259; </a>
						<span class="details"></span>
					</p>
					<p>
						<label for="exchange_rates_default_cc">Moneda implicit&#259;:</label>
						<input type="text" readonly="readonly" id="exchange_rates_default_cc" size="45" maxlength="255" value="<?php echo $defaultCC->ccode;?>" />
					</p>
					<p>
						<label for="cron_cmd_exr">Linie Cronjob:</label>
                        <code class="cronjob"><?php echo fn_Util::cronjob(FNPATH . '/inc/cron/cron.exchangerates.php', 7, 0, '*', '*', '1,2,3,4,5'); ?></code>
                        <br/><br class="clear"/>
					</p>
					
					<p>
						<button class="btn" type="button" onclick="fn_popup('<?php echo FN_URL; ?>/inc/cron/cron.exchangerates.php?test=1');"> Testeaz&#259; script </button>
						&nbsp;&nbsp;&nbsp;
						<button class="btn" type="button" onclick="fn_popup('<?php echo FN_URL; ?>/inc/cron/cron.exchangerates.php');"> Ruleaz&#259; acum </button>
					</p>
				</fieldset>
			</form>
		<?php endif; ?>
		
		
		<?php if ($tab == 'emailupdate'): ?>
		
			<?php fn_UI::show_errors( $errorMsg ); fn_UI::show_notes( $successMsg )?>
		
			<form target="_self" method="post" name="settings-form" id="settingsForm">
				<fieldset>
					<legend>Set&#259;ri cron</legend>
					<p>
						<label for="mup_state_active">Status</label>
						<label class="wrap-input">
							<input type="radio" name="mup_state" id="mup_state_active" value="active" <?php echo fn_UI::checked_or_not('active', $Settings['state']); ?>/> Activat 
						</label>
						
						<label class="wrap-input wrap-last">
							<input type="radio" name="mup_state" id="mup_state_deactive" value="inactive" <?php echo fn_UI::checked_or_not('inactive', $Settings['state']); ?>/> Dezactivat
						</label>
						
						<br class="clear"/>
						
					</p>
					
					<p>
						<label for="mup_host">Server mail:</label>
						<input type="text" size="45" maxlength="255" name="mup_host" id="mup_host" placeholder="mail.domeniu.com" value="<?php echo fn_UI::extract_post_val('mup_host', $Settings['host']);?>" />
					</p>
					
					<p>
						<label for="mup_user">Utilizator:</label>
						<input type="text" size="45" maxlength="255" name="mup_user" id="mup_user" placeholder="utilizator@domeniu.com" value="<?php echo fn_UI::extract_post_val('mup_user', fn_Settings::get('mup_user'), TRUE);?>" />
					</p>
					
					<p>
						<label for="mup_password">Parola:</label>
						<input type="password" size="45" maxlength="255" name="mup_password" id="mup_password" value="" /> 
						<?php if ( strlen($Settings['password']) ): ?>
							<span class="details"> parola este ascuns&#259;, completeaz&#259; doar dac&#259; vrei s&#259; o schimbi sau s&#259; testezi.</span>
						<?php endif; ?>
					</p>
					
					<p>
						<label for="mup_encription">Criptare:</label>
						<select name="mup_encription" id="mup_encription">
							<option value="tcp" <?php echo fn_UI::selected_or_not('tcp', $Settings["encryption"]); ?>>f&#259;r&#259;</option>
							<option value="ssl" <?php echo fn_UI::selected_or_not('ssl', $Settings["encryption"]); ?>>SSL</option>
							<option value="sslv2" <?php echo fn_UI::selected_or_not('sslv2', $Settings["encryption"]); ?>>SSLV2</option>
							<option value="sslv3" <?php echo fn_UI::selected_or_not('sslv3', $Settings["encryption"]); ?>>SSLV3</option>
							<option value="tls" <?php echo fn_UI::selected_or_not('tls', $Settings["encryption"]); ?>>TLS</option>
						</select>
					</p>
					
					<p>
						<label for="mup_port">Port:</label>
						<select id="port_select" style="width: auto;">
							<option value="0"> - alege - </option>
							<option value="110" <?php echo fn_UI::selected_or_not('110', $Settings["port"]); ?>>POP</option>
							<option value="995" <?php echo fn_UI::selected_or_not('995', $Settings["port"]); ?>>POP-TLS</option>
						</select>
						<input type="text" size="45" maxlength="255" name="mup_port" id="mup_port" style="width:45px;" placeholder="995" value="<?php echo fn_UI::extract_post_val('mup_port', $Settings["port"]);?>" />
					</p>
					
					<p>
						<label for="cron_cmd">Linie Cronjob:</label>
						<code class="cronjob"><?php echo fn_Util::cronjob(FNPATH . '/inc/cron/cron.readmail.php', 15, 0); ?></code>
                        <br/><br class="clear"/>
					</p>
					
					<p>
					
						<input type="hidden" name="mup_testonly" id="mup_testonly" value="0"/>
					
						<button class="btn" type="submit" onclick="fn_submit_apopup('settingsForm', '<?php echo FN_URL; ?>/inc/cron/cron.readmail.php?test=1');">Testeaz&#259; conexiunea</button>
						&nbsp;&nbsp;&nbsp;
						<button class="btn" type="submit" onmouseover="fn_form_defaults('settingsForm')">Salveaz&#259;</button>
						&nbsp;&nbsp;&nbsp;
						<button class="btn" type="button" onclick="fn_popup('<?php echo FN_URL; ?>/inc/cron/cron.readmail.php');">Ruleaz&#259; acum</button>
					</p>
					
				</fieldset>
				
			</form>
		
		<?php endif;?>

        <?php if ($tab == 'timezone'): ?>

            <?php $timezone_identifiers = DateTimeZone::listIdentifiers(); ?>

            <form target="_self" method="post" name="settings-form" id="settingsForm">
                <fieldset>
                    <legend>Set&#259;ri pentru fusul orar</legend>
                    <p>
                        <label for="timezone">Alege fus orar</label>
                        <label class="wrap-input">
                            <select name="timezone" id="timezone">
                                <?php foreach($timezone_identifiers as $id=>$name): ?>
                                    <option value="<?php echo $name ?>" <?php echo fn_UI::selected_or_not($name, FN_TIMEZONE); ?>>
                                        <?php echo str_replace('_', ' ',$name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </p>

                    <p>
                        <button class="btn btn-primary" type="submit">Salveaz&#259;</button>
                    </p>

                </fieldset>

            </form>

        <?php endif; ?>

        <?php if ($tab == 'thresholds') include_once 'settings-thresholds.php'; ?>

        <?php if ($tab == 'myaccount') include_once 'settings-access.php'; ?>

	</div>

	<?php include_once ( FNPATH . '/snippets/sidebar.php' ); ?>

</div>