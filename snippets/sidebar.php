<?php if ( !defined('FNPATH') ) exit; ?>
<div class="<?php fn_UI::sidebar_grid_class('col-lg-3 col-md-3 col-sm-4'); ?> sidebar panel-group">

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

    <div class="widget panel panel-default widget-calculator">
        <div class="widget-title panel-heading">Calculator</div>
        <div class="widget-content panel-body"><?php include_once 'widget-calculator.php';?></div>
    </div>

    <div class="widget panel panel-default widget-wclock">
        <div class="widget-title panel-heading">Ceas</div>
        <div class="widget-content panel-body"><?php include_once 'widget-wclock.php';?></div>
    </div>

    <div class="widget panel panel-default widget-converter">
        <div class="widget-title panel-heading">Convertor valutar</div>
        <div class="widget-content panel-body"><?php include_once 'widget-converter.php';?></div>
	</div>
	
</div>
