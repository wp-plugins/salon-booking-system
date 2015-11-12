<?php

class SLN_Metabox_Booking extends SLN_Metabox_Abstract
{
    public function add_meta_boxes()
    {
        $pt = $this->getPostType();
        add_meta_box(
            $pt . '-details',
            __('Booking details', 'sln'),
            array($this, 'details_meta_box'),
            $pt,
            'normal',
            'high'
        );
    }


    public function details_meta_box($object, $box)
    {
        echo $this->getPlugin()->loadView(
            'metabox/booking',
            array(
                'metabox'  => $this,
                'settings' => $this->getPlugin()->getSettings(),
                'booking'  => $this->getPlugin()->createBooking($object),
                'postType' => $this->getPostType(),
                'helper'   => new SLN_Metabox_Helper()
            )
        );
        do_action($this->getPostType() . '_details_meta_box', $object, $box);
    }

    protected function getFieldList()
    {
        return array(
            'amount'    => 'money',
            'deposit'   => 'money',
            'firstname' => '',
            'lastname'  => '',
            'email'     => '',
            'phone'     => '',
            'address'   => '',
            'date'      => 'date',
            'time'      => 'time',
            'attendant'  => '',
            'services'  => 'nofilter',
            'note' => '',
            'admin_note' => '',
            '_sln_calendar_event_id' => ''
        );
    }

    private $disabledSavePost = false;
    public function save_post($post_id, $post){
        if($this->disabledSavePost)
            return;

        if(!isset($_POST['_sln_booking_status']))
            return;
        if(isset($_POST['_sln_booking_services']))
        foreach($_POST['_sln_booking_services'] as $k => $v){
            $_POST['_sln_booking_services'][$k] = str_replace('sln_booking_services_','', $v);
        }
        parent::save_post($post_id, $post);
        $this->validate($_POST);
        if(isset($_SESSION['_sln_booking_user_errors']))
            return;

        $booking = new SLN_Wrapper_Booking($post_id);
        $booking->evalDuration();
        $s = $booking->getStatus();
        $new =  $_POST['_sln_booking_status'];
        if(strpos($new,'sln-b-') !== 0) $new = 'sln-b-pending';
        $postnew = array();
        if(strpos($s,'sln-b-') !== 0){
            $postnew = array_merge($postnew, array(
                'ID' => $post_id,
                'post_status' => $new
            ));
        }
        if(isset($_POST['_sln_booking_createuser']) && $_POST['_sln_booking_createuser']){
            $userid = $this->registration($booking);
            if($userid instanceof WP_Error)
                return;
            $postnew = array_merge($postnew, array(
                'ID' => $post_id,
                'post_author' => $userid
            ));
        }
        if(!empty($postnew)){
            $this->disabledSavePost = true;
            wp_update_post($postnew);
            $this->disabledSavePost = false;
        }
    } 

    protected function registration($booking){
        $errors = wp_create_user($booking->getEmail(), wp_generate_password(), $booking->getEmail());
        wp_update_user(
            array('ID' => $errors, 'first_name' => $booking->getFirstname(), 'last_name' => $booking->getLastname())
        );
        add_user_meta($errors, '_sln_phone', $booking->getPhone());
        add_user_meta($errors, '_sln_address', $booking->getAddress());
        if (is_wp_error($errors)) {
            $this->addError($errors->get_error_message());
        }
        wp_new_user_notification($errors); //, $values['password']);
        return $errors;
    }

    private function validate($values){
        if (empty($values['_sln_booking_firstname'])) {
            $this->addError(__('First name can\'t be empty', 'sln'));
        }
        if (empty($values['_sln_booking_lastname'])) {
            $this->addError(__('Last name can\'t be empty', 'sln'));
        }
        if (empty($values['_sln_booking_email'])) {
            $this->addError(__('e-mail can\'t be empty', 'sln'));
        }
        if (empty($values['_sln_booking_phone'])) {
            $this->addError(__('Mobile phone can\'t be empty', 'sln'));
        }
#       if (empty($values['address'])) {
#           $this->addError(__('Address can\'t be empty', 'sln'));
#       }
        if (!filter_var($values['_sln_booking_email'], FILTER_VALIDATE_EMAIL)) {
            $this->addError(__('e-mail is not valid', 'sln'));
        }
    }

    protected function addError($message){
        $_SESSION['_sln_booking_user_errors'][] = $message;
    }
}

