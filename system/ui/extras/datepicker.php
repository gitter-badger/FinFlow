<?php
/**
 * FinFlow 1.0 - Renders datepicker js
 * @author Adrian S. (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

use FinFlow\UI;

$selector = isset($selector) ? $selector : '.datepicker';

$min_default = new DateTime(date('Y-m-d', strtotime('-4 years')));
$max_default = new DateTime(date('Y-m-d', strtotime('+4 years')));

$min = isset($min) ? $min : $min_default;
$max = isset($max) ? $max : $max_default;

if( is_string($min) )
	$min = new DateTime($min);

if( is_string($max) )
	$max = new DateTime($max);

$theme = isset($theme) ? $theme : 'default';

$onset   = isset($onset) ? $onset : 'undefined';
$onclose = isset($onclose) ? $onclose : 'undefined';

//enqueue js assets
UI::enqueue_js('assets/js/moment.min.js');
UI::enqueue_js('assets/js/pickdate-picker.js');
UI::enqueue_js('assets/js/pickdate-picker.date.js');
UI::enqueue_js('assets/js/pickdate-legacy.js');

//enqueue css assets
UI::enqueue_css('assets/css/pickadate-' . $theme . '.css');
UI::enqueue_css('assets/css/pickadate-' . $theme . '.date.css');

//init datepicker

$min_date = ( '[' . $min->format('Y') . ', ' . $min->format('m') . ', ' . $min->format('d') . ']');
$max_date = ( '[' . $max->format('Y') . ', ' . $max->format('m') . ', ' . $max->format('d') . ']');

$config = <<<JS
    $(".datepicker").pickadate({
		min: $min_date,
	    max: $max_date,
	    format: 'yyyy-mm-dd',
		formatSubmit: 'yyyy-mm-dd',
		onSet: $onset,
		onClose: $onclose,
	});
JS;

//$inline = str_replace('min_date', $min_date, $inline);

UI::enqueue_inline($config, 'script');