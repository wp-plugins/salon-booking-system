<?php

abstract class SLN_Action_Ajax_Abstract
{
    /** @var  SLN_Plugin */
    protected $plugin;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    abstract public function execute();
}
