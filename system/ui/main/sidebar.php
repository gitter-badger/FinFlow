<?php
/**
 * Renders the sidebar
 */

if ( !defined('FNPATH') ) exit;

use FinFlow\UI;

?>

<div class="col-lg-4 aside sidebar sidebar-right panel-group">

    <div class="widget panel panel-default widget-upcoming" id="">
        <div class="widget-title panel-heading">
            Tranzactii in asteptare
        </div>
        <ul class="widget-content list-group">
            <li class="list-group-item">Cras justo odio</li>
            <li class="list-group-item">Dapibus ac facilisis in</li>
            <li class="list-group-item">Morbi leo risus</li>
            <li class="list-group-item">Porta ac consectetur ac</li>
            <li class="list-group-item">Vestibulum at eros</li>
        </ul>
    </div>

    <div class="widget panel panel-default widget-links">
        <div class="widget-title panel-heading">Legaturi rapide</div>
        <div class="widget-content panel-body">
            <ul class="list-unstyled">
                <li><a href="https://mail.google.com" target="_blank">GMail</a></li>
                <li><a href="https://mail.yahoo.com" target="_blank">Yahoo! Mail</a></li>
                <li><a href="https://paypal.com" target="_blank">PayPal</a></li>
                <li><a href="https://www.homebank.ro/" target="_blank">HomeBank&trade;</a></li>
                <li><a href="http://www.finflow.org/" target="_blank">FinFlow.org</a></li>
            </ul>
        </div>
    </div>

	<!--
    <div class="widget panel panel-default widget-calculator">
        <div class="widget-title panel-heading">Calculator</div>
        <div class="widget-content panel-body"><?php UI::component('widgets/widget-calculator'); ?></div>
    </div>
    -->

    <div class="widget panel panel-default widget-wclock">
        <div class="widget-title panel-heading">Ceas</div>
        <div class="widget-content panel-body">
	        <?php UI::component('widgets/widget-wclock'); ?>
        </div>
    </div>

    <div class="widget panel panel-default widget-converter">
        <div class="widget-title panel-heading">Convertor valutar</div>
        <div class="widget-content panel-body">
	        <?php UI::component('widgets/widget-converter'); ?>
        </div>
	</div>
	
</div>
