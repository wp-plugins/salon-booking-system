<?php

abstract class SLN_Shortcode_Salon_AbstractUserStep extends SLN_Shortcode_Salon_Step
{
    protected function successRegistration($values){
        $errors = wp_create_user($values['email'], $values['password'], $values['email']);
        wp_update_user(
            array('ID' => $errors, 'first_name' => $values['firstname'], 'last_name' => $values['lastname'])
        );
        add_user_meta($errors, '_sln_phone', $values['phone']);
        add_user_meta($errors, '_sln_address', $values['address']);
        if (is_wp_error($errors)) {
            $this->addError($errors->get_error_message());
        }
        wp_new_user_notification($errors); //, $values['password']);
        if (!$this->dispatchAuth($values['email'], $values['password'])) {
            $this->bindValues($values);
            return false;
        }
    }

    protected function dispatchAuth($username, $password)
    {
        if(empty($username)){
            $this->addError(__('username can\'t be empty'));
        }
        if(empty($password)){
            $this->addError(__('password can\'t be empty'));
        }
        if(empty($username) || empty($password)){
            return;
        }
        global $user;
        $creds                  = array();
        $creds['user_login']    = $username;
        $creds['user_password'] = $password;
        $creds['remember']      = true;
        $user                   = wp_signon($creds, false);
 
        if (is_wp_error($user)) {
            $this->addError(__('Bad credentials'));
            $this->addError($user->get_error_message());

            return false;
        }else{
            wp_set_current_user($user->ID);
            //global $current_user;
            //$current_user = new WP_User($user->ID);
        }

        return true;
    }

    public function isValid()
    {
        $bb = $this->getPlugin()->getBookingBuilder();
        if (!$bb->get('email') && is_user_logged_in()) {
            global $current_user;
            get_currentuserinfo();
            $values = array(
                'firstname' => $current_user->user_firstname,
                'lastname'  => $current_user->user_lastname,
                'email'     => $current_user->user_email,
                'phone'     => get_user_meta($current_user->ID, '_sln_phone', true),
                'address'     => get_user_meta($current_user->ID, '_sln_address', true)
            );
            $this->bindValues($values);
        }

        return parent::isValid();
    }

    protected function bindValues($values)
    {
        $bb     = $this->getPlugin()->getBookingBuilder();
        $fields = array(
            'firstname' => '',
            'lastname'  => '',
            'email'     => '',
            'phone'     => '',
            'address'     => ''
        );
        foreach ($fields as $field => $filter) {
            $data = isset($values[$field]) ? $values[$field] : '';
            $bb->set($field, SLN_Func::filter($data, $filter));
        }

        $bb->save();
    }
}
