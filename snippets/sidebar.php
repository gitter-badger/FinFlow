<?php if ( !defined('FNPATH') ) exit; ?>
<div class="<?php fn_UI::sidebar_grid_class('col-lg-offset-1 col-md-offset-1 col-sm-offset-1 col-lg-2 col-md-2 col-sm-4'); ?> sidebar">

    <div class="widget">
        <div class="widget-title"><h3>Legaturi rapide</h3></div>
        <div class="widget-content">
            <ul class="list-unstyled">
                <li><a href="https://mail.google.com" target="_blank">GMail</a></li>
                <li><a href="https://mail.yahoo.com" target="_blank">Yahoo! Mail</a></li>
                <li><a href="https://paypal.com" target="_blank">PayPal</a></li>
                <li><a href="https://www.homebank.ro/" target="_blank">HomeBank&trade;</a></li>
                <li><a href="http://www.finflow.org/" target="_blank">FinFlow.org</a></li>
            </ul>
        </div>
    </div>

    <div class="widget">
        <div class="widget-title"><h3>Ceas</h3></div>
        <div class="widget-content"><?php include_once 'widget-wclock.php';?></div>
    </div>
	
	<div class="widget">
        <div class="widget-title"><h3>Convertor valutar</h3></div>
        <div class="widget-content"><?php include_once 'widget-converter.php';?></div>
	</div>
	
</div>
