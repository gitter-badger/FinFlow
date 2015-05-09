<?php
/**
 * FinFlow 1.0 - Task initialization file
 * @author Adrian S. (adrian@finflow.org)
 * @version 1.0
 */


define('INCPATH'   , rtrim(str_replace(basename(dirname(__FILE__)), '', dirname(__FILE__)), DIRECTORY_SEPARATOR));
define('FN_IS_CRON', 7);
define('IS_CRON'   , 7);

include_once (INCPATH . '/init.php');