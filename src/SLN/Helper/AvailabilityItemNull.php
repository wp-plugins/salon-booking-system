<?php

class SLN_Helper_AvailabilityItemNull extends SLN_Helper_AvailabilityItem
{
    public function isValidDate($date)
    {
        return true;
    }

    public function isValidTime($date, $time)
    {
        return true;
    }
}
