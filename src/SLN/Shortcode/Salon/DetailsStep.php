<?php

class SLN_Shortcode_Salon_DetailsStep extends SLN_Shortcode_Salon_Step
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
                'phone'     => get_user_meta($current_user->ID, '_sln_phone', true)
            );
            if (!$ret) {
                return false;
            }
        } else {
            $values = $_POST['sln'];
            if (!is_user_logged_in()) {
                if (empty($values['firstname'])) {
                    $this->addError(__('firstname can\'t be empty', 'sln'));
                }
                if (empty($values['lastname'])) {
                    $this->addError(__('lastname can\'t be empty', 'sln'));
                }
                if (empty($values['email'])) {
                    $this->addError(__('email can\'t be empty', 'sln'));
                }
                if (empty($values['phone'])) {
                    $this->addError(__('phone can\'t be empty', 'sln'));
                }
                if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
                    $this->addError(__('email is not valid', 'sln'));
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
                $errors = wp_create_user($values['email'], $values['password'], $values['email']);
                wp_update_user(
                    array('ID' => $errors, 'first_name' => $values['firstname'], 'last_name' => $values['lastname'])
                );
                add_user_meta($errors, '_sln_phone', $values['phone']);
                if (is_wp_error($errors)) {
                    $this->addError($errors->get_error_message());
                }
                wp_new_user_notification($errors, $values['password']);
                if (!$this->dispatchAuth($values['email'], $values['password'])) {
                    $this->bindValues($values);
                    return false;
                }
            }else{
                wp_update_user(
                    array('ID' => $current_user->ID, 'first_name' => $values['firstname'], 'last_name' => $values['lastname'])
                );
                if(isset($values['phone'])){
                    update_user_meta($current_user->ID, '_sln_phone', $values['phone']);
                }
            }
        }
        $this->bindValues($values);

        return true;
    }

    private function dispatchAuth($username, $password)
    {
        global $user;
        $creds                  = array();
        $creds['user_login']    = $username;
        $creds['user_password'] = $password;
        $creds['remember']      = true;
        $user                   = wp_signon($creds, false);
        if (is_wp_error($user)) {
            $this->addError($user->get_error_message());

            return false;
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
                'phone'     => get_user_meta($current_user->ID, '_sln_phone', true)
            );
            $this->bindValues($values);
        }

        return parent::isValid();
    }

    private function bindValues($values)
    {
        $bb     = $this->getPlugin()->getBookingBuilder();
        $fields = array(
            'firstname' => '',
            'lastname'  => '',
            'email'     => '',
            'phone'     => ''
        );
        foreach ($fields as $field => $filter) {
            $bb->set($field, SLN_Func::filter($values[$field], $filter));
        }

        $bb->save();
    }
}
