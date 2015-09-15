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

        //https://weston.ruter.net/2013/04/02/do-not-change-the-default-timezone-from-utc-in-wordpress/
        //https://wordpress.org/support/topic/why-does-wordpress-set-timezone-to-utc


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
        $now = new SLN_DateTime(date('Y-m-d H:i:00'));
        $tmp = $now->format('i');
        $i             = SLN_Plugin::getInstance()->getSettings()->getInterval();
        $diff = $tmp % $i;
        if($diff > 0)
            $now->modify('+'.( $i - $diff).' minutes');
        $this->fromDate = $now;
        //$now->setTime($now->format('H'), $minutes);
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
        $str = $this->getHoursBeforeString();
        SLN_Plugin::addLog(__CLASS__.'Initialized with'.print_r($str,true));


    }
/*
    private function minutes(DateTime $date)
    {
        $interval = $this->settings->getInterval();
        $i        = $date->format('i');
        $ret      = (intval($i / $interval) + 1) * $interval;

        return $ret;
    }
*/
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

    public function getHoursBefore()
    {
        $from = $this->from;
        $to   = $this->to;

        return (object)compact('from', 'to');
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

/*
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
*/

}
