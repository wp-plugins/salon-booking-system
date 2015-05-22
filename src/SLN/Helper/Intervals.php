<?php

class SLN_Helper_Intervals
{
    /** @var  SLN_Helper_Availability */
    protected $availabilityHelper;
    protected $initialDate;
    protected $suggestedDate;

    protected $times;
    protected $years;
    protected $months;
    protected $days;
    protected $dates;

    public function __construct(SLN_Helper_Availability $availabilityHelper)
    {
        $this->availabilityHelper = $availabilityHelper;
        
    }

    public function setDatetime(DateTime $date)
    {
        $this->initialDate = $this->bindInitialDate($date);
        $ah                = $this->availabilityHelper;
        $times             = $ah->getTimes($date);
        $i                 = 0;
        while (empty($times) && $i < 100) {
            $date->modify('+1 days');
            $times = $ah->getTimes($date);
            $i++;
        }
        if (empty($times)) {
            $date->modify('-99 days');
            while (empty($times) && $i > 0) {
                $date->modify('-1 days');
                $times = $ah->getTimes($date);
                $i--;
            }
        }
        $this->times   = $times;
        $suggestedTime = $date->format('H:i');
        $i             = SLN_Plugin::getInstance()->getSettings()->getInterval();
        $timeout = 0;
        while ($timeout < 86400 && !isset($times[$suggestedTime])) {
            $date->modify("+$i minutes");
            $suggestedTime = $date->format('H:i');
            $timeout++;
        }
        $this->suggestedDate = $date;
        $this->bindDates($ah->getDays());
        ksort($this->times);
        ksort($this->years);
        ksort($this->days);
        ksort($this->months);
    }

    public function bindInitialDate($date)
    {
        $from = $this->availabilityHelper->getHoursBeforeHelper()->getFromDate();
        if ($date < $from) {
            $date = $from;
        }

        return $date;
    }

    private function bindDates($dates)
    {
        $this->years  = array();
        $this->months = array();
        $this->days   = array();
        $checkDay     = $this->suggestedDate->format('Y-m-');
        $checkMonth   = $this->suggestedDate->format('Y-');
        foreach ($dates as $date) {
            list($year, $month, $day) = explode('-', $date);
            $this->years[$year] = true;
            if (strpos($date, $checkMonth) === 0) {
                $this->months[$month] = true;
            }
            if (strpos($date, $checkDay) === 0) {
                $this->days[$day] = true;
            }
            $this->dates[] = $date;
        }
        foreach ($this->years as $k => $v) {
            $this->years[$k] = $k;
        }

        $months = SLN_Func::getMonths();
        foreach ($this->months as $k => $v) {
            $this->months[$k] = $months[intval($k)];
        }
        foreach ($this->days as $k => $v) {
            $this->days[$k] = $k; //. date_i18n(' l',strtotime($checkDay.$k));
        }
        ksort($this->years);
        ksort($this->months);
        ksort($this->days);
    }

    public function toArray()
    {
        return array(
            'years'          => $this->getYears(),
            'months'         => $this->getMonths(),
            'days'           => $this->getDays(),
            'times'          => $this->getTimes(),
            'dates'          => $this->getDates(),
            'suggestedDay'   => $this->suggestedDate->format('d'),
            'suggestedMonth' => $this->suggestedDate->format('m'),
            'suggestedYear'  => $this->suggestedDate->format('Y'),
            'suggestedDate' => ucwords(date_i18n('d M Y', $this->suggestedDate->format('U'))),
            'suggestedTime'  => $this->suggestedDate->format('H:i'),
        );
    }

    /**
     * @return mixed
     */
    public function getInitialDate()
    {
        return $this->initialDate;
    }

    /**
     * @return mixed
     */
    public function getSuggestedDate()
    {
        return $this->suggestedDate;
    }

    /**
     * @return mixed
     */
    public function getTimes()
    {
        return $this->times;
    }

    /**
     * @return mixed
     */
    public function getYears()
    {
        return $this->years;
    }

    /**
     * @return mixed
     */
    public function getMonths()
    {
        return $this->months;
    }

    /**
     * @return mixed
     */
    public function getDays()
    {
        return $this->days;
    }
    public function getDates(){
        return $this->dates;
    }
}
