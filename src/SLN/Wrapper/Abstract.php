<?php

abstract class SLN_Wrapper_Abstract
{
    protected $object;

    function __construct($object)
    {
        if (!is_object($object)) {
            $object = get_post($object);
        }
        $this->object = $object;
    }

    function getId()
    {
        return $this->object->ID;
    }
}