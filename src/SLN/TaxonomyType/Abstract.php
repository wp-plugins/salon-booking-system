<?php

abstract class SLN_TaxonomyType_Abstract
{
    private $postTypes;
    protected $taxonomyType;
    private $plugin;

    public function __construct(SLN_Plugin $plugin, $taxonomyType, $postTypes)
    {
        $this->plugin   = $plugin;
        $this->taxonomyType = $taxonomyType;
        $this->postTypes = $postTypes;
        add_action('init', array($this, 'init'));
        add_action( 'admin_menu', array($this, 'initAdmin'));
    }

    public function init()
    {
	register_taxonomy( $this->taxonomyType, $this->postTypes, $this->getTaxonomyTypeArgs() );
    }
    abstract protected function getTaxonomyTypeArgs();
}
