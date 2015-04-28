<?php
/**
 * Renders the application header
 */

if ( !defined('FNPATH') ) exit();

use FinFlow\UI;

$active = array(); $p = url_part(1);

if ( strlen($p) )
    $active[$p]= 'active';
else
    $active['index'] = 'active';

?>

<div class="header">
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand <?php echo av($active, 'index'); ?>" href="<?php UI::url('/dashboard'); ?>">
                    &nbsp;FinFlow
                </a>
            </div>
            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class="<?php echo av($active, 'transactions'); ?>">
	                    <a href="<?php UI::url('transactions'); ?>">Tranzac&#355;ii</a>
                    </li>
                    <li class="<?php echo av($active, 'performance'); ?>">
	                    <a href="<?php UI::url('performance'); ?>">Performan&#355;&#259;</a>
                    </li>
                    <li class="<?php echo av($active, 'labels'); ?>">
	                    <a href="<?php UI::url('labels'); ?>">Etichete</a>
                    </li>
                    <li class="<?php echo av($active, 'accounts'); ?>">
	                    <a href="<?php UI::url('accounts'); ?>">Conturi</a>
                    </li>
                    <li class="<?php echo av($active, 'contacts'); ?>">
	                    <a href="<?php UI::url('contacts'); ?>">Contacte</a>
                    </li>
                    <li class="<?php echo av($active, 'currencies'); ?>">
	                    <a href="<?php UI::url('currencies'); ?>">Monede</a>
                    </li>
                    <li class="<?php echo av($active, 'tools'); ?>">
	                    <a href="<?php UI::url('tools'); ?>">Unelte</a>
                    </li>
                    <li class="<?php echo av($active, 'settings'); ?>">
	                    <a href="<?php UI::url('settings'); ?>">Set&#259;ri</a>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="<?php UI::url('logout'); ?>" title="de-autentificare">
                            <span class="fa fa-power-off"></span>
                        </a>
                    </li>
                    <li>
                        <a onclick="fn_popup('<?php echo FN_URL; ?>/help/<?php echo $p; ?>.html#<?php echo get('t'); ?>');" href="#help" title="ajutor">
                            <span class="fa fa-question-circle"></span>
                        </a>
                    </li>
	                <!--
                    <li>
                        <a href="<?php echo UI::get_file_url('/setup/upgrade.php', false); ?>" title="verifica actualizari">
                            <span class="fa fa-cloud-download"></span>
                        </a>
                    </li>
                    -->
                </ul>
            </div>
        </div>
    </div>
</div>

