<?php
/**
 * @var SLN_Plugin                $plugin
 * @var string                    $formAction
 * @var string                    $submitName
 * @var SLN_Shortcode_Salon_Step $step
 */
$bb = $plugin->getBookingBuilder();
global $current_user;
get_currentuserinfo();
$values = array(
    'firstname' => $current_user->user_firstname,
    'lastname'  => $current_user->user_lastname,
    'email'     => $current_user->user_email,
    'phone'     => get_user_meta($current_user->ID, '_sln_phone', true)
);
?>
    <?php include '_errors.php' ?>
 
<?php if (!is_user_logged_in()): ?>
    <form method="post" action="<?php echo $formAction ?>" role="form">
        <h2><?php _e('Returning customer?', 'sln') ?><em><?php _e('Please, log-in.', 'sln') ?></em></h2>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="login_name"><?php _e('E-mail') ?></label>
                    <input name="login_name" type="text" class="form-control login-field"/>
                </div>
                <a href=" <?php echo wp_lostpassword_url($formAction) ?>"><?php _e('Forgot password?', 'sln') ?></a>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="login_password"><?php _e('Password') ?></label>
                    <input name="login_password" type="password" class="form-control login-field"/>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-success btn-block" name="<?php echo $submitName ?>"
                            value="next">
                        Login <i class="glyphicon glyphicon-user"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <h2><?php _e('Checkout as a guest', 'sln') ?>
        <em><?php _e('An account will be automatically created', 'sln') ?></em>
    </h2>

    <form method="post" action="<?php echo $formAction ?>" role="form">
        <div class="row">
            <?php foreach (array(
                               'firstname' => __('Firstname', 'sln'),
                               'lastname'  => __('Lastname', 'sln'),
                               'email'     => __('E-mail', 'sln'),
                               'phone'     => __('Phone', 'sln'),
                               'password'  => __('Password', 'sln'),
                               'password_confirm' => __('Confirm your password', 'sln')
                           ) as $field => $label): ?>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="<?php echo SLN_Form::makeID('sln[' . $field . ']') ?>"><?php echo $label ?></label>
                        <?php 
                            if(strpos($field, 'password') === 0){
                                SLN_Form::fieldText('sln[' . $field . ']', $bb->get($field), array('required' => true, 'type' => 'password'));
                            }else{
                                SLN_Form::fieldText('sln[' . $field . ']', $bb->get($field), array('required' => true));
                            }
                        ?>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
        <?php include "_form_actions.php" ?>
    </form>
<?php else: ?>
    <h2><?php _e('Checkout', 'sln') ?></h2>

    <form method="post" action="<?php echo $formAction ?>" role="form">
        <div class="row">
            <?php foreach (array(
                               'firstname' => __('Firstname', 'sln'),
                               'lastname'  => __('Lastname', 'sln'),
                               'email'     => __('E-mail', 'sln'),
                               'phone'     => __('Phone', 'sln')
                           ) as $field => $label): ?>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="<?php echo SLN_Form::makeID('sln[' . $field . ']') ?>"><?php echo $label ?></label>
                        <?php SLN_Form::fieldText('sln[' . $field . ']', $bb->get($field), array('required' => true)) ?>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
        <?php include "_form_actions.php" ?>
    </form>
<?php endif ?>

