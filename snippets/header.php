<?php if ( !defined('FNPATH') ) exit();

$active=array(); $p = get('p');

if ( strlen($p) ):
    $active[$p] = 'active';
else:
    $active['index'] = 'active'; endif; ?>

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
                <a class="navbar-brand <?php echo av($active, 'index'); ?>" href="index.php">
                    &nbsp;FinFlow
                </a>
            </div>
            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class="<?php echo av($active, 'transactions'); ?>"><a href="<?php fn_UI::page_url('transactions'); ?>">Tranzac&#355;ii</a></li>
                    <li class="<?php echo av($active, 'performance'); ?>"><a href="<?php fn_UI::page_url('performance'); ?>">Performan&#355;&#259;</a></li>
                    <li class="<?php echo av($active, 'labels'); ?>"><a href="<?php fn_UI::page_url('labels'); ?>">Etichete</a></li>
                    <li class="<?php echo av($active, 'accounts'); ?>"><a href="<?php fn_UI::page_url('accounts'); ?>">Conturi</a></li>
                    <!--- <li class="<?php echo av($active, 'contacts'); ?>"><a href="<?php fn_UI::page_url('contacts'); ?>">Contacte</a></li> --->
                    <li class="<?php echo av($active, 'currencies'); ?>"><a href="<?php fn_UI::page_url('currencies'); ?>">Monede</a></li>
                    <li class="<?php echo av($active, 'tools'); ?>"><a href="<?php fn_UI::page_url('tools'); ?>">Unelte</a></li>
                    <li class="<?php echo av($active, 'settings'); ?>"><a href="<?php fn_UI::page_url('settings'); ?>">Set&#259;ri</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="<?php fn_UI::page_url('logout'); ?>" title="de-autentificare">
                            <span class="fa fa-power-off"></span>
                        </a>
                    </li>
                    <li>
                        <a onclick="fn_popup('<?php echo FN_URL; ?>/help/<?php echo $p; ?>.html#<?php echo get('t'); ?>');" href="#help" title="ajutor">
                            <span class="fa fa-question-circle"></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo fn_UI::get_file_url('/setup/upgrade.php', false); ?>" title="verifica actualizari">
                            <span class="fa fa-cloud-download"></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

