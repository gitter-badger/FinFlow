<?php if ( !defined('FNPATH') ) exit;

if( isset($_GET['edit']) ){

    $user_id = intval($_GET['edit']);

    $User = fn_User::get($_GET['edit']); $formsubmitURL = fn_UI::page_url('settings', array('t'=>'myaccount', 'edit'=>$user_id), false);
}
else{
    $User = null; $user_id=0; $formsubmitURL = fn_UI::page_url('settings', array('t'=>'myaccount'), false);
}

if( isset($_GET['del']) ) fn_User::delete( $_GET['del'] );

if( count($_POST) ){

    $Errors     = array();
    $Notices   = array();

    if( !fn_CheckValidityOf::stringlen($_POST['password'], 6))
        $Errors[] = "Parola trebuie sa aib&#259; minim 6 caractere";

    if( !fn_CheckValidityOf::email($_POST['email']))
        $Errors[] = "Adresa email aleas&#259; este invalid&#259;.";

    if( $_POST['password'] != $_POST['password_confirm'] )
        $Errors[] = "Parola aleas&#259; este diferit&#259; de cea confirmat&#259;.";

    if( empty($Errors) ){
        $exists = fn_User::get_by($_POST['email'], 'email'); if($exists and isset($exists->user_id) and ( $user_id != $exists->user_id ) )
            $Errors[] = "Adresa de email este deja &#238;nregistrat&#259;.";
    }

    if( empty($Errors) ){

        $userdata = array('email'=>$_POST['email'], 'password'=>$_POST['password']);

        if( $user_id )
            $success = fn_User::update($user_id, $userdata);
        else
            $success = fn_User::add($userdata);

        if( $success )
            $Notices[] = "Utilizatorul a fost salvat";
        else
            $Errors[] = "Oups! Utilizatorul nu a putut fi salvat din cauza unei erori tehnice.";

    }

}


$Users = fn_User::get_all();


?>

<?php fn_UI::show_errors($Errors); ?>

<?php fn_UI::show_notes($Notices); ?>

<?php if( count($Users) ):  ?>

    <table class="list transactions" border="1">
    <tr>
        <th>Email</th>
        <th>Ultima autentificare</th>
        <th>&nbsp;</th>
    </tr>
    <?php foreach ($Users as $user):
            $k++; $trclass= ( $k%2 == 0) ? 'even' : 'odd';
            if( $user->last_login ) $lastlogin = fn_UI::translate_date( date(FN_DATETIME_FORMAT, strtotime($user->last_login)) ); else $lastlogin = ""; ?>
    <tr class="<?php echo $trclass; ?>">
        <td><a href="mailto:<?php echo $user->email; ?>"><?php echo $user->email; ?></a> </td>
        <td><?php echo $lastlogin; ?></td>
        <td>

            <button class="btn" onclick="window.location.href='<?php fn_UI::page_url('settings', array('t'=>'myaccount', 'edit'=>$user->user_id)); ?>#userEditForm';">
                <span class="icon-pencil"></span>
            </button>

            <?php if( $user->user_id != fn_User::current_user_id() ): ?>
            &nbsp;&nbsp;
            <button class="btn" onclick="confirm_delete('<?php fn_UI::page_url('settings', array('t'=>'myaccount', 'del'=>$user->user_id)); ?>')">
                <span class="icon-remove"></span>
            </button>
            <?php endif; ?>

        </td>

    </tr>
    <?php endforeach; ?>
</table>

<br class="clear"/>

    <?php if($user_id): ?>

        <h4 class="form-label-normal"><em>Modific&#259; utilizator</em></h4>

        <form name="user-edit-form" id="userEditForm" method="post" action="<?php echo $formsubmitURL; ?>" target="_self">
            <p>
                <label for="email">Email:</label>
                <input type="email" size="45" maxlength="255" name="email" id="email" value="<?php echo fn_UI::extract_post_val('email', $User->email); ?>" />
            </p>
            <p>
                <label for="password">Parola:</label>
                <input type="password" size="45" maxlength="255" name="password" id="password" value="" />
            </p>
            <p>
                <label for="password_confirm">Confirm&#259; parola:</label>
                <input type="password" size="45" maxlength="255" name="password_confirm" id="password_confirm" value="" />
            </p>

            <p>
                <button class="btn btn-primary" type="submit">Salveaz&#259;</button>
            </p>
        </form>

    <?php else: ?>

        <h4 class="form-label-normal"><em>Adaug&#259; utilizator</em></h4>

        <form name="user-add-form" id="userAddForm" method="post" action="<?php echo $formsubmitURL; ?>" target="_self">
            <p>
                <label for="email">Email:</label>
                <input type="email" size="45" maxlength="255" name="email" id="email" value="<?php echo fn_UI::extract_post_val('email'); ?>" />
            </p>
            <p>
                <label for="password">Parola:</label>
                <input type="password" size="45" maxlength="255" name="password" id="password" value="" />
            </p>
            <p>
                <label for="password_confirm">Confirm&#259; parola:</label>
                <input type="password" size="45" maxlength="255" name="password_confirm" id="password_confirm" value="" />
            </p>

            <p>
                <button class="btn btn-primary" type="submit">Adaug&#259;</button>
            </p>
        </form>
    <?php endif; ?>

<?php else: ?>

    <?php fn_UI::msg("Oups! Nu am gasit utilizatori.", fn_UI::$MSG_NOTE); ?>

<?php endif; ?>