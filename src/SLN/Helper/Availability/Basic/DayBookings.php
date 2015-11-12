<?php

class SLN_Helper_Availability_Basic_DayBookings extends SLN_Helper_Availability_AbstractDayBookings
{

    /**
     * @return SLN_Wrapper_Booking[]
     */
    public function getBookingsByHour($hour, $minutes = null)
    {
        if (!isset($hour)) {
            $hour = $this->getDate()->format('H');
        }

        $now = clone $this->getDate();
        $now->setTime($hour, $minutes);
        $ret = array();
        foreach ($this->getBookings() as $b) {
            if ($b->getStartsAt() <= $now && $b->getStartsAt() >= $now) {
                $ret[] = $b;
            }
        }

        return $ret;
    }
}
