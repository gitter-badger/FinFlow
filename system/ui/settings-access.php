<?php if ( !defined('FNPATH') ) exit; $Errors = $Notices = array();

if( isset($_GET['edit']) ){
    $user_id = intval($_GET['edit']);  $User = fn_User::get($_GET['edit']); $formsubmitURL = fn_UI::page_url('settings', array('t'=>'myaccount', 'edit'=>$user_id), false);
}
else{
    $User = null; $user_id=0; $formsubmitURL = fn_UI::page_url('settings', array('t'=>'myaccount'), false);
}

if( isset($_GET['del']) ) fn_User::delete( $_GET['del'] );

if( count($_POST) ){

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


$Users = fn_User::get_all(); ?>

<?php if( count($Users) ):  ?>

    <div class="panel panel-default">

        <table class="table table-striped list users">
            <tr>
                <th>Email</th>
                <th>Ultima autentificare</th>
                <th>&nbsp;</th>
            </tr>
            <?php foreach ($Users as $user): if( $user->last_login ) $lastlogin = fn_UI::translate_date( date(FN_DATETIME_FORMAT, strtotime($user->last_login)) ); else $lastlogin = ""; ?>
            <tr>
                <td><a href="mailto:<?php echo $user->email; ?>"><?php echo $user->email; ?></a> </td>
                <td><?php echo $lastlogin; ?></td>
                <td>

                    <button class="btn btn-default" onclick="window.location.href='<?php fn_UI::page_url('settings', array('t'=>'myaccount', 'edit'=>$user->user_id)); ?>#userEditForm';">
                        <span class="fa fa-edit"></span>
                    </button>

                    <?php if( $user->user_id != fn_User::current_user_id() ): ?>
                    <button class="btn btn-default" onclick="confirm_delete('<?php fn_UI::page_url('settings', array('t'=>'myaccount', 'del'=>$user->user_id)); ?>')">
                        <i class="fa fa-remove"></i>
                    </button>
                    <?php endif; ?>

                </td>

            </tr>
            <?php endforeach; ?>
        </table>

    </div>

    <div class="clearfix"></div>

    <?php fn_UI::show_errors($Errors); fn_UI::show_notes($Notices); ?>

    <?php if($user_id): ?>

        <h4><em>Modific&#259; utilizator</em></h4>

        <form class="form form-horizontal" name="user-edit-form" id="userEditForm" method="post" action="<?php echo $formsubmitURL; ?>" target="_self">

            <div class="form-group">
                <label class="control-label col-lg-3" for="email">Email:</label>
                <div class="col-lg-4">
                    <input class="form-control" type="email" size="45" maxlength="255" name="email" id="email" value="<?php echo fn_UI::extract_post_val('email', $User->email); ?>" />
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3" for="password">Parola:</label>
                <div class="col-lg-4">
                    <input class="form-control" type="password" size="45" maxlength="255" name="password" id="password" value="" />
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3" for="password_confirm">Confirm&#259; parola:</label>
                <div class="col-lg-4">
                    <input class="form-control" type="password" size="45" maxlength="255" name="password_confirm" id="password_confirm" value="" />
                </div>
            </div>

            <div class="form-group">
                <div class="col-lg-12 align-center form-submit">
                    <button class="btn btn-primary" type="submit">Salveaz&#259;</button>
                </div>
            </div>

        </form>

    <?php else: ?>

        <h4><em>Adaug&#259; utilizator</em></h4>

        <form class="form form-horizontal" name="user-add-form" id="userAddForm" method="post" action="<?php echo $formsubmitURL; ?>" target="_self">

            <div class="form-group">
                <label class="control-label col-lg-3" for="email">Email:</label>
                <div class="col-lg-4">
                    <input class="form-control" type="email" size="45" maxlength="255" name="email" id="email" value="<?php echo fn_UI::extract_post_val('email'); ?>" />
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3" for="password">Parola:</label>
                <div class="col-lg-4">
                    <input class="form-control" type="password" size="45" maxlength="255" name="password" id="password" value="" />
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3" for="password_confirm">Confirm&#259; parola:</label>
                <div class="col-lg-4">
                    <input class="form-control" type="password" size="45" maxlength="255" name="password_confirm" id="password_confirm" value="" />
                </div>
            </div>

            <div class="form-group">
                <div class="col-lg-12 align-center form-submit"><button class="btn btn-primary" type="submit">Adaug&#259;</button></div>
            </div>

        </form>

    <?php endif; ?>

<?php else: ?>

    <?php fn_UI::msg("Oops! Nu am gasit utilizatori.", fn_UI::$MSG_NOTE); ?>

<?php endif; ?>