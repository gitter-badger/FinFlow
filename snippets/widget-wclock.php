<?php if ( !defined('FNPATH') ) include_once '../inc/init.php'; ?>
<?php if ( !fn_User::is_authenticated() ) exit(); ?>
<p>
	<select name="clock_tz" id="clock_tz" class="widget-input-fullwidth">
		<option value="<?php echo fn_Util::get_timezone_offset('Europe/Bucharest', FN_TIMEZONE); ?>">Bucure&#351;ti</option>
		<option value="<?php echo fn_Util::get_timezone_offset('Europe/London', FN_TIMEZONE); ?>">Londra</option>
		<option value="<?php echo fn_Util::get_timezone_offset('America/New_York', FN_TIMEZONE); ?>">New York</option>
		<option value="<?php echo fn_Util::get_timezone_offset('Asia/Tokyo', FN_TIMEZONE); ?>">Tokyo</option>
		<option value="<?php echo fn_Util::get_timezone_offset('Asia/Hong_Kong', FN_TIMEZONE); ?>">Hong Kong</option>
		<option value="<?php echo fn_Util::get_timezone_offset('Europe/Moscow', FN_TIMEZONE); ?>">Moscova</option>
		<option value="<?php echo fn_Util::get_timezone_offset('America/Los_Angeles', FN_TIMEZONE); ?>">Los Angeles</option>
		<option value="<?php echo fn_Util::get_timezone_offset('America/Sao_Paulo', FN_TIMEZONE); ?>">Sao Paulo</option>
	</select>
	<strong id="displayWClock" class="widget-inline-blockclear"><?php echo date('H:i:s'); ?></strong>
</p>