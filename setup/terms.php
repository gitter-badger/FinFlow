<?php include_once 'header.php'; ?>

    <p align="center" class="logo">
        <img src="<?php echo FN_URL; ?>/images/finflow-logo.png" width="50" align="absmiddle" alt="FinFlow"/>
        <br/>FinFlow
    </p>

    <h3 align="center">Termeni de utilizare</h3>

    <div class="terms" style="background: #fafafa; height: 300px; overflow: auto; padding: 10px; border: 1px #cccccc solid; border-radius: .3em; font-size: 87%;">
        <?php echo @file_get_contents('license.htm'); ?>
    </div>

    <br class="clear"/>

    <p style="text-align: center;">
        <button class="btn" onclick="window.location.href='install.php';"> <span class="icon-arrow-left"></span> Inapoi</button>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <button class="btn btn-success" onclick="window.location.href='requirements.php';">
            Am &#238;n&#539;eles termenii, continu&#259; instalarea <span class="icon-arrow-right"></span>
        </button>
    </p>

    <hr/>


<?php include_once 'footer.php'; ?>