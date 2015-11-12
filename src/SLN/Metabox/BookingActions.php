<?php

class SLN_Metabox_BookingActions extends SLN_Metabox_Abstract
{
    public function add_meta_boxes()
    {
        $pt = $this->getPostType();
        add_meta_box(
            $pt . '-actions',
            __('Booking Actions', 'sln'),
            array($this, 'actions_meta_box'),
            $pt,
            'side',
            'low'
        );
    }


    public function actions_meta_box($object, $box)
    {
        if(in_array($object->post_status,array('draft','auto-draft')))
            return '';
        echo $this->getPlugin()->loadView(
            'metabox/booking_actions',
            array(
                'metabox'  => $this,
                'settings' => $this->getPlugin()->getSettings(),
                'booking'  => $this->getPlugin()->createBooking($object),
                'postType' => $this->getPostType(),
                'helper'   => new SLN_Metabox_Helper()
            )
        );
        do_action($this->getPostType() . '_actions_meta_box', $object, $box);
    }

    protected function getFieldList()
    {
        return array(
        );
    }
    public function save_post($post_id, $post){

    } 
}

