<?php

class SLN_Shortcode_Salon_DetailsStep extends SLN_Shortcode_Salon_AbstractUserStep
{
    protected function dispatchForm()
    {
        global $current_user;
        if (isset($_POST['login_name'])) {
            $ret = $this->dispatchAuth($_POST['login_name'], $_POST['login_password']);
            get_currentuserinfo();
            $values = array(
                'firstname' => $current_user->user_firstname,
                'lastname'  => $current_user->user_lastname,
                'email'     => $current_user->user_email,
                'phone'     => get_user_meta($current_user->ID, '_sln_phone', true),
                'address'     => get_user_meta($current_user->ID, '_sln_address', true)
            );
            if (!$ret) {
                return false;
            }
        } else {
            $values = $_POST['sln'];
            if (!is_user_logged_in()) {
                if (empty($values['firstname'])) {
                    $this->addError(__('First name can\'t be empty', 'sln'));
                }
                if (empty($values['lastname'])) {
                    $this->addError(__('Last name can\'t be empty', 'sln'));
                }
                if (empty($values['email'])) {
                    $this->addError(__('e-mail can\'t be empty', 'sln'));
                }
                if (empty($values['phone'])) {
                    $this->addError(__('Mobile phone can\'t be empty', 'sln'));
                }
#                if (empty($values['address'])) {
#                    $this->addError(__('Address can\'t be empty', 'sln'));
#                } 
                if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
                    $this->addError(__('e-mail is not valid', 'sln'));
                }


                if ($this->getErrors()) {
                    $this->bindValues($values);
                    return false;
                }

                if (email_exists($values['email'])) {
                    $this->addError(__('E-mail exists', 'sln'));
                }
                if ($values['password'] != $values['password_confirm']) {
                    $this->addError(__('Passwords are different', 'sln'));
                }
                if ($this->getErrors()) {
                    $this->bindValues($values);
                    return false;
                }
                if(!$this->getShortcode()->needSms()) {
                    $this->successRegistration($values);
                }else{
                    $_SESSION['sln_detail_step'] = $values;
                }
            }else{
                wp_update_user(
                    array('ID' => $current_user->ID, 'first_name' => $values['firstname'], 'last_name' => $values['lastname'])
                );
                foreach(array('phone', 'address') as $k){
                    if(isset($values[$k])){
                       update_user_meta($current_user->ID, '_sln_'.$k, $values[$k]);
                    }
                }
            }
        }
        $this->bindValues($values);

        return true;
    }

}
