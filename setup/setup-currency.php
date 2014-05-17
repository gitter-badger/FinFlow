<?php
/**
 * FinFlow 1.0 - Default currency setup file
 * @author Adrian S. (https://www.amsquared.co.uk/)
 * @version 1.0
 */




include_once 'header.php';
include_once '../inc/class.currency.php';
include_once '../inc/exr-init.php';



global $fnexr; $errors = array(); $success = false; $choiceDefaultCurrency = true;


if( count($_POST) ){
   if( fn_Installer::setup_default_currency( $_POST['currency_default'] ) )
       $success = true;
    else
        $errors[] = 'Moneda implicita nu poate fi configurata. Verifica conexiunea cu baza de date si incearca din nou.';
}

$currencies = $fnexr->getAvailableCurrencies();

//--- if the parser is BNR the default currency will be RON ---//
if( $fnexr::id == BNR_ExchangeRateParser::id ){
    $choiceDefaultCurrency = false; $currencies[] = $_POST['currency_default'] = BNR_ExchangeRateParser::defaultCurrency;
}
//--- if the parser is BNR the default currency will be RON ---//


//--- check of we can change the default currency for OER ---//
if( $fnexr::id == OER_ExchangeRateParser::id and !$fnexr->setBaseCurrency('EUR') ){
    $choiceDefaultCurrency = false; $currencies[] = $_POST['currency_default'] = OER_ExchangeRateParser::defaultCurrency;
}
//--- check of we can change the default currency for OER ---//

?>

    <p align="center" class="logo">
        <img src="<?php echo FN_URL; ?>/images/finflow-logo.png" width="50" align="absmiddle" alt="FinFlow"/>
        <br/>FinFlow
    </p>

    <h3 align="center">Alegerea monezii implicite </h3>

    <?php fn_UI::show_errors( $errors ); ?>

    <?php if( !$success ): ?>
        <form name="setup-default-currency" id="setupDefaultCurrency" method="post" target="_self">
            <p>
                <label for="currency_default">Moneda implicit&#259;:</label>

                <?php if( $choiceDefaultCurrency ): ?>
                <select name="currency_default" id="currency_default">
                    <?php foreach($currencies as $code): ?>
                    <option value="<?php echo $code; ?>" <?php fn_UI::selected_or_not($_POST['currency_default'], $code) ?>><?php echo $code; ?></option>
                    <?php endforeach; ?>
                </select>
                <?php else: ?>
                    <input type="text" readonly name="currency_default" id="currency_default" value="<?php echo $fnexr::defaultCurrency; ?>">
                <?php endif; ?>
            </p>

            <p>
                <em>Moneda implicita disponibila depinde de parserul ales. De exemplu parserul BNR, suporta doar RON ca moneda implicita.</em>
            </p>

            <p>
                De asemenea moneda implicit&#259; nu mai poate fi schimbat&#259; ulterior, &#351;i
                este recomandat s&#259; alegi moneda &#238;n care preferi s&#259;-&#355;i p&#259;strezi economiile.
            </p>

            <p style="text-align: center;">
                <button class="btn" type="button" onclick="window.location.href='setup-exchangerate-parser.php';">
                    <span class="icon-arrow-left"></span> &#206;napoi
                </button>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <button class="btn btn-success" type="submit">
                    Continu&#259; <span class="icon-arrow-right"></span>
                </button>
            </p>

        </form>
    <?php else: ?>

        <p class="msg note"> Fi&#351;ierul de configurare a fost creat cu succes. </p>
        <script type="text/javascript">
            window.location.href = '<?php echo FN_URL; ?>/setup/setup-user.php';
        </script>

    <?php endif; ?>

    <hr/>

<?php include_once 'footer.php'; ?>