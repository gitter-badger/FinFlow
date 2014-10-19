<?php if ( !defined('FNPATH') ) exit();

$tab = isset($_GET['t']) ? urldecode($_GET['t']) : 'list'; $activetab = array(); $activetab[$tab] = 'active';

$account_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$errors     = array();
$notices   = false;

if( count($_POST) ){

    //---- add/edit an account ---//

    if( !fn_CheckValidityOf::stringlen( $_POST['holder_name'] ) )
        $errors[] = "Numele detinatorului/emitentului lipseste.";

    if( empty($errors) ){
        $slug  = fn_Util::make_slug($_POST['holder_name']);
        $row = fn_Accounts::get_by_slug($slug);

        if( $row and isset($row->account_id) and ($row->account_id != $account_id) ) //there's an account with the same slug
            $errors[] = "Exista deja un cont cu acelas emitent. Adauga o cifra sau o litera la numele emitentului, pentru a diferentia conturile";
    }

    if( empty($errors) ){

        $accountdata = array(
            'account_currency_id' => intval($_POST['account_currency_id']),
            'account_slug'            => $slug,
            'holder_name'           => $_POST['holder_name'],
            'balance'                  => floatval($_POST['balance'])
        );

        if( isset($_POST['holder_swift']) )  $accountdata['holder_swift'] = strtoupper( $_POST['holder_swift'] );
        if( isset($_POST['account_iban']) )  $accountdata['account_iban']= strtoupper( $_POST['account_iban'] );

        if( $account_id ){ //Update account

            //--- reset amounts spent and won when updating the account balance ---//
            $accountdata['balance_won'] = 0;
            $accountdata['balance_spent']=0;
            //--- reset amounts spent and won when updating the account balance ---//

            $saved = fn_Accounts::update($account_id, $accountdata);

        }else                //Add new account
            $saved = fn_Accounts::add($accountdata);

        if( $saved )
            $notices[] = "Contul a fost salvat";
        else
            $errors[] = "Contul nu a putut fi salvat din cauza unei erori tehnice.";

        //---- add/edit an account ---//

    }

}

if ( isset($_GET['del']) ){

    //--- delete an account ---//
    $account_id = intval($_GET['del']); if ( $account_id ) fn_Accounts::remove($account_id);
    //--- delete a account ---//

}

if ( ( $tab == 'add' ) or ( $tab == 'edit' ) ) {
    if ( empty($Currencies) ) $Currencies = fn_Currency::get_all();
}

if( $tab == 'list' ){

    $per_page   = FN_RESULTS_PER_PAGE;
    $offset         = isset($_GET['pag']) ? fn_UI::pagination_get_current_offset($_GET['pag'], $per_page) : 0;

    $Accounts = fn_Accounts::get_all($offset, $per_page);

}

?>

<div class="row">

    <div class="<?php fn_UI::main_container_grid_class(); ?>">

        <ul class="nav nav-tabs">
            <li class="<?php echo $activetab['list']; ?>"><a href="<?php fn_UI::page_url('accounts', array('t'=>'list'))?>">Lista</a></li>
            <?php if($tab == 'edit'): ?><li class="<?php echo $activetab['edit']; ?>"><a> Modific&#259; cont </a></li><?php endif; ?>
            <li class="<?php echo $activetab['add']; ?>"><a href="<?php fn_UI::page_url('accounts', array('t'=>'add'))?>"> Adaug&#259; cont </a></li>
        </ul>

        <?php if ( $tab == 'list' ) include_once 'accounts-list.php'; ?>

        <?php if ( $tab == 'add' ) include_once 'accounts-add.php'; ?>

        <?php if ( $tab == 'edit' ) include_once 'accounts-edit.php'; ?>

    </div>

    <?php include_once ( FNPATH . '/snippets/sidebar.php' ); ?>

</div>