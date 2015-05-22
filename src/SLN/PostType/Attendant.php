<?php

class SLN_PostType_Attendant extends SLN_PostType_Abstract
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
            )
        );
    }

    public function manage_column($column, $post_id)
    {
        switch ($column) {
        }
    }

    public function enter_title_here($title, $post)
    {

        if ($this->getPostType() === $post->post_type) {
            $title = __('Enter attendant name', 'sln');
        }

        return $title;
    }

    public function updated_messages($messages)
    {
        global $post, $post_ID;

        $messages[$this->getPostType()] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => sprintf(
                __('Attendant updated.', 'sln')
            ),
            2  => '',
            3  => '',
            4  => __('Attendant updated.', 'sln'),
            5  => isset($_GET['revision']) ? sprintf(
                __('Attendant restored to revision from %s', 'sln'),
                wp_post_revision_title((int)$_GET['revision'], false)
            ) : false,
            6  => sprintf(
                __('Attendant published.', 'sln')
            ),
            7  => __('Attendant saved.', 'sln'),
            8  => sprintf(
                __('Attendant submitted.', 'sln')
            ),
            9  => sprintf(
                __(
                    'Attendant scheduled for: <strong>%1$s</strong>. ',
                    'sln'
                ),
                date_i18n(__('M j, Y @ G:i', 'restaurant'), strtotime($post->post_date))
            ),
            10 => sprintf(
                __('Attendant draft updated.', 'sln')
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
                'name'               => __('Attendants', 'sln'),
                'singular_name'      => __('Attendant', 'sln'),
                'menu_name'          => __('Salon', 'sln'),
                'name_admin_bar'     => __('Salon Attendant', 'sln'),
                'all_items'          => __('Attendants', 'sln'),
                'add_new'            => __('Add Attendant', 'sln'),
                'add_new_item'       => __('Add New Attendant', 'sln'),
                'edit_item'          => __('Edit Attendant', 'sln'),
                'new_item'           => __('New Attendant', 'sln'),
                'view_item'          => __('View Attendant', 'sln'),
                'search_items'       => __('Search Attendants', 'sln'),
                'not_found'          => __('No attendants found', 'sln'),
                'not_found_in_trash' => __('No attendants found in trash', 'sln'),
                'archive_title'      => __('Attendants Archive', 'sln'),
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
