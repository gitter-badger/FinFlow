<?php if ( !defined('FNPATH') ) exit; $errors = $warnings = $notices = array();

if ( isset($_GET['del']) ){
	//--- remove a transaction ---//
	$removed = ( isset($_GET['t']) and ( $_GET['t'] == 'pending' ) ) ? fn_OP_Pending::remove($_GET['del']) : fn_OP::remove($_GET['del']);
	//--- remove a transaction ---//
}

if ( isset($_POST['add']) ){

    //--- add a transaction ---//

	$value = floatval($_POST['value']);
    $date  = $_POST['date'];

    $account_id = isset($_POST['account_id']) ? intval($_POST['account_id']) : 0;
    $contact_id = isset($_POST['contact_id']) ? intval($_POST['contact_id']) : 0;

	if ( $value <= 0 )                     $errors[] = "Valoare tranzac&#355;iei lipse&#351;te.";
    if( strtotime($date) === false) $errors[] = "Data specificat&#259; este invalida";
	
	if ( !in_array($_POST['optype'], array(FN_OP_IN, FN_OP_OUT)) )
        $errors[] = "Tipul tranzac&#355;iei este invalid.";

    if( !isset($_POST['add_pending']) and (strtotime($date) > time() ) )
        $errors[] = "Data tranzactiei este in viitor. Foloseste caracteristica &quot;in asteptare&quot; pentru a adauga tranzactii in viitor.";

	if ( empty($errors) ){

        if( isset($_POST['add_pending']) ){

            //--- add a pending transaction ---//

            $trans_id = fn_OP_Pending::add($_POST['optype'], $value, $_POST['currency_id'], $_POST['recurring'], array(), $date);

            if( $trans_id ){

                $metadata = array('labels'=>array(), 'files'=>array(), 'account_id'=>0, 'comments'=>trim($_POST['comments']));

                //--- add the metadata ---//
                if ( count($_POST['labels']) ) foreach ($_POST['labels'] as $label_id) $metadata['labels'][] = $label_id;

                if( $account_id ) $metadata['account_id'] = $account_id;
                if( $contact_id ) $metadata['contact_id'] = $contact_id;

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

                //--- associate with a contact (if any selected) ---//
                if( $contact_id ) fn_Contacts::assoc_trans($contact_id, $trans_id);
                //--- associate with a contact (if any selected) ---//

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

<div class="row">

	<div class="<?php fn_UI::main_container_grid_class(); ?>">
		
		<ul class="nav nav-tabs" role="tablist">
			<li class="dropdown <?php echo av($activetab, 'list'); ?>">
				<a href="<?php fn_UI::page_url('transactions', array('t'=>'list'))?>" class="dropdown-toggle" data-toggle="dropdown">
                    Raport <span class="caret"></span>
                </a>
				<ul class="dropdown-menu" role="menu">
                	<li><a href="<?php fn_UI::page_url('transactions', array('sdate'=>$currmonthstart)); ?>">Luna aceasta</a></li>
                  	<li><a href="<?php fn_UI::page_url('transactions', array('sdate'=>fn_Util::get_relative_time(0, 3, 0, $currmonthstart)) ); ?>">Ultimele 3 luni</a></li>
                  	<li><a href="<?php fn_UI::page_url('transactions', array('sdate'=>fn_Util::get_relative_time(0, 6, 0, $currmonthstart)) ); ?>">Ultimele 6 luni</a></li>
                 	<li><a href="<?php fn_UI::page_url('transactions', array('sdate'=>fn_Util::get_relative_time(0, 0, 1, $currmonthstart)) ); ?>">Ultimul an</a></li>
                 	<li><a href="<?php fn_UI::page_url('transactions', array('sdate'=>fn_Util::get_relative_time(0, 0, 3, $currmonthstart)) ); ?>">Ultimii 3 ani</a></li>
                 	<li><a href="<?php fn_UI::page_url('transactions', array('sdate'=>fn_Util::get_relative_time(0, 0, 5, $currmonthstart)) ); ?>">Ultimii 5 ani</a></li>
                 	<li><a href="<?php fn_UI::page_url('transactions', array('sdate'=>'1970-01-01')); ?>">Toate</a></li>
                </ul>
			</li>
			<li class="<?php echo av($activetab, 'generator'); ?>"><a href="<?php fn_UI::page_url('transactions', array('t'=>'generator'))?>"> Generator raport  </a></li>
			<li class="dropdown <?php echo av($activetab, 'pending'); ?>">
                <a href="<?php fn_UI::page_url('transactions', array('t'=>'pending'))?>"  class="dropdown-toggle" data-toggle="dropdown">
                    &#206;n a&#351;teptare <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="<?php fn_UI::page_url('transactions', array('t'=>'pending', 'over'=>'overdue')); ?>">&#206;n &#238;ntarziere</a></li>
                    <li><a href="<?php fn_UI::page_url('transactions', array('t'=>'pending', 'over'=>'30 days')); ?>">&#206;n urm&#259;toarele 30 de zile</a></li>
                    <li><a href="<?php fn_UI::page_url('transactions', array('t'=>'pending', 'over'=>'3 months')); ?>">&#206;n urm&#259;toarele 3 luni</a></li>
                    <li><a href="<?php fn_UI::page_url('transactions', array('t'=>'pending', 'over'=>'6 months')); ?>">&#206;n urm&#259;toarele 6 luni</a></li>
                    <li><a href="<?php fn_UI::page_url('transactions', array('t'=>'pending', 'over'=>'12 months')); ?>">&#206;n urm&#259;toarele 12 luni</a></li>
                    <li class="divider"></li>
                    <li><a href="<?php fn_UI::page_url('transactions', array('t'=>'pending', 'forecast'=>'1')); ?>">Prognoza</a></li>
                </ul>
            </li>
			<li class="<?php echo av($activetab, 'add'); ?>">
                <a href="<?php fn_UI::page_url('transactions', array('t'=>'add'))?>"> Adaug&#259; </a>
            </li>
		</ul>


		<?php if ( $tab == 'list' ) include_once ( 'transactions-list.php' ); ?>

		<?php if ( $tab == 'pending' ) include_once ( 'pending.php' ); ?>

		<?php if ( $tab == 'generator' ) include_once ( 'transactions-filter.php' ); ?>
		
		<?php if ( $tab == 'add' ) include_once ( 'transactions-add.php' ); ?>
		
	</div>
	
	<?php include_once ( FNPATH . '/snippets/sidebar.php' ); ?>
	
</div>