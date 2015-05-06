<?php if ( !defined('FNPATH') ) exit();

use FinFlow\UI;
use FinFlow\Util;
use FinFlow\Accounts;
use FinFlow\Currency;
use FinFlow\CanValidate;

$_section   = ( $s = url_part(2) ) ? $s : 'list';
$errors     = $warnings = $notices = array();
$activetab  = array();

$activetab[$_section] = 'active';

$account_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$errors     = array();
$notices   = false;

if( count($_POST) ){

    //---- add/edit an account ---//

    if( ! CanValidate::stringlen( post('holder_name') ) )
        $errors[] = "Numele detinatorului/emitentului lipseste.";

    if( empty($errors) ){

        $slug  = Util::make_slug( post('holder_name') );
        $row   = Accounts::get_by_slug($slug);

        if( $row and isset($row->account_id) and ($row->account_id != $account_id) ) //there's an account with the same slug
            $errors[] = "Exista deja un cont cu acelas emitent. Adauga o cifra sau o litera la numele emitentului, pentru a diferentia conturile";

    }

    if( empty($errors) ){

        $accountdata = array(
            'account_currency_id' => intval( post('account_currency_id') ),
            'account_slug'        => $slug,
            'holder_name'         => post('holder_name'),
            'balance'             => floatval( post('balance') )
        );

        if( isset($_POST['holder_swift']) )
	        $accountdata['holder_swift'] = strtoupper( $_POST['holder_swift'] );

        if( isset($_POST['account_iban']) )
	        $accountdata['account_iban']= strtoupper( $_POST['account_iban'] );

        if( $account_id ){ //Update account

            //--- reset amounts spent and won when updating the account balance ---//
            $accountdata['balance_won']  = 0;
            $accountdata['balance_spent']=0;
            //--- reset amounts spent and won when updating the account balance ---//

            $saved = Accounts::update($account_id, $accountdata);

        }else                //Add new account

            $saved = Accounts::add($accountdata);

        if( $saved )
            $notices[]= "Contul a fost salvat";
        else
            $errors[] = "Contul nu a putut fi salvat din cauza unei erori tehnice.";

        //---- add/edit an account ---//

    }

}

if ( isset($_GET['del']) ){

    //--- delete an account ---//
    $account_id = intval($_GET['del']); if ( $account_id ) Accounts::remove($account_id);
    //--- delete a account ---//

}

if ( ( $_section == 'add' ) or ( $_section == 'edit' ) ) {
    if ( empty($Currencies) ) $Currencies = Currency::get_all();
}

if( $_section == 'list' ){

    $per_page   = FN_RESULTS_PER_PAGE;
    $offset     = isset($_GET['pag']) ? fn_UI::pagination_get_current_offset($_GET['pag'], $per_page) : 0;

    $Accounts = Accounts::get_all($offset, $per_page);

}

?>

<div class="row">

    <div class="col-lg-8 col-md-8">

        <ul class="nav nav-justified nav-pills nav-page-menu" role="tablist">

            <li class="<?php echo av($activetab, 'list'); ?>">
	            <a href="<?php UI::url('accounts/list'); ?>">Lista</a>
            </li>

            <?php if($_section == 'edit'): ?>

	            <li class="separator"></li>

                <li class="<?php echo av($activetab, 'edit'); ?>"><a> Modific&#259; cont </a></li>

            <?php endif; ?>

	        <li class="separator"></li>

            <li class="<?php echo av($activetab, 'add'); ?>">
	            <a href="<?php UI::url('accounts/add'); ?>"> Adaug&#259; cont </a>
            </li>

        </ul>

        <?php

            if ( $_section == 'list' )
	            include_once 'accounts-list.php';

        ?>

        <?php

            if ( $_section == 'add' )
	            include_once 'accounts-add.php'; ?>

        <?php

            if ( $_section == 'edit' )
	            include_once 'accounts-edit.php';

        ?>

    </div>

	<?php UI::component('main/sidebar'); ?>

</div>