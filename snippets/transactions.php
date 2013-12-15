<?php if ( !defined('FNPATH') ) exit;

if ( isset($_GET['del']) ){
	//--- remove a transaction ---//
	fn_OP::remove($_GET['del']);
	//--- remove a transaction ---//
}

if ( isset($_POST['add']) ){
	
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
	}
	
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
			<!--- <li class="<?php echo $activetab['waiting']; ?>"><a href="<?php fn_UI::page_url('transactions', array('t'=>'waiting'))?>"> &#206;n a&#351;teptare </a></li> --->
			<li class="<?php echo $activetab['add']; ?>"><a href="<?php fn_UI::page_url('transactions', array('t'=>'add'))?>"> Adaug&#259; </a></li>
		</ul>
		
		<?php if ( $tab == 'list' ): ?>
		
			<?php if ( count($Transactions) ): ?>

            <?php echo fn_OP::get_filter_readable_string($filters); ?>

			<table class="list report" border="1">
				<tr>
					<td>Rulaj: </td>
					<td class="align-right"><?php echo $Currency->ccode; ?> <?php echo fn_Util::format_nr($Total); ?></td>
				</tr>
				<?php if ( !isset($filters['type']) or $filters['type'] == FN_OP_IN): ?>
				<tr>
					<td>Venit: </td>
					<td class="align-right"><?php echo $Currency->ccode; ?> <?php echo fn_Util::format_nr($Income); ?></td>
				</tr>
				<?php endif;?>
				<?php if ( !isset($filters['type']) or $filters['type'] == FN_OP_OUT): ?>
				<tr>
					<td>Cheltuieli: </td>
					<td class="align-right"><?php echo $Currency->ccode; ?> <?php echo fn_Util::format_nr($Outcome); ?></td>
				</tr>
				<?php endif; ?>
				<?php if ( !isset($filters['type']) ): ?>
				<tr class="highlight">
					<td>Balan&#355;a: </td>
					<td class="align-right">
						<strong> <?php echo $Currency->ccode; ?> <?php echo fn_Util::format_nr($Income - $Outcome); ?> </strong>
					</td>
				</tr>
				<?php endif; ?>
			</table>
			
			<br class="clear"/>
			
			<table class="list transactions" border="1">
				<tr>
                    <th>ID</th>
					<th>Tip</th>
					<th>Suma</th>
					<th>Moneda</th>
					<th>Data</th>
					<th>Etichete</th>
					<th>&nbsp;</th>
				</tr>
				<?php foreach ($Transactions as $transaction):  $k++; $trclass= ( $k%2 == 0) ? 'even' : 'odd'; $currency = fn_Currency::get($transaction->currency_id); ?>
				<tr class="<?php echo $trclass; ?>">
                    <td>#<?php echo $transaction->trans_id; ?></td>
                    <td>
                        <img src="images/<?php echo $transaction->optype; ?>.png" title="<?php echo ($transaction->optype == FN_OP_IN) ? 'venit' : 'cheltuiala'; ?>" align="middle" alt="<?php echo $transaction->optype; ?>"/>
                    </td>
					<td><?php echo fn_Util::format_nr( $transaction->value ); ?></td>
					<td><?php echo $currency->ccode; ?></td>
					<td><?php echo fn_UI::translate_date( date(FN_DAY_FORMAT, strtotime($transaction->sdate)) ); ?></td>
					<td>
						<?php $labels = fn_OP::get_labels($transaction->trans_id); $lc=0; if (count($labels))  foreach ($labels as $label): $lc++; ?>
							<?php echo fn_UI::esc_html($label->title); ?><?php if ( $lc < count($labels) ) echo ", "; ?>
						<?php endforeach;?>
					</td>
					<td>
						<button class="btn" onclick="fn_popup('<?php echo (FN_URL . "/snippets/transaction-details.php?id={$transaction->trans_id}"); ?>')">
							<span class="icon-info-sign"></span>
						</button>
						&nbsp;&nbsp;
						<button class="btn" onclick="confirm_delete('<?php fn_UI::page_url('transactions', array_merge($_GET, array('del'=>$transaction->trans_id))); ?>')">
							<span class="icon-remove"></span>
						</button>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>

            <div class="pagination">
                <?php $total = fn_OP::get_total($filters); if ( $total > $count ):?>
                <ul><?php fn_UI::pagination($total, $count, $_GET['pag'], fn_UI::page_url('transactions', $pagevars, FALSE)); ?></ul>
                <?php endif;?>
            </div>

			<?php else: $month = fn_UI::get_translated_month($month); if ($day > 0) $month = ($day . " {$month}"); ?>
			
				<p class="msg note">
                    Nu am gasit tranzac&#355;ii pentru <?php echo $month; ?> <?php echo $year; ?>. <a href="<?php fn_UI::page_url('transactions', array('t'=>'add')); ?>">Adaug&#259; &rarr;</a>
                </p>

			<?php endif; ?>
			
		<?php endif;?>
		
		<?php if ( $tab == 'generator' ) : ?>
			<?php include_once ( FNPATH . '/snippets/transactions-filter.php' );?>
		<?php endif;?>
		
		<?php if ( $tab == 'add' ) include_once ( FNPATH . '/snippets/transactions-add.php' ); ?>
		
	</div>
	
	<?php include_once ( FNPATH . '/snippets/sidebar.php' ); ?>
	
</div>