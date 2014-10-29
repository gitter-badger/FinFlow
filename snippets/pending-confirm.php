<?php include_once '../inc/init.php'; if ( !fn_User::is_authenticated() ) exit(); $errors = $notices = $warnings = array();

$trans_id = intval($_GET['id']); $base_url = ( FN_URL . '/snippets/pending-confirm.php?id=' . $trans_id );

$Transaction = fn_OP_Pending::get($trans_id);

$notices = $errors =array(); $labels = null; $account = null; $details = null; $attachments = array();

if( isset($_POST['action']) ){

    $instances = intval($_POST['instances']);

    $confirm   = ( $_POST['action'] == 'confirm' ? true : false );
    $clear       = ( $_POST['action'] == 'clean' ? true : false );


    if( $confirm and fn_OP_Pending::confirm($Transaction, $instances) ) {
        $notices[] = "Tranzactia a fost confirmat&#259;.";
    }

    if($clear and fn_OP_Pending::clear($Transaction, $instances))
        $notices[] = "Tranzactia a fost curatat&#259;.";

}

if( count($Transaction) and isset($Transaction->trans_id) ){

    $labels		  = fn_OP_Pending::get_labels( $Transaction );
    $account     =  fn_OP_Pending::get_account( $Transaction );
    $currency	  = fn_Currency::get( $Transaction->currency_id );

    $details		   = fn_OP_Pending::get_metdata($Transaction, 'details');
    $attachments= fn_OP_Pending::get_metdata($Transaction, 'attachments');

    if( ( is_array($attachments) and count($attachments) ) or strlen($attachments) ){

        $attachmentsNames = fn_OP_Pending::get_metdata($Transaction, 'attachments_names', $attachments);

        $attachments           = is_array($attachments) ? $attachments : @unserialize($attachments);
        $attachmentsNames = is_array($attachmentsNames) ? $attachmentsNames : @unserialize($attachmentsNames);

        $a=0;
    }

}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>FinFlow | Confirma tranzac&#355;ie</title>
<link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('/styles/bootstrap.min.css');; ?>" />
<link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('styles/style.css'); ?>" />
<link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('styles/font-awesome.min.css'); ?>" />
<link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('styles/bootstrap-slider.min.css'); ?>" />
</head>
<body id="page-transaction-details" class="transaction-details" role="document">

