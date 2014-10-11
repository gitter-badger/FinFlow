<?php
/**
 * FinFlow 1.0 - Helper for adding a new currency rate
 * @author Adrian S. (http://www.gridwave.co.uk/)
 * @version 1.0
 */

include_once '../inc/init.php';

$_SESSION['currencies_added'] = isset( $_SESSION['currencies_added'] ) ? $_SESSION['currencies_added'] : array();

if( ! fn_User::is_authenticated() ) die('Please log in'); ?>

<!DOCTYPE html>
<html charset="utf-8">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/bootstrap.min.css" />
</head>
<body style="padding: 10%">

<?php
global $fndb, $fnsql;

if( count($_POST) ){

    $errors = array();

    $_SESSION['currencies_added'] = isset( $_SESSION['currencies_added'] ) ? array_merge($_SESSION['currencies_added'], array($_POST['currency_id'])) : array();

    if( empty($_POST['currency_id']) )
        die('Please enter a currency ID');

    if( empty($_POST['regdate']) )
        die('Please enter the rate date');

    if( @strtotime($_POST['regdate']) < 0 )
        die('Please enter a valid rate date');

    $regdate = date(FN_MYSQL_DATE, @strtotime($_POST['regdate']));

    $data = array(
        'currency_id' => intval($_POST['currency_id']),
        'regdate' => $regdate,
        'cexchange' => floatval($_POST['cexchange']),
    );

    $fnsql->insert(fn_Currency::$table_history, $data);

    if( $fndb->execute_query( $fnsql->get_query() ) ){
        echo('<p>Rate successfully added.</p>');
    }
    else{ //value already exists ( try update)

        $fnsql->update(fn_Currency::$table_history, array('cexchange' => floatval($_POST['cexchange'])), array('currency_id' => intval($_POST['currency_id']), 'regdate' => $regdate));

        if( $fndb->execute_query( $fnsql->get_query() ) )
            echo('<p>Rate successfully added.</p>');
        else
            die ( '<p> SQL Error: ' . $fndb->error . '</p>' );
    }


}

$currencies        = fn_Currency::get_all(true);
$next_rate_date = isset($regdate) ? date('Y-m-d', @strtotime("+5 days", @strtotime($regdate))) : '2013-01-01';
$next_row         = ( count($_SESSION['currencies_added']) == count($currencies) );

if( $next_row ){
    $frm_submit = basename(__FILE__) . '?new=1'; unset( $_SESSION['currencies_added'] );
}else
    $frm_submit = basename(__FILE__);

?>


<form method="post" action="<?php echo $frm_submit; ?>" target="_self">

    <p><a href="http://www.xe.com/currencytables/?from=<?php echo fn_Currency::get_default_cc(); ?>&date=<?php echo $next_rate_date; ?>">View</a> </p>

    <p>
        <label for="currency_id">Currency:</label>
        <select name="currency_id" id="currency_id">
            <?php if( count($currencies) ) foreach($currencies as $currency): $last_added = null; if( $currency->currency_id == $_POST['currency_id'] ) $last_added = $currency; ?>
                <option value="<?php echo $currency->currency_id; ?>"><?php echo $currency->ccode; ?></option>
            <?php endforeach; ?>
        </select>

        <?php if( ! $next_row and $last_added ) echo 'Last Added : ' . $last_added->ccode; ?>

    </p>

    <p>
        <label for="regdate">Date:</label>
        <input type="date" name="regdate" id="regdate" value="<?php echo $next_row ? $next_rate_date : $_POST['regdate']; ?>"/>
    </p>

    <p>
        <label for="cexchange">Rate:</label>
        <input type="number" name="cexchange" id="cexchange" step="any" value=""/>
    </p>

    <p>
        <button type="submit">Submit</button>
    </p>

</form>

<pre><?php print_r($_SESSION['currencies_added']); ?></pre>

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo FN_URL; ?>/js/bootstrap.min.js"></script>

</body>
</html>