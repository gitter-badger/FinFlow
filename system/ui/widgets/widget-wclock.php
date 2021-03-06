<?php

if ( !defined('FNPATH') ) exit();

use FinFlow\Util;
use FinFlow\UI;

$dtimezones = array(
    'Europe/Bucharest'      => 'Bucure&#351;ti',
    'Europe/London'         => 'Londra',
    'America/New_York'   => 'New York',
    'Asia/Tokyo'               => 'Tokyo',
    'Asia/Hong_Kong'        => 'Hong Kong',
    'Europe/Moscow'        => 'Moscova',
    'America/Los_Angeles' => 'Los Angeles',
    'America/Sao_Paulo'    => 'Sao Paulo'
);

if( array_key_exists(FN_TIMEZONE, $dtimezones) ); else {
    $city = @explode('/', FN_TIMEZONE); $city = $city[1];
	$city = str_replace('_', ' ', $city); $dtimezones[FN_TIMEZONE] = $city;
}
$timezone = new DateTimeZone(FN_TIMEZONE);
$currTime = new DateTime('now', $timezone);

?>

<form class="form">

	<div class="form-group">
		<output id="displayWClock" class="form-control-static form-output"><?php echo $currTime->format('m:s'); ?></output>
	</div>

    <div class="form-group">
        <label class="sr-only">Alege oras</label>
        <select name="clock_tz" id="clock_tz" class="form-control">
            <option value="0">Ora locala</option>
            <?php foreach($dtimezones as $tz=>$city): ?>
                <option value="<?php echo Util::get_timezone_offset($tz, FN_TIMEZONE); ?>" <?php echo UI::selected_or_not($tz, FN_TIMEZONE); ?>>
                    <?php echo $city; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

</form>