<?php if ( count($Transaction) ): ?>
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand">Tranzactie #<?php echo $Transaction->trans_id;?></a>
            </div>
            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="#"><i class="fa fa-chevron-left"></i></a></li>
                    <li><a href="#"><i class="fa fa-chevron-right"></i></a></li>
                    <li><a href="#" onclick="window.print();"><i class="fa fa-print"></i></a></li>
                    <li><a href="#">&nbsp;</a></li>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="container-fluid">

    <div class="row">
        <div class="col-lg-12">

            <?php fn_UI::show_errors($errors); fn_UI::show_notes($notices); fn_UI::show_warnings($warnings); ?>

            <?php if ( count($Transaction) ): ?>

                <h4 class="visible-print">Tranzactie #<?php echo $Transaction->trans_id;?></h4>

                <div class="panel panel-default">

                    <div class="panel-heading">Detalii</div>

                    <table class="table table-striped list details">
                        <tr>
                            <td>Data:</td>
                            <td>
                                <?php echo fn_UI::translate_date( date(FN_DATETIME_FORMAT, strtotime($Transaction->fdate)) ); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Valoare:</td>
                            <td><?php echo $currency->ccode; ?> <?php echo fn_Util::format_nr( $Transaction->value, 4 ); ?></td>
                        </tr>

                        <tr>
                            <td>Se repet&#259;:</td>
                            <td><?php echo $Transaction->recurring; ?></td>
                        </tr>

                        <?php if (count($labels) ): ?>
                        <tr>
                            <td> Etichete: </td>
                            <td>
                                <?php $dlabels=array(); foreach ($labels as $label) $dlabels[] = $label->title; echo @implode(", ", $dlabels); ?>
                            </td>
                        </tr>
                        <?php endif; ?>

                        <?php if ( $account ): ?>
                        <tr>
                            <td> Cont: </td>
                            <td>
                                <?php echo fn_UI::esc_html( $account->holder_name ); ?>
                            </td>
                        </tr>
                        <?php endif; ?>

                        <?php $comments = fn_OP_Pending::get_metdata($Transaction, 'comments'); if ( !empty( $comments ) ): ?>
                        <tr>
                            <td>Comentarii: </td>
                            <td><?php echo fn_UI::esc_html( $comments ); ?></td>
                        </tr>
                        <?php endif;?>

                        <?php if( is_array($attachments) and count($attachments) ): ?>
                        <tr>
                            <td>Fi&#351;iere ata&#351;ate:</td>
                            <td>
                                <ul style="list-style-type: square;">
                                    <?php foreach($attachments as $attachment): $apath = fn_OP::get_attachment_path($attachment); $aname = $attachmentsNames[$a]; ?>
                                   <li>
                                       <a href="<?php echo fn_UI::get_file_preview_url($apath, array('name'=>$aname)); ?>" target="_self" title="previzualizare">
                                           <?php echo $aname; ?>
                                       </a>
                                       &nbsp;&nbsp;&nbsp;
                                       <small>
                                           <a href="<?php echo fn_UI::get_file_download_url($apath, false, $aname); ?>"><span class="icon-download"></span> descarc&#259;</a>
                                       </small>
                                   </li>
                                    <?php $a++; endforeach; ?>
                                </ul>
                            </td>
                        </tr>
                        <?php endif;?>

                        <?php if ( !empty($details) ): ?>
                        <tr>
                            <td colspan="2" class="textonly-td"><?php echo fn_UI::esc_html( nl2br( strip_tags($details) ) ); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Confirmare</div>
                        <table class="table list">
                            <tr>
                                <td>Instan&#355;e:</td>
                                <td>
                                    <table>
                                        <tr>
                                            <td style="padding-right: 15px;">
                                                <a href="#"><i class="fa fa-minus-circle"></i></a>
                                            </td>
                                            <td style="width: 100%">
                                                <input class="slider" style="width: 100%;" type="range" name="instance_count" id="instance_count" data-slider-min="1" data-slider-max="100" data-slider-step="1" data-slider-value="1"/>
                                            </td>
                                            <td style="padding: 0px 15px;">
                                                <a href="#"><i class="fa fa-plus-circle"></i></a>
                                            </td>
                                            <td class="align-center" style="padding-left: 20px;">
                                                <div class="label label-default" style="font-size: 1em; font-weight: normal;">
                                                    <span id="instancesCurrentValue">1</span> x <?php echo $currency->ccode; ?> <?php echo fn_Util::format_nr( $Transaction->value, 4 ); ?>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td>Total:</td>
                                <td>
                                    <em><?php echo $currency->ccode; ?> <span id="instancesTotal"><?php echo fn_Util::format_nr( $Transaction->value, 4 ); ?></span></em>
                                </td>
                            </tr>

                    </table>
                </div>

             </div>

            <div class="clearfix"></div>

            <div class="col-lg-12">
                <form class="form form-horizontal" name="transactionConfirm" id="transactionConfirm" method="post" action="<?php echo $base_url; ?>" target="_self">

                    <div class="form-group">
                        <div class="col-lg-12 align-center form-submit">
                            <button class="btn btn-primary" type="button" onclick="send_transaction('confirm');">Confirm&#259;</button>
                            <button class="btn" type="button"  onclick="send_transaction('clean');">Cur&#259;&#355;&#259;</button>
                        </div>
                    </div>

                    <input type="hidden" name="action" id="selectedAction" value="confirm"/>
                    <input type="hidden" name="instances" id="selectedInstances" value="1"/>

                </form>
            </div>

            <?php else: ?>
                <div class="col-lg-12">
                    <div class="alert alert-warning">Tranzactia nu exist&#259; sau a fost &#351;tears&#259;.</div>
                    <p>
                        <a class="btn btn-default" onclick="window.history.back();return false;"><i class="fa fa-arrow-left"></i> inapoi</a>
                    </p>
                </div>
            <?php endif; ?>

    </div>

</div>

<script type="text/javascript" src="<?php fn_UI::asset_url('js/jquery.min.js'); ?>"></script>
<script type="text/javascript" src="<?php fn_UI::asset_url('js/bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php fn_UI::asset_url('js/bootstrap-slider.min.js'); ?>"></script>
<script type="text/javascript" src="<?php fn_UI::asset_url('js/fn.js'); ?>"></script>

<script type="text/javascript">

    function send_transaction(action){

        $('#selectedAction').val(action); $('#selectedInstances').val( parseInt($('#instance_count').val()) );

        if( ( action == 'clean' ) ) {
            if( confirm("Esti sigur?") ) return $('#transactionConfirm').submit();
        }

        if( action == 'confirm' ) return $('#transactionConfirm').submit();

    }

    $(function(){
        var mySlider = $("input.slider").slider(); var currentValue = $('#instancesCurrentValue'); $("input.slider").on("slide", function(event) {
            var value = parseInt(event.value); currentValue.html(value); var total = parseFloat('<?php echo $Transaction->value; ?>') * value; $('#instancesTotal').text( fn_round_nr(total, 4) );
        });
    });
</script>

</body>
</html>