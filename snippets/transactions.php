<?php if ( !defined('FNPATH') ) exit;

if ( isset($_GET['del']) ){
	//--- remove a transaction ---//
	fn_OP::remove($_GET['del']);
	//--- remove a transaction ---//
}

if ( isset($_GET['pdel']) ){
    //--- remove a pending transaction ---//
    fn_OP_Pending::remove($_GET['del']);
    //--- remove a pending transaction ---//
}

if ( isset($_POST['add']) ){

    //--- add a transaction ---//
	
	$errors 		= array();
	$notices  	= array();
    $warnings  = array();
	
	$value = floatval($_POST['value']);
    $date  = $_POST['date'];

    $account_id = isset($_POST['account_id']) ? intval($_POST['account_id']) : 0;
	
	if ( $value <= 0 )                     $errors[] = "Valoare tranzac&#355;iei lipse&#351;te.";
    if( strtotime($date) === false) $errors[] = "Data specificat&#259; este invalida";
	
	if ( !in_array($_POST['optype'], array(FN_OP_IN, FN_OP_OUT)) ) $errors[] = "Tipul tranzac&#355;iei este invalid.";

	if ( empty($errors) ){

        if( isset($_POST['add_pending']) ){

            //--- add a pending transaction ---//

            $trans_id = fn_OP_Pending::add($_POST['optype'], $value, $_POST['currency_id'], $_POST['recurring'], array(), $date);

            if( $trans_id ){

                $metadata = array('labels'=>array(), 'files'=>array(), 'account_id'=>0);

                //--- add the metadata ---//
                if ( count($_POST['labels']) ) foreach ($_POST['labels'] as $label_id) $metadata['labels'][] = $label_id;

                if( $account_id ) $metadata['account_id'] = $account_id;

                //--- upload files if any ---//
                if( count( $_FILES ) ) {

                    if( strlen($_FILES['attachment_1']['name']) ){

                        $attached = fn_OP_Pending::add_file($trans_id, $_FILES['attachment_1']);

                        if( strpos($attached, '@') === false )
                            $errors[] = $attached;
                        else {
                            $filename = $_FILES['attachment_1']['name']; $metadata['files'][$filename] = trim($attached, '@');
                        }

                    }

                    if( strlen($_FILES['attachment_2']['name']) ){

                        $attached = fn_OP_Pending::add_file($trans_id, $_FILES['attachment_2']);

                        if( strpos($attached, '@') === false )
                            $errors[] = $attached;
                        else{
                            $filename = $_FILES['attachment_2']['name']; $metadata['files'][$filename] = trim($attached, '@');
                        }
                    }

                    if( strlen($_FILES['attachment_3']['name']) ){

                        $attached = fn_OP_Pending::add_file($trans_id, $_FILES['attachment_3']);

                        if( strpos($attached, '@') === false )
                            $errors[] = $attached;
                        else {
                            $filename = $_FILES['attachment_3']['name']; $metadata['files'][$filename] = trim($attached, '@');
                        }
                    }

                }
                //--- upload files if any ---//

                $metadata = @serialize($metadata); fn_OP_Pending::update($trans_id, array('metadata'=>$metadata));

                if( $_POST['recurring'] != 'no' )
                    $children = fn_OP_Pending::add_children($trans_id); //Add a children as instance of the recurring transaction
                else
                    $children = $trans_id;

                //--- add the metadata ---//

                //--- create first child transaction ---//
                if( $children )
                    $notices[] = "Tranzac&#355;ia a fost adaugat&#259;.";
                else
                    $errors[] =  "Eroare SQL: {$fndb->error} .";
                //--- create first child transaction ---//

            }
            else $errors[] = "Eroare SQL: {$fndb->error} .";

            //--- add a pending transaction ---//

        }
        else{

            //--- add a normal transaction ---//

            $trans_id = fn_OP::add($_POST['optype'], $value, $_POST['currency_id'], $_POST['comments'], $date);

            if ( $trans_id ){

                //--- associate to an account (if any selected) ---//
                if( $account_id ) fn_Accounts::add_trans($account_id, $trans_id);
                //--- associate to an account (if any selected) ---//

                if ( count($_POST['labels']) ) foreach ($_POST['labels'] as $label_id){
                    fn_OP::associate_label($trans_id, $label_id);
                }
                else $warnings[] = "Nu s-a asociat nici o etichet&#259; pentru aceast&#259; tranzac&#355;ie.";

                //--- upload files if any ---//
                if( count( $_FILES ) ) {

                    if( strlen($_FILES['attachment_1']['name']) ){
                        $attached = fn_OP::add_attachment($trans_id, $_FILES['attachment_1']); if($attached != fn_OP::$attached ) $errors[] = $attached;
                    }

                    if( strlen($_FILES['attachment_2']['name']) ){
                        $attached = fn_OP::add_attachment($trans_id, $_FILES['attachment_2']); if($attached != fn_OP::$attached ) $errors[] = $attached;
                    }

                    if( strlen($_FILES['attachment_3']['name']) ){
                        $attached = fn_OP::add_attachment($trans_id, $_FILES['attachment_3']); if($attached != fn_OP::$attached ) $errors[] = $attached;
                    }

                }
                //--- upload files if any ---//

                $notices[] = "Tranzac&#355;ia a fost adaugat&#259;.";

            }
            else
                $errors[] = "Eroare SQL: {$fndb->error} .";

            //--- add a normal transaction ---//
        }


	}

    //--- add a transaction ---//
}

