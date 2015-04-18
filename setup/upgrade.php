<?php

include_once '../inc/init.php';
include_once ( FNPATH . '/inc/class.installer.php' );
include_once ( FNPATH . '/inc/class.upgrade.php' );

if( !fn_User::is_authenticated() ) exit; //only authenticated users can do upgrade

$latest = fn_Upgrader::upgrade_available();

if( isset($_GET['ustart']) and ( $_GET['ustart'] == 1 ) ){
    fn_Upgrader::start($latest); exit;
}

if( isset($_GET['ustatus']) and ( $_GET['ustatus'] == 1 ) ){
    echo fn_Upgrader::get_status(true); exit;
}

include_once 'header.php'; ?>

    <p align="center" class="logo">
        <img src="<?php echo FN_URL; ?>/images/finflow-logo.png" width="50" align="absmiddle" alt="FinFlow"/>
        <br/>FinFlow
    </p>

<?php if( isset($_GET['execute']) and ( $_GET['execute'] == 1 ) ): ?>

    <h3 align="center">Actualizare la versiunea <?php echo $latest['version']; ?></h3>

    <?php fn_Upgrader::display_progress(); ?>

<?php else: ?>

    <?php if ($latest['version']): ?>

        <h3 align="center">Actualizare la versiunea <?php echo $latest['version']; ?></h3>

        <p>
            Este disponibil&#259; <em><a href="<?php echo fn_Upgrader::$changelogURL; ?>#version-<?php echo $latest['version']; ?>" target="_blank">versiunea <?php echo $latest['version']; ?> </a></em>.
            Aceasta aduce o serie de imbun&#259;t&#259;&#355;iri fa&#355;&#259; de <em>versiunea <?php echo FN_VERSION; ?></em> pe care o folose&#351;ti acum. <br/>
        </p>

        <p>
            &#206;nainte de a &#238;ncepe procesul de actualizare, &#238;&#355;i recomandam s&#259; faci
            <a href="<?php fn_UI::page_url('tools', array('t'=>'export')) ?>"> un backup</a>.
        </p>

        <br class="clear"/>

        <p style="text-align: center;">
            <button class="btn" onclick="window.location.href='../index.php';"> <span class="icon-arrow-left"></span> &#206;napoi</button>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <button class="btn btn-success" onclick="window.location.href='upgrade.php?execute=1';">
                Actualizeaz&#259; <span class="icon-arrow-right"></span>
            </button>
        </p>
    <?php else: ?>
        <p style="text-align: center;">
            Folose&#351;ti deja ultima versiune disponibil&#259;, <?php echo FN_VERSION ?>. <br/>
            Viziteaz&#259; <a href="http://www.finflow.org" target="_blank">www.finflow.org</a> pentru mai multe detalii.
        </p>

        <p style="text-align: center;">
            <button class="btn" onclick="window.location.href='../index.php';"> <span class="icon-arrow-left"></span> &#206;napoi</button>
        </p>

    <?php endif; ?>

<?php endif; ?>

<hr/>


<?php include_once 'footer.php'; ?>