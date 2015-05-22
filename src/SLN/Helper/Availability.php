<?php

class SLN_Helper_Availability
{
    const MAX_DAYS = 365;

    private $settings;
    private $date;
    /** @var  SLN_Helper_AvailabilityDayBookings */
    private $dayBookings;
    /** @var  SLN_Helper_HoursBefore */
    private $hoursBefore;

    public function __construct(SLN_Settings $settings)
    {
        $this->settings = $settings;
    }

    public function getHoursBefore()
    {
        $from = $this->settings->get('hours_before_from');
        $to   = $this->settings->get('hours_before_to');

        return (object)compact('from', 'to');
    }

    public function getHoursBeforeHelper()
    {
        if (!isset($this->hoursBefore)) {
            $this->hoursBefore = new SLN_Helper_HoursBefore($this->settings);
        }

        return $this->hoursBefore;
    }

    public function getHoursBeforeString()
    {
        $txt = SLN_Func::getIntervalItems();
        $ret = $this->getHoursBefore();
        if ($ret->from) {
            $ret->from = $txt[$ret->from];
        }
        if ($ret->to) {
            $ret->to = $txt[$ret->to];
        }

        return $ret;
    }

    public function getHoursBeforeDateTime()
    {
        $ret     = $this->getHoursBefore();
        $ret->from = $now     = new DateTime();
        $minutes = $this->minutes($now);
        $now->setTime($now->format('H'), $minutes);
        $ret->to = $now2 = clone $now;
        if ($ret->from) {
            $now->modify($ret->from);
        } else {
            $now->modify('+30 minutes');
        }
        if ($ret->to) {
            $now2->modify($ret->to);
        }

        return $ret;
    }

    public function getDays()
    {
        $interval = $this->getHoursBeforeHelper();
        $from     = $interval->getFromDate();
        $count    = SLN_Func::countDaysBetweenDatetimes($from, $interval->getToDate());
        $ret      = array();
        $avItems  = $this->getItems();
        while ($count > 0) {
            $date = $from->format('Y-m-d');
            $count--;
            if ($avItems->isValidDate($date) && $this->isValidDate($from)) {
                $ret[] = $date;
            }
            $from->modify('+1 days');
        }

        return $ret;
    }

    public function getTimes($date)
    {
        $ret     = array();
        $avItems = $this->getItems();
        $hb      = $this->getHoursBeforeHelper();
        foreach (SLN_Func::getMinutesIntervals() as $time) {
            $d = new DateTime($date->format('Y-m-d') . ' ' . $time);
            if ($avItems->isValidTime($date, $time)
                && $hb->check($d)
                && $this->isValidTime($d)
            ) {
                $ret[$time] = $time;
            }
        }

        return $ret;
    }

    private function minutes(DateTime $date)
    {
        $interval = $this->settings->getInterval();
        $i        = $date->format('i');
        $ret      = (intval($i / $interval) + 1) * $interval;

        return $ret;
    }

    public function setDate(DateTime $date)
    {
        if (empty($this->date) || $this->date->format('Ymd') != $date->format('Ymd')) {
            $this->dayBookings = new SLN_Helper_AvailabilityDayBookings($date);
        }

        $this->date = $date;

        return $this;
    }

    /**
     * @return SLN_Helper_AvailabilityDayBookings
     */
    public function getDayBookings()
    {
        return $this->dayBookings;
    }

    public function getBookingsDayCount()
    {
        return $this->getDayBookings()->countBookingsByDay();
    }

    public function getBookingsHourCount($hour = null)
    {
        return $this->getDayBookings()->countBookingsByHour($hour);
    }
    public function validateAttendant(SLN_Wrapper_Attendant $attendant)
    {
        if ($attendant->isNotAvailableOnDate($this->date)) {
            return array(
                __('this attendant is not available ', 'sln') . $attendant->getNotAvailableString()
            );
        }
        $ids = $this->getDayBookings()->countAttendantsByHour();
        if (
            isset($ids[$attendant->getId()])
        ) {
            return array(
                __('this attendant is busy in this hour', 'sln') . $attendant->getNotAvailableString()
            );
        }
    }



    public function validateService(SLN_Wrapper_Service $service)
    {
        if ($service->isNotAvailableOnDate($this->date)) {
            return array(
                __('this service is not available ', 'sln') . $service->getNotAvailableString()
            );
        }
        $ids = $this->getDayBookings()->countServicesByHour();;
        if (
            $service->getUnitPerHour() > 0
            && isset($ids[$service->getId()])
            && $ids[$service->getId()] >= $service->getUnitPerHour()
        ) {
            return array(
                __('this service is full in this hour', 'sln') . $service->getNotAvailableString()
            );
        }
    }

    /**
     * @return SLN_Helper_AvailabilityItems
     */
    public function getItems()
    {
        if (!isset($this->items)) {
            $this->items = new SLN_Helper_AvailabilityItems($this->settings->get('availabilities'));
        }

        return $this->items;
    }

    public function isValidDate($date)
    {
        $this->setDate($date);
        $countDay = $this->settings->get('parallels_day');

        return !($countDay && $this->getBookingsDayCount() >= $countDay);
    }

    public function isValidTime($date)
    {
        if (!$this->isValidDate($date)) {
            return false;
        }
        $countHour = $this->settings->get('parallels_hour');

        return !($countHour && $this->getBookingsHourCount($date->format('H')) >= $countHour);
    }
}
