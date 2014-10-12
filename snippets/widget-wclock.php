<?php if ( !defined('FNPATH') ) include_once '../inc/init.php'; ?>
<?php if ( !fn_User::is_authenticated() ) exit();

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
    $city = @explode('/', FN_TIMEZONE); $city = $city[1]; $city = str_replace('_', ' ', $city); $dtimezones[FN_TIMEZONE] = $city;
}

?>
<form class="form-horizontal">
    <div class="form-group">
        <select name="clock_tz" id="clock_tz" class="form-control">
            <?php foreach($dtimezones as $tz=>$city): ?>
                <option value="<?php echo fn_Util::get_timezone_offset($tz, FN_TIMEZONE); ?>" <?php echo fn_UI::selected_or_not($tz, FN_TIMEZONE); ?>>
                    <?php echo $city; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
	    <output  id="displayWClock" class="form-control form-output">--:--:--</output>
    </div>
</form>