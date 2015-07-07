<?php

class SLN_PostType_Service extends SLN_PostType_Abstract
{
    public function init()
    {
        parent::init();

        if (is_admin()) {
            add_action('manage_' . $this->getPostType() . '_posts_custom_column', array($this, 'manage_column'), 10, 2);
            add_filter('manage_' . $this->getPostType() . '_posts_columns', array($this, 'manage_columns'));
            add_action('admin_head-post-new.php', array($this, 'posttype_admin_css'));
            add_action('admin_head-post.php', array($this, 'posttype_admin_css'));
        }
    }

    public function manage_columns($columns)
    {

        return array_merge(
            $columns,
            array(
                'service_duration' => __('Duration', 'sln'),
                'service_price'    => __('Price', 'sln')
            )
        );
    }

    public function manage_column($column, $post_id)
    {
        switch ($column) {
            case 'booking_status' :
                echo SLN_Enum_BookingStatus::getLabel(get_post_meta($post_id, '_sln_booking_status', true));
                break;
            case 'service_duration':
                $time = SLN_Func::filter(get_post_meta($post_id, '_sln_service_duration', true), 'time');
                echo $time ? $time : '-';
                break;
            case 'service_price' :
                echo $this->getPlugin()->format()->money(get_post_meta($post_id, '_sln_service_price', true));
                break;
        }
    }

    public function enter_title_here($title, $post)
    {

        if ($this->getPostType() === $post->post_type) {
            $title = __('Enter service name', 'sln');
        }

        return $title;
    }

    public function updated_messages($messages)
    {
        global $post, $post_ID;

        $messages[$this->getPostType()] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => sprintf(
                __('Service updated.', 'sln')
            ),
            2  => '',
            3  => '',
            4  => __('Service updated.', 'sln'),
            5  => isset($_GET['revision']) ? sprintf(
                __('Service restored to revision from %s', 'sln'),
                wp_post_revision_title((int)$_GET['revision'], false)
            ) : false,
            6  => sprintf(
                __('Service published.', 'sln')
            ),
            7  => __('Service saved.', 'sln'),
            8  => sprintf(
                __('Service submitted.', 'sln')
            ),
            9  => sprintf(
                __(
                    'Service scheduled for: <strong>%1$s</strong>. ',
                    'sln'
                ),
                date_i18n(__('M j, Y @ G:i', 'restaurant'), strtotime($post->post_date))
            ),
            10 => sprintf(
                __('Service draft updated.', 'sln')
            ),
        );


        return $messages;
    }

    protected function getPostTypeArgs()
    {
        return array(
            'public'              => true,
            'publicly_queryable'  => true,
            'exclude_from_search' => true,
            'show_in_menu'        => 'salon',
            'rewrite'             => false,
            'supports'            => array(
                'title',
                'excerpt',
                'thumbnail',
                'revisions',
            ),
            'labels'              => array(
                'name'               => __('Services', 'sln'),
                'singular_name'      => __('Service', 'sln'),
                'menu_name'          => __('Salon', 'sln'),
                'name_admin_bar'     => __('Salon Service', 'sln'),
                'all_items'          => __('Services', 'sln'),
                'add_new'            => __('Add Service', 'sln'),
                'add_new_item'       => __('Add New Service', 'sln'),
                'edit_item'          => __('Edit Service', 'sln'),
                'new_item'           => __('New Service', 'sln'),
                'view_item'          => __('View Service', 'sln'),
                'search_items'       => __('Search Services', 'sln'),
                'not_found'          => __('No services found', 'sln'),
                'not_found_in_trash' => __('No services found in trash', 'sln'),
                'archive_title'      => __('Services Archive', 'sln'),
            )
        );
    }

    function posttype_admin_css()
    {
        global $post_type;
        if ($post_type == SLN_Plugin::POST_TYPE_SERVICE) {
            ?>
            <style type="text/css">
                #post-preview, #view-post-btn,
                #edit-slug-box
                {
                    display: none;
                }
            </style>
        <?php
        }
    }
}
