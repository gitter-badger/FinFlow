<?php
/**
 * Renders the transactions template
 */

if ( !defined('FNPATH') ) exit;

use FinFlow\UI;
use FinFlow\OP;
use FinFlow\OP_Pending;
use FinFlow\Contacts;
use FinFlow\Accounts;
use FinFlow\Util;

$_section = ( $s = url_part(2) ) ? $s : 'list';
$errors   = $warnings = $notices = array();

if ( isset($_GET['del']) ){
	//--- remove a transaction ---//
	$removed = ( isset($_GET['t']) and ( $_GET['t'] == 'pending' ) ) ? OP_Pending::remove($_GET['del']) : OP::remove($_GET['del']);
	//--- remove a transaction ---//
}

if ( isset($_POST['add']) ){

    //--- add a transaction ---//

	$value = floatval($_POST['value']);
    $date  = $_POST['date'];

    $account_id = isset($_POST['account_id']) ? intval($_POST['account_id']) : 0;
    $contact_id = isset($_POST['contact_id']) ? intval($_POST['contact_id']) : 0;

	if ( $value <= 0 )
		$errors[] = "Valoare tranzac&#355;iei lipse&#351;te.";

    if( strtotime($date) === false)
	    $errors[] = "Data specificat&#259; este invalida";
	
	if ( !in_array($_POST['optype'], array(FN_OP_IN, FN_OP_OUT)) )
        $errors[] = "Tipul tranzac&#355;iei este invalid.";

    if( !isset($_POST['add_pending']) and (strtotime($date) > time() ) )
        $errors[] = "Data tranzactiei este in viitor. Foloseste caracteristica &quot;in asteptare&quot; pentru a adauga tranzactii in viitor.";

	if ( empty($errors) ){

        if( isset($_POST['add_pending']) ){

            //--- add a pending transaction ---//

            $trans_id = OP_Pending::add($_POST['optype'], $value, $_POST['currency_id'], $_POST['recurring'], array(), $date);

            if( $trans_id ){

                $metadata = array('labels'=>array(), 'files'=>array(), 'account_id'=>0, 'comments'=>trim($_POST['comments']));

                //--- add the metadata ---//
                if ( count($_POST['labels']) ) foreach ($_POST['labels'] as $label_id) $metadata['labels'][] = $label_id;

                if( $account_id ) $metadata['account_id'] = $account_id;
                if( $contact_id ) $metadata['contact_id'] = $contact_id;

                //--- upload files if any ---//
                if( count( $_FILES ) ) {

                    if( strlen($_FILES['attachment_1']['name']) ){

                        $attached = OP_Pending::add_file($trans_id, $_FILES['attachment_1']);

                        if( strpos($attached, '@') === false )
                            $errors[] = $attached;
                        else {
                            $filename = $_FILES['attachment_1']['name']; $metadata['files'][$filename] = trim($attached, '@');
                        }

                    }

                    if( strlen($_FILES['attachment_2']['name']) ){

                        $attached = OP_Pending::add_file($trans_id, $_FILES['attachment_2']);

                        if( strpos($attached, '@') === false )
                            $errors[] = $attached;
                        else{
                            $filename = $_FILES['attachment_2']['name']; $metadata['files'][$filename] = trim($attached, '@');
                        }
                    }

                    if( strlen($_FILES['attachment_3']['name']) ){

                        $attached = OP_Pending::add_file($trans_id, $_FILES['attachment_3']);

                        if( strpos($attached, '@') === false )
                            $errors[] = $attached;
                        else {
                            $filename = $_FILES['attachment_3']['name']; $metadata['files'][$filename] = trim($attached, '@');
                        }
                    }

                }
                //--- upload files if any ---//

                $metadata = @serialize($metadata); OP_Pending::update($trans_id, array('metadata'=>$metadata));

                if( $_POST['recurring'] != 'no' )
                    $children = OP_Pending::add_children($trans_id); //Add a children as instance of the recurring transaction
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

            $trans_id = OP::add($_POST['optype'], $value, $_POST['currency_id'], $_POST['comments'], $date);

            if ( $trans_id ){

                //--- associate to an account (if any selected) ---//
                if( $account_id ) Accounts::add_trans($account_id, $trans_id);
                //--- associate to an account (if any selected) ---//

                if ( count($_POST['labels']) ) foreach ($_POST['labels'] as $label_id){
                    OP::associate_label($trans_id, $label_id);
                }
                else $warnings[] = "Nu s-a asociat nici o etichet&#259; pentru aceast&#259; tranzac&#355;ie.";

                //--- upload files if any ---//
                if( count( $_FILES ) ) {

                    if( strlen($_FILES['attachment_1']['name']) ){
                        $attached = OP::add_attachment($trans_id, $_FILES['attachment_1']); if($attached != OP::$attached ) $errors[] = $attached;
                    }

                    if( strlen($_FILES['attachment_2']['name']) ){
                        $attached = OP::add_attachment($trans_id, $_FILES['attachment_2']); if($attached != OP::$attached ) $errors[] = $attached;
                    }

                    if( strlen($_FILES['attachment_3']['name']) ){
                        $attached = OP::add_attachment($trans_id, $_FILES['attachment_3']); if($attached != OP::$attached ) $errors[] = $attached;
                    }

                }
                //--- upload files if any ---//

                //--- associate with a contact (if any selected) ---//
                if( $contact_id ) Contacts::assoc_trans($contact_id, $trans_id);
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

//prepare transaction filter variables
include_once ( FNPATH . '/system/library/transfilter-vars.php');

//global $filters, $start, $count, $pagevars;

$activetab            = array();
$activetab[$_section] = 'active';

//--- add the current report period to the Report menu label ---//
if( empty($filters['enddate']) or ( strtotime($filters['enddate']) > time() ) )
    $report_period = Util::nicetime($filters['startdate'], " pe ", array('m'=>'ultimul', 'f'=>'ultima'), array('m'=>'ultimii', 'f'=>'ultimele'));
else
    $report_period = '';
//--- add the current report period to the Report menu label ---//

?>

<div class="row">

	<div class="col-lg-8 col-md-8">

		<ol class="breadcrumb">
			<li><a href="<?php UI::url('/dashboard'); ?>"><i class="fa fa-home"></i></a></li>
			<li><a href="<?php UI::url('/transactions'); ?>">Transactions</a></li>
			<li class="active">Add transaction</li>
		</ol>
		
		<ul class="nav nav-justified nav-pills nav-page-menu" role="tablist">
			<li class="dropdown <?php echo av($activetab, 'list'); ?>">

				<a href="<?php UI::url('transactions', array('t'=>'list'))?>" class="dropdown-toggle" data-toggle="dropdown">
                    Raport <span class="caret"></span>
                </a>

				<ul class="dropdown-menu" role="menu">
                	<li>
		                <a href="<?php UI::url('transactions', array('sdate'=>$currmonthstart)); ?>">Luna aceasta</a>
	                </li>
                  	<li>
	                    <a href="<?php UI::url('transactions', array('sdate'=>Util::get_relative_time(0, 3, 0, $currmonthstart)) ); ?>">Ultimele 3 luni</a>
                    </li>
                  	<li>
	                    <a href="<?php UI::url('transactions', array('sdate'=>Util::get_relative_time(0, 6, 0, $currmonthstart)) ); ?>">Ultimele 6 luni</a>
                    </li>
                 	<li>
	                    <a href="<?php UI::url('transactions', array('sdate'=>Util::get_relative_time(0, 0, 1, $currmonthstart)) ); ?>">Ultimul an</a>
                    </li>
                 	<li>
	                    <a href="<?php UI::url('transactions', array('sdate'=>Util::get_relative_time(0, 0, 3, $currmonthstart)) ); ?>">Ultimii 3 ani</a>
                    </li>
                 	<li>
	                    <a href="<?php UI::url('transactions', array('sdate'=>Util::get_relative_time(0, 0, 5, $currmonthstart)) ); ?>">Ultimii 5 ani</a>
                    </li>
                 	<li>
	                    <a href="<?php UI::url('transactions', array('sdate'=>'1970-01-01')); ?>">Toate</a>
                    </li>
					<li class="divider"></li>
					<li>
						<a href="<?php UI::url('transactions/calendar'); ?>">Calendar</a>
					</li>
                </ul>
			</li>

			<li class="separator"></li>

			<li class="<?php echo av($activetab, 'generator'); ?>">

				<a href="<?php UI::url('transactions/report')?>"> Generator raport  </a>

			</li>

			<li class="separator"></li>

			<li class="dropdown <?php echo av($activetab, 'pending'); ?>">
                <a href="<?php UI::url('transactions/pending')?>"  class="dropdown-toggle" data-toggle="dropdown">
                    &#206;n a&#351;teptare <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" role="menu">
                    <li>
	                    <a href="<?php UI::url('transactions/pending', array('over'=>'overdue')); ?>">&#206;n &#238;ntarziere</a>
                    </li>
                    <li>
	                    <a href="<?php UI::url('transactions/pending', array('over'=>'30 days')); ?>">&#206;n urm&#259;toarele 30 de zile</a>
                    </li>
                    <li>
	                    <a href="<?php UI::url('transactions/pending', array('over'=>'3 months')); ?>">&#206;n urm&#259;toarele 3 luni</a>
                    </li>
                    <li>
	                    <a href="<?php UI::url('transactions/pending', array('over'=>'6 months')); ?>">&#206;n urm&#259;toarele 6 luni</a>
                    </li>
                    <li>
	                    <a href="<?php UI::url('transactions/pending', array('over'=>'12 months')); ?>">&#206;n urm&#259;toarele 12 luni</a>
                    </li>
                    <li class="divider"></li>
                    <li>
	                    <a href="<?php UI::url('transactions/pending', array('forecast'=>'1')); ?>">Prognoza</a>
                    </li>
                </ul>
            </li>

			<li class="separator"></li>

			<li class="<?php echo av($activetab, 'add'); ?>">
                <a href="<?php UI::url('transactions/add')?>"> Adaug&#259; </a>
            </li>
		</ul>


		<?php

			if ( $_section == 'list' )
				include_once( 'transactions-list.php' );

		?>

		<?php

			if ( $_section == 'pending' )
				//include_once( 'pending.php' );

		?>

		<?php

			if ( $_section == 'generator' )
				//include_once( 'transactions-filter.php' );

		?>
		
		<?php

			if ( $_section == 'add' )
				include_once( 'transactions-add.php' );

		?>
		
	</div>
	
	<?php UI::component('main/sidebar'); ?>
	
</div>