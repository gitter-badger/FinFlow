<?php if ( !defined('FNPATH') ) exit; fn_User::logout(); ?>
<div class="row content">
	<div class="span12">
		<p class="msg">Ai fost de-autentificat...</p>
		<script type="text/javascript">window.location.href='<?php fn_UI::page_url('login'); ?>';</script>
	</div>
</div>

