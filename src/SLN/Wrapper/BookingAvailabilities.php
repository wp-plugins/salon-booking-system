<?php

class SLN_Wrapper_Availabilities
{
    protected $items;

    public function __construct($data)
    {
        foreach ($data as $row) {
            $this->items = new SLN_Wrapper_Availability($row);
        }
    }

    public function getItems()
    {
        return $this->items;
    }

    public function toArray()
    {
        $ret = array();
        foreach ($this->items as $item) {
            $ret[] = $item->toArray();
        }

        return $ret;
    }

    public function toStrings()
    {

    }
}