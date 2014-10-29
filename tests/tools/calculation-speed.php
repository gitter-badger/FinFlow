<?php
/**
 * Testing calculations speeds
 */

include_once '../../inc/init.php';

set_time_limit(0);

echo '<div style="font-size: 1.3em; font-family: monospace;">';

$tstart = time();

$defaultCurrency = fn_Currency::get_default();

$trans_num = fn_OP::get_total(array());

$income     = fn_OP::get_sum(array('type'=>FN_OP_IN));
$outcome   = fn_OP::get_sum(array('type'=>FN_OP_OUT));

$balance = fn_OP::get_balance();

echo "<p>Found {$trans_num} transactions</p>";
echo "<p>Income is {$defaultCurrency->ccode} {$income}</p>";
echo "<p>Outcome is {$defaultCurrency->ccode} {$income}</p>";


//$Income

$tend = time();

$execTime = $tend - $tstart;

echo "<p>Execution time {$execTime} seconds</p>";