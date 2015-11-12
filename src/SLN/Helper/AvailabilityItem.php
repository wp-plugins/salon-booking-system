<?php

class SLN_Helper_AvailabilityItem
{
    private $data;
    private $times = array();

    function __construct($data)
    {
        $this->data = $data;
        if ($data['from'][0] != '00:00') {
            $this->times[] = array(
                strtotime($data['from'][0]),
                strtotime($data['to'][0]),
            );
        }
        if ($data['from'][1] != '00:00') {
            $this->times[] = array(
                strtotime($data['from'][1]),
                strtotime($data['to'][1]),
            );
        }
        if (empty($this->times)) {
            $this->times[] = array(strtotime('00:00'), strtotime('23:59'));
        }
    }

    public function isValidDate($date)
    {
        if ($date instanceof DateTime) {
            $date = $date->format('Y-m-d');
        }
        $dayOfTheWeek = date("w", strtotime($date)) + 1;

        return isset($this->data['days'][$dayOfTheWeek]) ? true : false;
    }

    public function isValidTime($date, $time)
    {
        if (!$this->isValidDate($date)) {
            return false;
        }

        return $this->checkTime($time);
    }

    private function checkTime($time)
    {
        if ($time instanceof DateTime) {
            $time = $time->format('H:i');
        }
        $time = strtotime($time);
        foreach ($this->times as $t) {
            if ($t[0] <= $time && $t[1] >= $time) {
                return true;
            }
        }

        return false;
    }

    public function __toString()
    {
        $days = SLN_Func::getDays();
        $ret  = array();
        foreach ($this->data['days'] as $d => $v) {
            $ret[] = $days[$d];
        }
        $ret = implode('-', $ret);
        foreach ($this->times as $t) {
            $ret .= sprintf(' %s/%s', $t[0], $t[1]);
        }

        return $ret;
    }
}
