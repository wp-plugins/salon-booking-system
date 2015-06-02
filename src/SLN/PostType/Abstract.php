<?php

abstract class SLN_PostType_Abstract
{
    private $postType;
    private $plugin;

    public function __construct(SLN_Plugin $plugin, $postType)
    {
        $this->plugin   = $plugin;
        $this->postType = $postType;
        add_action('init', array($this, 'init'));
        add_filter('post_updated_messages', array($this, 'updated_messages'));
        add_filter('enter_title_here', array($this, 'enter_title_here'), 10, 2);
    }

    public function init()
    {
        register_post_type($this->getPostType(), $this->getPostTypeArgs());
    }

    abstract protected function getPostTypeArgs();

    abstract public function enter_title_here($title, $post);

    abstract public function updated_messages($messages);

    public function getPostType()
    {
        return $this->postType;
    }

    /**
     * @return SLN_Plugin
     */
    protected function getPlugin()
    {
        return $this->plugin;
    }
}