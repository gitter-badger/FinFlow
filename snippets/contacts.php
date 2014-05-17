<?php if ( !defined('FNPATH') ) exit();

$tab = isset($_GET['t']) ? urldecode($_GET['t']) : 'list'; $activetab = array(); $activetab[$tab] = 'active';

$contact_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if( count($_POST) ){

    $errors     = array();
    $notices   = false;

    //TODO

}

if ( isset($_GET['del']) ){

    //--- delete an account ---//
    $contact_id = intval($_GET['del']); //TODO if ( $contact_id ) fn_Accounts::remove($contact_id);
    //--- delete a account ---//

}

if( $tab == 'list' ){

    $per_page   = FN_RESULTS_PER_PAGE;
    $offset         = isset($_GET['pag']) ? fn_UI::pagination_get_current_offset($_GET['pag'], $per_page) : 0;

    //TODO $Accounts = fn_Accounts::get_all($offset, $per_page);

}

?>

<div class="row content">

    <div class="span10">

        <ul class="nav nav-tabs">
            <li class="<?php echo $activetab['list']; ?>"><a href="<?php fn_UI::page_url('contacts', array('t'=>'list'))?>">Lista</a></li>
            <?php if($tab == 'edit'): ?><li class="<?php echo $activetab['edit']; ?>"><a> Modific&#259; cont </a></li><?php endif; ?>
            <li class="<?php echo $activetab['add']; ?>"><a href="<?php fn_UI::page_url('contacts', array('t'=>'add'))?>"> Adaug&#259; contact </a></li>
        </ul>

        ... under construction ...

    </div>

    <?php include_once ( FNPATH . '/snippets/sidebar.php' ); ?>

</div>