<?php include_once '../inc/init.php';

if ( !fn_User::is_authenticated() ) exit();

$trans_id = intval($_GET['id']); $base_url = ( FN_URL . '/snippets/pending-confirm.php?id=' . $trans_id );

$Transaction = fn_OP_Pending::get($trans_id); $notices = array(); $errors = array();

if( isset($_POST['action']) ){

    $instances = intval($_POST['instances']);

    $confirm   = ( $_POST['action'] == 'confirm' ? true : false );
    $clear       = ( $_POST['action'] == 'clean' ? true : false );


    if( $confirm and fn_OP_Pending::confirm($Transaction, $instances) ) {
        $notices[] = "Tranzactia a fost confirmata.";
    }

    if($clear and fn_OP_Pending::clear($Transaction, $instances))
        $notices[] = "Tranzactia a fost curatata.";

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
<link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/bootstrap-responsive.min.css" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo FN_URL; ?>/styles/style.css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo FN_URL; ?>/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo FN_URL; ?>/js/fn.js"></script>
</head>
<body style="background: #FFF;">

    <?php fn_UI::show_errors($errors); fn_UI::show_notes($notices); fn_UI::show_warnings($warnings); ?>

	<?php if ( count($Transaction) ): ?>
        <table class="list details" border="1">
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
                <td colspan="2"><pre><?php echo fn_UI::esc_html( $details ); ?></pre></td>
            </tr>
            <?php endif; ?>

            <tr style="border-top-width: 2px;">
                <td>Instan&#355;e:</td>
                <td>
                    <input type="range" name="instance_count" id="instance_count" min="1" max="120" step="1" style="width: 100%;" value="1" list="instanceSet"/>
                    ( <span id="instancesCurrentValue">1</span> x <?php echo $currency->ccode; ?> <?php echo fn_Util::format_nr( $Transaction->value, 4 ); ?> )

                    <datalist id="instanceSet">
                        <option>1</option>
                        <option>3</option>
                        <option>6</option>
                        <option>9</option>
                        <option>12</option>
                        <option>24</option>
                        <option>48</option>
                        <option>72</option>
                        <option>96</option>
                        <option>120</option>
                    </datalist>

                </td>
            </tr>

            <tr>
                <td>Total:</td>
                <td>
                    <em><?php echo $currency->ccode; ?> <span id="instancesTotal"><?php echo fn_Util::format_nr( $Transaction->value, 4 ); ?></span></em>
                </td>
            </tr>

        </table>

        <br class="clear"/>

        <form name="transactionConfirm" id="transactionConfirm" method="post" action="<?php echo $base_url; ?>" target="_self">
            <p class="popup-btn-group">
                <button class="btn btn-primary" type="button" onclick="send_transaction('confirm');">Confirm&#259;</button>
                <button class="btn" type="button"  onclick="send_transaction('clean');">Cur&#259;&#355;&#259;</button>
            </p>

            <input type="hidden" name="action" id="selectedAction" value="confirm"/>
            <input type="hidden" name="instances" id="selectedInstances" value="1"/>
        </form>

	<?php else: ?>
	    <p class="msg warn">Tranzactia nu exist&#259; sau a fost &#351;tears&#259;.</p>
	<?php endif; ?>

<script type="text/javascript">

    function send_transaction(action){

        $('#selectedAction').val(action); $('#selectedInstances').val( parseInt($('#instance_count').val()) );

        if( ( action == 'clean' ) ) {
            if( confirm("Esti sigur?") ) return $('#transactionConfirm').submit();
        }

        if( action == 'confirm' ) return $('#transactionConfirm').submit();

    }

    $(function(){

        var currentValue = $('#instancesCurrentValue');

        $('#instance_count').change(function(){
            var value = parseInt(this.value); currentValue.html(value); var total = parseFloat('<?php echo $Transaction->value; ?>') * value; $('#instancesTotal').text( fn_round_nr(total, 4) );
        });

        // Trigger the event on load, so
        // the value field is populated:

        $('#instance_count').change();



    });
</script>
</body>
</html>