<?php

if ( !defined('FNPATH') ) exit;

use FinFlow\User;

User::logout();

?>
<div class="row">
	<div class="col-lg-12">
		<div class="alert alert-info">Ai fost de-autentificat...</div>
		<script type="text/javascript">window.location.href='<?php UI::page_url('login'); ?>';</script>
	</div>
</div>

