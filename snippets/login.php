<?php if( !defined('FNPATH') ) exit; ?>
<div class="row content">
	<div class="span2">&nbsp;</div>
	
	<div class="span8">
	
		<div class="inner-container container-login">
			<h2 align="center" class="brand">FinFlow</h2>
		
			<?php 
			
				if (count($_POST)){
				
					$errors 	= 	array();
					$notice	=	array();
					
					if ( empty($_POST['email']) ) $errors[] = "Email-ul lipse&#351;te.";
					if ( empty($_POST['pass']) ) 	$errors[] = "Parola lipse&#351;te.";
					
					if ( empty($errors) ){

						if ( fn_User::authenticate($_POST['email'], $_POST['pass']) )
							$notice[] = "Ai fost autentificat...";
						else 
							$errors[] = "Verific&#259; email-ul &#351;i parola. Cel pu&#355;in una dintre ele este gre&#351;it&#259;.";

					}
					
				}
				
				fn_UI::show_errors($errors); fn_UI::show_notes($notes);
			?>
			
			<?php if (fn_User::is_authenticated() ): ?>
				<script type="text/javascript">window.location.href='<?php fn_UI::page_url('dashboard'); ?>';</script>
			<?php else:?>
		
			<form target="_self" method="post" name="settings-form" id="settingsForm">
				<p align="center">
					<label for="email">Email:</label>
					<input type="text" size="45" maxlength="255" name="email" id="email" value="<?php echo fn_UI::extract_post_val('email');?>" />
				</p>
				<p align="center">
					<label for="pass">Parola:</label>
					<input type="password" size="45" maxlength="255" name="pass" id="pass" value="" />
				</p>
				
				<p class="submit" align="center">
					<button class="btn submit btn-primary" type="submit">Autentificare</button> &nbsp;&nbsp;&nbsp;&nbsp;
					<button class="btn" type="button" onclick="window.location.href='index.php?p=pwreset';">Am uitat parola...</button>
				</p>
			</form>
			<?php endif; ?>
			
		</div>
	</div>
	<div class="span2">&nbsp;</div>
</div>