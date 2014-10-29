<?php if ( !defined('FNPATH') ) exit();

$tab = isset($_GET['t']) ? urldecode($_GET['t']) : 'import'; $activetab = array(); $activetab[$tab] = 'active'; ?>

<div class="row">
    <div class="<?php fn_UI::main_container_grid_class(); ?>">

        <ul class="nav nav-tabs">
            <li class="<?php echo av($activetab, 'import'); ?>"><a href="<?php fn_UI::page_url('tools', array('t'=>'import'))?>">Import</a></li>
            <li class="<?php echo av($activetab, 'export'); ?>"><a href="<?php fn_UI::page_url('tools', array('t'=>'export'))?>">Export </a></li>
            <!--- <li class="<?php echo av($activetab, 'backup'); ?>"><a href="<?php fn_UI::page_url('tools', array('t'=>'backup'))?>">Backup </a></li> --->
        </ul>

        <?php fn_UI::msg("Atentie! Aceasta catacteristica este experimentala.", fn_UI::$MSG_WARN); ?>

        <?php if ( $tab == 'import' ) include_once 'tools-import.php'; ?>

        <?php if ( $tab == 'export' ) include_once 'tools-export.php'; ?>

        <?php if ( $tab == 'backup' ) include_once 'tools-backup.php'; ?>

    </div>

    <?php include_once ( FNPATH . '/snippets/sidebar.php' ); ?>

</div>