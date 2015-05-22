<?php

class SLN_Helper_HoursBefore
{
    private $settings;
    private $from;
    private $to;
    private $fromString;
    private $toString;
    private $fromDate;
    private $toDate;

    public function __construct(SLN_Settings $settings)
    {
        $this->settings = $settings;
        $this->from     = $this->settings->get('hours_before_from');
        $this->to       = $this->settings->get('hours_before_to');

        $txt = SLN_Func::getIntervalItems();
        if ($this->from) {
            $this->fromString = $txt[$this->from];
        }
        if ($this->to) {
            $this->toString = $txt[$this->to];
        }
        $this->fromDate = $now = new DateTime;
        $minutes = $this->minutes($now);
        $now->setTime($now->format('H'), $minutes);
        $this->toDate = $now2 = clone $now;
        if ($this->from) {
            $now->modify($this->from);
        } else {
            $now->modify('+30 minutes');
        }
        if ($this->to) {
            $now2->modify($this->to);
        } else {
            $this->toDate = new DateTime('+1 year');
        }
    }

    private function minutes(DateTime $date)
    {
        $interval = $this->settings->getInterval();
        $i        = $date->format('i');
        $ret      = (intval($i / $interval) + 1) * $interval;

        return $ret;
    }

    public function getFromDate()
    {
        return $this->fromDate;
    }

    public function getToDate()
    {
        return $this->toDate;
    }


    public function check(DateTime $date)
    {
        return $this->isValidFrom($date) && $this->isValidTo($date);
    }

    public function isValidFrom(DateTime $date)
    {
        return $date >= $this->getFromDate();
    }

    public function isValidTo($date)
    {
        $to = $this->getToDate();
        if (!$to) {
            return true;
        }

        return $date <= $to;
    }
}
