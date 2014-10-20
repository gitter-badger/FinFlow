<?php include_once '../inc/init.php';

if ( !fn_User::is_authenticated() ) exit();

$trans_id = intval($_GET['id']); $is_pending = false;

if( $_GET['t'] == 'pending' ) {
    $Transaction = fn_OP_Pending::get($trans_id); $is_pending= true;
}else
    $Transaction = fn_OP::get($trans_id);

if( count($Transaction) and isset($Transaction->trans_id) ){

    $labels		 = $is_pending ? fn_OP_Pending::get_labels( $Transaction ) : fn_OP::get_labels($trans_id);
    $account     = $is_pending ? fn_OP_Pending::get_account( $Transaction ) : fn_OP::get_account($trans_id);
    $currency	  = fn_Currency::get( $Transaction->currency_id );

    $details		   = $is_pending ? fn_OP_Pending::get_metdata($Transaction, 'details') : fn_OP::get_metadata($trans_id, 'details');
    $comments   = $is_pending ? fn_OP_Pending::get_metdata($Transaction, 'comments') : $Transaction->comments;
    $attachments= $is_pending ?  fn_OP_Pending::get_metdata($Transaction, 'attachments') : fn_OP::get_metadata($trans_id, 'attachments');

    if( ( is_array($attachments) and count($attachments) ) or strlen($attachments) ){

        $attachmentsNames = $is_pending ? fn_OP_Pending::get_metdata($Transaction, 'attachments_names', $attachments) : fn_OP::get_metadata($trans_id, 'attachments_names', $attachments);

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
<title>FinFlow | Detalii tranzac&#355;ie #<?php echo $Transaction->trans_id; ?></title>
<link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('/styles/bootstrap.min.css');; ?>" />
<link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('styles/style.css'); ?>" />
<link rel="stylesheet" type="text/css" media="all" href="<?php fn_UI::asset_url('styles/font-awesome.min.css'); ?>" />
</head>
<body id="page-transaction-details" class="transaction-details" role="document">

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

<div class="container-fluid">

   <div class="row">
       <div class="col-lg-12">


           <?php if (count($Transaction)): ?>

               <h4 class="visible-print">Tranzactie #<?php echo $Transaction->trans_id;?></h4>

               <div class="panel panel-default">

                   <table class="table table-striped list details">

                       <tr>
                           <td>Data:</td>
                           <td>
                               <?php echo fn_UI::translate_date( date(FN_DATETIME_FORMAT, strtotime($Transaction->sdate)) ); ?>
                           </td>
                       </tr>
                       <tr>
                           <td>Valoare:</td>
                           <td><?php echo $currency->ccode; ?> <?php echo fn_Util::format_nr( $Transaction->value, 4 ); ?></td>
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

                       <?php if ( !empty( $comments ) ): ?>
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

                   </table>
               </div>

               <?php if ( !empty($details) ): ?>
                   <div class="panel panel-default">
                       <div class="panel-body">
                           <?php echo fn_UI::esc_html( nl2br( strip_tags($details) ) ); ?>
                       </div>
                   </div>
               <?php endif; ?>

           <?php else: ?>
               <div class="alert alert-warning">Tranzactia nu exist&#259; sau a fost &#351;tears&#259;.</div>
           <?php endif; ?>

       </div>
   </div>
</div>

<script type="text/javascript" src="<?php fn_UI::asset_url('js/jquery.min.js'); ?>"></script>
<script type="text/javascript" src="<?php fn_UI::asset_url('js/bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php fn_UI::asset_url('js/fn.js'); ?>"></script>

</body>
</html>