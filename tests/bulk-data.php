<?php
/**
 * Generates bulk data and fills the database
 */

include_once '../inc/init.php';

set_time_limit(0);

define('FAKER_AUTOLOAD', '../../thirdparty/Faker-master/src/autoload.php');
define('CURPATH', dirname(__FILE__));

define('REF_AMOUNT', 5000);

if( file_exists( FAKER_AUTOLOAD ) ) {

    require_once FAKER_AUTOLOAD;

    $faker = Faker\Factory::create();

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

    //--- get the list of available currencies ---//
    $db_currencies = fn_Currency::get_all(); $currencies = array();
    if( count($db_currencies) ) foreach($db_currencies as $currency) $currencies[] = $currency->currency_id;
    //--- get the list of available currencies ---//

    echo '<div style="font-size: 1.3em; font-family: monospace;">';

    //--- insert a few labels ---//

    $labels = array(); $labels_amount = intval( REF_AMOUNT / 3 );

    for( $i=0; $i<$labels_amount; $i++ ){

        $label = $faker->company;
        $desc = $faker->text(25);

        $labels[] = fn_Label::add($label, $desc); //TODO

    }

    echo "<p>Created {$labels_amount} fake labels</p>";

    //--- insert a few labels ---//

    //--- insert a few accounts ---//
    $accounts = array(); $accounts_amount = intval( REF_AMOUNT / 10 );

    $ibans = array(
        'AL47212110090000000235698741', 'BG80BNBG96611020345678', 'FO1464600009692713', 'FI2112345600000785',
        'GL8964710001000206', 'IT60X0542811101000000123456', 'MC5813488000010051108001292', 'SK3112000000198742637541',
        'GB29NWBK60161331926819', 'DO28BAGR00000001212453611324', 'ML03D00890170001002120000447', 'AE260211000000230064016'
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

        $accounts[] = fn_Accounts::add($account); //TODO

    }

    echo "<p>Created {$accounts_amount} fake accounts</p>";
    //--- insert a few accounts ---//

    //--- insert a few transactions ---//
    $trans_amount = intval( REF_AMOUNT );

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

    echo "<p>Created {$trans_amount} fake transactions</p>";
    //--- insert a few transactions ---//

    echo '<p><strong>Finished with ' . ( $labels_amount + $accounts_amount + $trans_amount ) . ' fake data generated.</strong></p>';

}
else fn_UI::fatal_error("Could not find Faker autoloader!");