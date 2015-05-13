<?php

class SLN_Wrapper_Availability
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function toArray()
    {
        return $this->data;
    }

    public function getIntervals()
    {

    }
}