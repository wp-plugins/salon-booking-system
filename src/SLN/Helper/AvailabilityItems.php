<?php

class SLN_Helper_AvailabilityItems
{
    private $items;

    public function __construct($availabilities)
    {
        foreach ($availabilities as $item) {
            $this->items[] = new SLN_Helper_AvailabilityItem($item);
        }
        if (empty($this->items)) {
            $this->items = array(new SLN_Helper_AvailabilityItemNull(array()));
        }
    }

    /**
     * @return SLN_Helper_AvailabilityItem[]
     */
    public function toArray()
    {
        return $this->items;
    }

    public function isValidDatetime(DateTime $date)
    {
        return $this->isValidTime($date->format('Y-m-d'), $date->format('H:i'));
    }

    public function isValidDate($day)
    {
        foreach ($this->toArray() as $av) {
            if ($av->isValidDate($day)) {
                return true;
            }
        }

        return false;
    }

    public function isValidTime($date, $time)
    {
        foreach ($this->toArray() as $av) {
            if ($av->isValidTime($date, $time)) {
                return true;
            }
        }

        return false;
    }


}