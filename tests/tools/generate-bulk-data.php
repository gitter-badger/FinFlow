<?php
/**
 * FinFlow Test suite - Generates bulk data and fills it in the database
 * @version 1.2
 */

include_once ('../../inc/init.php');
include_once ('../inc/class.cronassistant.php');

set_time_limit(0);

$main_step = isset($_GET['step']) ? intval($_GET['step']) : 1;
$baseurl     = $_SERVER['REQUEST_URI'];

if( strlen($_SERVER['QUERY_STRING']) ){
    $baseurl = trim(str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']), '?');
}

function next_step(){

    if( fn_CronAssistant::is_cli() ) return; global $main_step, $baseurl; $url = ( $baseurl . '?step=' . ( $main_step + 1 ) ); ?>
        <h3>Generating bulk data...</h3>
        <p style="text-align: center"><img src="<?php fn_UI::asset_url('images/preloader.gif'); ?>"/></p>
        <script type="text/javascript">setTimeout("window.location.href='<?php echo $url; ?>'", 3000)</script>
    <?php die();

}

define('FAKER_AUTOLOAD', 'faker/autoload.php');
define('CURPATH', dirname(__FILE__));

define('REF_AMOUNT', 500);

if ( ob_get_level() == 0 ) ob_start();

if( file_exists( FAKER_AUTOLOAD ) ) {

    require_once FAKER_AUTOLOAD;

    $faker = Faker\Factory::create();

    if( $main_step == 1 ){

        //--- insert currencies ---//
        $currenciesSQL     = file_get_contents(CURPATH . '/assets/currencies.sql');
        $currencies_saved = $fndb->execute_multi_query( $currenciesSQL );
        //--- insert currencies ---//

        //--- insert currencies history ---//
        if( $currencies_saved ) {
            $currenciesHistorySQL = file_get_contents(CURPATH . '/assets/currencies_history.sql');
            $currencies_saved = $fndb->execute_multi_query( $currenciesHistorySQL );
        }
        //--- insert currencies history ---//

        if( !$currencies_saved ) fn_UI::fatal_error("Could not save currencies data.");

        next_step();
    }

    //--- get the list of available currencies ---//
    $db_currencies = fn_Currency::get_all(); $currencies = array(); if( count($db_currencies) ) foreach($db_currencies as $currency) $currencies[] = $currency->currency_id;
    //--- get the list of available currencies ---//

    //--- get the list of available labels ---//
    $db_labels = fn_Label::get_all(0, 9999); $labels = array(); if( count($db_labels) ) foreach($db_labels as $label) $labels[] = $label->label_id;
    //--- get the list of available labels ---//

    //--- get the list of available accounts ---//
    $db_accounts = fn_Accounts::get_all(0, 9999); $accounts = array(); if( count($db_accounts) ) foreach($db_accounts as $account) $accounts[] = $account->account_id;
    //--- get the list of available accounts ---//

    if( count($currencies) <= 1){
        die("Please add some currencies before generating random transactions");
    }

    if( $main_step == 2 ){

        //--- insert a few labels ---//

        $labels = array(); $labels_amount = intval( REF_AMOUNT / 3 );

        for( $i=0; $i<$labels_amount; $i++ ){

            $label = $faker->company;
            $desc = $faker->text(25);

            fn_Label::add($label, $desc);

        }

        echo "<p>Created {$labels_amount} fake labels</p>"; ob_flush(); flush();

        //--- insert a few labels ---//

        next_step();

    }

    if( $main_step == 3 ){

        //--- insert a few accounts ---//
        $accounts = array(); $accounts_amount = intval( REF_AMOUNT / 10 );

        //Some IBAN codes
        $ibans = array(
            'AL47212110090000000235698741',
            'BG80BNBG96611020345678',
            'FO1464600009692713',
            'FI2112345600000785',
            'GL8964710001000206',
            'IT60X0542811101000000123456',
            'MC5813488000010051108001292',
            'SK3112000000198742637541',
            'GB29NWBK60161331926819',
            'DO28BAGR00000001212453611324',
            'ML03D00890170001002120000447',
            'AE260211000000230064016'
        );

        for( $i=0; $i<$accounts_amount; $i++ ){

            if( $faker->boolean(50) ) {
                $iban_n = $faker->numberBetween(0, count($ibans) -1);
                $iban    = $ibans[$iban_n];
            }
            else $iban = '';

            $swift = $faker->boolean(50) ? ( 'SWFT-' . $faker->domainWord ) : '';

            $currency_n = $faker->numberBetween(0, count($currencies) -1); $currency_id = $currencies[$currency_n];

            $holder = $faker->company; $slug = fn_Util::transliterate($holder, '-');

            $account = array(
                'account_iban'=> $iban,
                'account_currency_id'=> $currency_id,
                'account_slug'=> $slug,
                'holder_name'=> $holder,
                'holder_swift'=> $swift,
                'balance'=> $faker->numberBetween(100, 1000)
            );

            $accounts[] = fn_Accounts::add($account);

        }

        echo "<p>Created {$accounts_amount} fake accounts.</p>"; ob_flush(); flush();
        //--- insert a few accounts ---//

        next_step();

    }

    $trans_amount = intval( REF_AMOUNT );

    if( $main_step == 4 ){

        if( count($labels) <= 1 ) die("Oops! Only one label found!");
        if( count($accounts) <= 1 ) die("Oops! Only one account found!");

        //--- insert a few transactions ---//

        for( $i=0; $i<$trans_amount; $i++ ){

            $has_meta = $faker->boolean(50);
            $has_account = $faker->boolean(50);
            $has_labels = $faker->boolean(50);

            $type = $faker->boolean(50) ? FN_OP_IN : FN_OP_OUT;
            $value= $faker->randomFloat(2, 1, 99999);

            $currency_n = $faker->numberBetween(0, count($currencies) -1); $currency_id = $currencies[$currency_n];

            $date = date(FN_MYSQL_DATE, @strtotime("-" . $faker->numberBetween(2, 8000) . ' days'));

            $trans_id = fn_OP::add($type, $value, $currency_id, $faker->text(100), $date);

            if( $trans_id ) {

                if( $has_meta ) fn_OP::save_metadata($trans_id, 'details', $faker->text(500));

                if( $has_account ) {
                    $account_n = $faker->numberBetween(0, count($accounts) -1); $account_id = $accounts[$account_n];
                    if($account_id) fn_Accounts::add_trans($account_id, $trans_id);
                }

                if( $has_labels ){
                    $label_n = $faker->numberBetween(0, count($labels) -1); $label_id = $accounts[$label_n];
                    if($label_id) fn_OP::associate_label($trans_id, $label_id);
                }
            }

        }

        echo "<p>Created {$trans_amount} fake transactions.</p>"; ob_flush(); flush();
        //--- insert a few transactions ---//

        next_step();

    }

    if( $main_step == 5 ){

        //--- insert a few pending transactions ---//
        $recurring_opts = array('no', 'daily', 'monthly', 'yearly');

        for( $i=0; $i<$trans_amount; $i++ ){

            $has_meta       = $faker->boolean(50);
            $has_account   = $faker->boolean(50);
            $has_labels     = $faker->boolean(70);

            $type = $faker->boolean(50) ? FN_OP_IN : FN_OP_OUT;
            $value= $faker->randomFloat(2, 1, 80);

            $recurring_n =  $faker->numberBetween(0, count($recurring_opts) -1); $recurring = $recurring_opts[$recurring_n];

            $currency_n = $faker->numberBetween(0, count($currencies) -1); $currency_id = $currencies[$currency_n];

            $date = date(FN_MYSQL_DATE, @strtotime("-" . $faker->numberBetween(2, 8000) . ' days'));

            $trans_id = fn_OP::add($type, $value, $currency_id, $faker->text(100), $date);
            $trans_id = fn_OP_Pending::add($type, $value, $currency_id, $recurring, array());

            if( $trans_id ) {

                $metadata = array();

                if( $has_meta )
                    $metadata['details'] = $faker->text(500);

                if( $has_account ) {
                    $account_n = $faker->numberBetween(0, count($accounts) -1); $account_id = $accounts[$account_n];
                    if($account_id) $metadata['account_id'] = $account_id;
                }

                if( $has_labels ){
                    $label_n = $faker->numberBetween(0, count($labels) -1); $label_id = $labels[$label_n];
                    if($label_id) $metadata['labels'][] = $label_id;
                }

                $metadata = @serialize($metadata); fn_OP_Pending::update($trans_id, array('metadata'=>$metadata));

                if( $_POST['recurring'] != 'no' )
                    $children = fn_OP_Pending::add_children($trans_id); //Add a children as instance of the recurring transaction
                else
                    $children = $trans_id;
            }

        }

        echo "<p>Created {$trans_amount} fake pending transactions.</p>"; ob_flush(); flush();

        //--- insert a few pending transactions ---//

        next_step();
    }

    echo '<p> <strong>Finished with ' . ( $labels_amount + $accounts_amount + $trans_amount ) . ' fake data generated.</strong></p>';

}
else
    fn_CronAssistant::cron_error('Could not find Faker autoloader. You can get Faker from here <a href="https://github.com/fzaninotto/Faker">https://github.com/fzaninotto/Faker</a>!',fn_CronAssistant::is_running_in_window());