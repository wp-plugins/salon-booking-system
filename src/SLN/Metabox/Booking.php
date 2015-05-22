<?php

class SLN_Metabox_Booking extends SLN_Metabox_Abstract
{
    public function add_meta_boxes()
    {
        $pt = $this->getPostType();
        add_meta_box(
            $pt . '-details',
            __('Booking Details', 'sln'),
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
            'firstname' => '',
            'lastname'  => '',
            'email'     => '',
            'phone'     => '',
            'duration'  => 'time',
            'date'      => 'date',
            'time'      => 'time',
            'attendant'  => '',
            'services'  => 'set',
        );
    }
    public function save_post($post_id, $post){
        if(isset($_POST['emailto']) && isset($_POST['emailto_submit']) && !empty($_POST['emailto'])){
            $booking = new SLN_Wrapper_Booking($post_id);
            $to = $_POST['emailto'];
            $this->getPlugin()->sendMail(
                'mail/summary',
                compact('booking','to')
            );
        }
        parent::save_post($post_id, $post);
        $booking = new SLN_Wrapper_Booking($post_id);
        $booking->evalDuration();
    } 

}

