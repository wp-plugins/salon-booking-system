<?php

class SLN_Metabox_Attendant extends SLN_Metabox_Abstract
{
    public function add_meta_boxes()
    {
        $postType = $this->getPostType();
        add_meta_box(
            $postType . '-details',
            __('Attendant Details', 'sln'),
            array($this, 'details_meta_box'),
            $postType,
            'normal',
            'high'
        );
        remove_meta_box('postexcerpt', $postType, 'side');
        add_meta_box(
            'postexcerpt',
            __('Attendant description'),
            array($this, 'post_excerpt_meta_box'),
            $postType,
            'normal',
            'high'
        );
    }

    public function post_excerpt_meta_box($post)
    {
        ?>
        <label class="screen-reader-text" for="excerpt">
            <?php _e('Attendant Description', 'sln') ?>
        </label>
        <textarea rows="1" cols="40" name="excerpt"
                  id="excerpt"><?php echo $post->post_excerpt; // textarea_escaped ?></textarea>
        <p><?php _e('A very short description of this attendant. It is optional', 'sln'); ?></p>
    <?php
    }


    public function details_meta_box($object, $box)
    {
        echo $this->getPlugin()->loadView(
            'metabox/attendant',
            array(
                'metabox'  => $this,
                'settings' => $this->getPlugin()->getSettings(),
                'attendant'  => $this->getPlugin()->createAttendant($object),
                'postType' => $this->getPostType(),
                'helper'   => new SLN_Metabox_Helper()
            )
        );
        do_action($this->getPostType() . '_details_meta_box', $object, $box);
    }

    protected function getFieldList()
    {
        return array(
            'notav_from' => 'time',
            'notav_to'   => 'time',
            'notav_1'    => 'bool',
            'notav_2'    => 'bool',
            'notav_3'    => 'bool',
            'notav_4'    => 'bool',
            'notav_5'    => 'bool',
            'notav_6'    => 'bool',
            'notav_7'    => 'bool',
            'email'    => 'text',
        );
    }
}
