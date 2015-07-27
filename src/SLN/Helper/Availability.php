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

    public function getHoursBeforeHelper()
    {
        if (!isset($this->hoursBefore)) {
            $this->hoursBefore = new SLN_Helper_HoursBefore($this->settings);
        }

        return $this->hoursBefore;
    }
    public function getHoursBeforeString(){
        return $this->getHoursBeforeHelper()->getHoursBeforeString();
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
            if (
                $avItems->isValidDatetime($d)
                && $this->isValidDate($d)
                && $this->isValidTime($d)
            ) {
                $ret[$time] = $time;
            }
        }
	SLN_Plugin::addLog(__CLASS__.' getTimes '.print_r($ret,true));
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
            $obj = SLN_Enum_AvailabilityModeProvider::getService($this->settings->get('availability_mode'), $date);
            $this->dayBookings = $obj;
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

    public function getBookingsHourCount($hour = null, $minutes = null)
    {
        return $this->getDayBookings()->countBookingsByHour($hour, $minutes);
    }
    public function validateAttendant(SLN_Wrapper_Attendant $attendant)
    {
        if ($attendant->isNotAvailableOnDate($this->date)) {
            return array(
                __('This assistant is not available  ', 'sln') . $attendant->getNotAvailableString()
            );
        }
        $ids = $this->getDayBookings()->countAttendantsByHour();
        if (
            isset($ids[$attendant->getId()])
        ) {
            return array(
                __('This assistant is unavailable during this period', 'sln') . $attendant->getNotAvailableString()
            );
        }
    }



    public function validateService(SLN_Wrapper_Service $service)
    {
        if ($service->isNotAvailableOnDate($this->date)) {
            return array(
                __('This service is unavailable ', 'sln') . $service->getNotAvailableString()
            );
        }
        $ids = $this->getDayBookings()->countServicesByHour();;
        if (
            $service->getUnitPerHour() > 0
            && isset($ids[$service->getId()])
            && $ids[$service->getId()] >= $service->getUnitPerHour()
        ) {
            return array(
                __('The service for this hour is currently full', 'sln') . $service->getNotAvailableString()
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

        return !($countHour && $this->getBookingsHourCount($date->format('H'), $date->format('i')) >= $countHour);
    }
}
