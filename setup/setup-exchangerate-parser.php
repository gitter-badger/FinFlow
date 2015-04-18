<?php
/**
 * FinFlow 1.0 - Setup the exchange rate parser;
 * @author Adrian S. (http://adrian.silimon.eu/)
 * @version 1.0
 */

include_once 'header.php';
include_once (FNPATH . '/inc/interface.exr.php');

$errors = array(); $success = false;

if( count($_POST) ){

    $parser                = trim($_POST['selected_parser']);
    $availableParsers = fn_ExchangeRatesParserFactory::getAvailableImplementations();

    if( isset($parser)  and in_array($parser, $availableParsers)){

        //--- check if you have an OER API Key before we continue ----//
        if( ( $parser == 'oer' ) and empty($_POST['oer_api_key']) ){
            $errors[] = 'Serviciul OpenExchangeRates.com necesita un APP ID. <br/> Poti obtine unul gratuit prin inregistrarea pe saitul <a href="https://openexchangerates.org/">openexchangerates.org</a> .';
        }
        //--- check if you have an OER API Key before we continue ----//

        if( empty($errors) ){

            fn_Settings::set('exchange_rates_parser', $parser);
            fn_Settings::set('oer_api_key', trim( $_POST['oer_api_key'] ));

            $success = true;
        }

    }
    else $errors[] = 'Trebuie sa alegi un parser pentru a merge mai departe.';

}

?>

    <p align="center" class="logo">
        <img src="<?php echo FN_URL; ?>/images/finflow-logo.png" width="50" align="absmiddle" alt="FinFlow"/>
        <br/>FinFlow
    </p>

    <h3 align="center">Alegerea serviciului pentru actualizarea cursul valutar</h3>

    <?php fn_UI::show_errors( $errors ); ?>

    <?php if( !$success ): ?>
        <form name="setup-currency" id="setupCurrencyDefaults" method="post" target="_self">
            <p class="installer-mchoice">
                <label for="parser-bnr" style="width: 100%; cursor: pointer;">
                    <input type="radio" name="selected_parser" data-detail="bnr" id="parser-bnr" value="bnr"/>
                    <span>Serviciul gratuit oferit de BNR (Banca Nationala a Romaniei)</span>
                </label>
                <small class="info-detail bnr" style="display: none;">
                    <span class="icon-info-sign"></span>  Cursul oferit de <a href="http://www.bnr.ro/" target="_blank">BNR (Banca Nationala a Romaniei)</a>
                    dispune de 20 de monede si este actualizat zilnic.Moneda implicita la care se raporteaza este RON.
                </small>
            </p>

            <br class="clear"/>

            <p class="installer-mchoice">
                <label for="parser-finflow-default" style="width: 100%; cursor: pointer;">
                    <input type="radio" name="selected_parser" id="parser-finflow-default" data-detail="finflow" value="finflowoer"/>
                     <span>Serviciul gratuit openexchangerates.appspot.com</span>
                </label>
                <small class="info-detail finflow" style="display: none;">
                    <span class="icon-info-sign"></span> <a href="http://openexchangerates.appspot.com/" target="_blank">openexchangerates.appspot.com</a>
                    este un serviciu gratuit (in limita resurselor disponibile)
                    pus la dispozitia utilizatorilor de creatorii aplicatiei. Acesta dispune de 100 de monede si o fereastra de actualizare de 10 minute.
                </small>
            </p>

            <br class="clear"/>

            <p class="installer-mchoice">
                <label for="parser-openexchangerates" style="width: 100%; cursor: pointer;">
                    <input type="radio" name="selected_parser" id="parser-openexchangerates" data-detail="openexchangerates" value="oer"/>
                     <span>Serviciul OpenExchangeRates.org</span>
                </label>

                <small class="info-detail openexchangerates" style="display: none;">
                    <span class="icon-info-sign"></span>
                    Serviciul <a href="http://openexchangerates.org/" target="_blank">OpenExchangeRates.org</a> ofera cursul valutar atat gratuit
                    (limitat la USD ca moneda implicita), cat si contra cost, cu o rata de actualizare mare si 165 de monede disponibile.
                    <br/><br/><input type="text" name="oer_api_key" id="oer_api_key" placeholder="OpenExchangeRates APP ID..." value=""/>
                </small>

            </p>

            <p style="text-align: center;">
                <button class="btn" type="button" onclick="window.location.href='requirements.php';"> <span class="icon-arrow-left"></span> &#206;napoi</button>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <button class="btn btn-success" type="submit">
                    Continu&#259; <span class="icon-arrow-right"></span>
                </button>
            </p>

        </form>

    <?php else: ?>
        <p class="msg note"> Parserul a fost initializat cu succes... . </p>
    <script type="text/javascript">
        window.location.href = '<?php echo FN_URL; ?>/setup/setup-currency.php';
    </script>

    <?php endif; ?>

    <hr/>

    <script type="text/javascript">
        $(document).ready(function(){
            $('input[type=radio]').change(function(){
                $('small.info-detail').slideUp(); if($(this).is(':checked') ){ var detailSpan = $(this).data('detail'); $('small.info-detail.' + detailSpan).slideDown(); }
            });
        });
    </script>

<?php include_once 'footer.php'; ?>