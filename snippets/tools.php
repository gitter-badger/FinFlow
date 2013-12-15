<?php if ( !defined('FNPATH') ) exit();

$tab = isset($_GET['t']) ? urldecode($_GET['t']) : 'import'; $activetab = array(); $activetab[$tab] = 'active';

?>

<div class="row content">
    <div class="span10">

        <ul class="nav nav-tabs">
            <li class="<?php echo $activetab['import']; ?>"><a href="<?php fn_UI::page_url('tools', array('t'=>'import'))?>">Import</a></li>
            <li class="<?php echo $activetab['export']; ?>"><a href="<?php fn_UI::page_url('tools', array('t'=>'export'))?>">Export </a></li>
            <!--- <li class="<?php echo $activetab['backup']; ?>"><a href="<?php fn_UI::page_url('tools', array('t'=>'backup'))?>">Backup </a></li> --->
        </ul>

        <?php if ( $tab == 'import' ) include_once 'tools-import.php'; ?>

        <?php if ( $tab == 'export' ) include_once 'tools-export.php'; ?>

        <?php if ( $tab == 'backup' ) include_once 'tools-backup.php'; ?>

    </div>

    <?php include_once ( FNPATH . '/snippets/sidebar.php' ); ?>

</div>