include_once ( FNPATH . '/inc/transfilter-vars.php');

global $filters, $start, $count, $pagevars;

$tab = isset($_GET['t']) ? urldecode($_GET['t']) : 'list'; $activetab = array(); $activetab[$tab] = 'active';

if (  $tab == 'list' ){

	$Total 		= fn_OP::get_sum($filters);
	$Income		= fn_OP::get_sum(array_merge($filters, array('type'=>FN_OP_IN)));
	$Outcome	= fn_OP::get_sum(array_merge($filters, array('type'=>FN_OP_OUT)));
	
	$Transactions = fn_OP::get_operations($filters, $start, $count);

}

//--- add the current report period to the Report menu label ---//
if( empty($filters['enddate']) or ( strtotime($filters['enddate']) > time() ) )
    $report_period = fn_Util::nicetime($filters['startdate'], " pe ", array('m'=>'ultimul', 'f'=>'ultima'), array('m'=>'ultimii', 'f'=>'ultimele'));
else
    $report_period = "";
//--- add the current report period to the Report menu label ---//

?>

<div class="row content">
	<div class="span10">
		
		<ul class="nav nav-tabs">
			<li class="dropdown <?php echo $activetab['list']; ?>">
				<a href="<?php fn_UI::page_url('transactions', array('t'=>'list'))?>" class="dropdown-toggle" data-toggle="dropdown">
                    Raport <?php echo $report_period; ?> <b class="caret"></b>
                </a>
				<ul class="dropdown-menu">
                	<li><a href="<?php fn_UI::page_url('transactions', array('sdate'=>$currmonthstart)); ?>">Luna aceasta</a></li>
                  	<li><a href="<?php fn_UI::page_url('transactions', array('sdate'=>fn_Util::get_relative_time(0, 3, 0, $currmonthstart)) ); ?>">Ultimele 3 luni</a></li>
                  	<li><a href="<?php fn_UI::page_url('transactions', array('sdate'=>fn_Util::get_relative_time(0, 6, 0, $currmonthstart)) ); ?>">Ultimele 6 luni</a></li>
                 	<li><a href="<?php fn_UI::page_url('transactions', array('sdate'=>fn_Util::get_relative_time(0, 0, 1, $currmonthstart)) ); ?>">Ultimul an</a></li>
                 	<li><a href="<?php fn_UI::page_url('transactions', array('sdate'=>fn_Util::get_relative_time(0, 0, 3, $currmonthstart)) ); ?>">Ultimii 3 ani</a></li>
                 	<li><a href="<?php fn_UI::page_url('transactions', array('sdate'=>fn_Util::get_relative_time(0, 0, 5, $currmonthstart)) ); ?>">Ultimii 5 ani</a></li>
                 	<li><a href="<?php fn_UI::page_url('transactions', array('sdate'=>'1970-01-01')); ?>">Toate</a></li>
                </ul>
			</li>
			<li class="<?php echo $activetab['generator']; ?>"><a href="<?php fn_UI::page_url('transactions', array('t'=>'generator'))?>"> Generator raport  </a></li>
			<li class="dropdown <?php echo $activetab['pending']; ?>">
                <a href="<?php fn_UI::page_url('transactions', array('t'=>'pending'))?>"  class="dropdown-toggle" data-toggle="dropdown">
                    &#206;n a&#351;teptare <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="<?php fn_UI::page_url('transactions', array('t'=>'pending')); ?>">Toate</a></li>
                    <li><a href="<?php fn_UI::page_url('transactions', array('t'=>'pending', 'over'=>'overdue')); ?>">Overdue</a></li>
                    <li><a href="<?php fn_UI::page_url('transactions', array('t'=>'pending', 'over'=>'30 days')); ?>">In 30 days</a></li>
                    <li><a href="<?php fn_UI::page_url('transactions', array('t'=>'pending', 'over'=>'3 months')); ?>">In 3 months</a></li>
                    <li><a href="<?php fn_UI::page_url('transactions', array('t'=>'pending', 'over'=>'6 months')); ?>">In 6 months</a></li>
                    <li><a href="<?php fn_UI::page_url('transactions', array('t'=>'pending', 'over'=>'12 months')); ?>">In 12 months</a></li>
                    <li class="divider"></li>
                    <li><a href="<?php fn_UI::page_url('transactions', array('t'=>'pending', 'viewroots'=>'1')); ?>">Modifica</a></li>
                </ul>
            </li>
			<li class="<?php echo $activetab['add']; ?>"><a href="<?php fn_UI::page_url('transactions', array('t'=>'add'))?>"> Adaug&#259; </a></li>
		</ul>
		
		<?php if ( $tab == 'list' ) include_once ( FNPATH . '/snippets/transactions-list.php' ); ?>

		<?php if ( $tab == 'pending' ) include_once ( FNPATH . '/snippets/pending.php' ); ?>

		<?php if ( $tab == 'generator' ) include_once ( FNPATH . '/snippets/transactions-filter.php' ); ?>
		
		<?php if ( $tab == 'add' ) include_once ( FNPATH . '/snippets/transactions-add.php' ); ?>
		
	</div>
	
	<?php include_once ( FNPATH . '/snippets/sidebar.php' ); ?>
	
</div>