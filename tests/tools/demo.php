<?php
/**
 * FinFlow 1.0 - [file description]
 * @author Adrian S. (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

define('FN_IS_CRON', true);
define('IS_CRON', true);

include_once '../../system/init.php';

use FinFlow\UI;
use FinFlow\OP;

echo '<pre>';

$static_labels = OP::get(19, 'labels');

print_r($static_labels);