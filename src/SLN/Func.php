<?php

class SLN_Func
{
    public static function getDays()
    {
        $timestamp = strtotime('next Sunday');
        $ret       = array();
        for ($i = 1; $i <= 7; $i++) {
            $ret[$i]   = self::getDateDayName($timestamp);
            $timestamp = strtotime('+1 day', $timestamp);
        }

        return $ret;
    }

    public static function getDateDayName($day)
    {
        if ($day instanceof DateTime) {
            $day = $day->format('U');
        }

        return date_i18n('l', $day);
    }

    public static function countDaysBetweenDatetimes(DateTime $from, DateTime $to)
    {
        $datediff = abs($from->format('U') - $to->format('U'));

        return floor($datediff / (60 * 60 * 24));
    }

    public static function getMonths()
    {
        $timestamp = strtotime("1970-01-01");
        $ret       = array();
        for ($i = 1; $i <= 12; $i++) {
            $ret[$i]   = date_i18n('F', $timestamp);
            $timestamp = strtotime('+1 month', $timestamp);
        }

        return $ret;
    }

    public static function getYears($min = null, $max = null)
    {
        if (!isset($min)) {
            $min = date('Y') - 1;
        }
        if (!isset($max)) {
            $max = $min + 2;
        }
        $ret = array();
        for ($i = $min; $i <= $max || count($ret) > 10; $i++) {
            $ret[$i] = $i;
        }

        return $ret;
    }

    public static function filter($val, $filter = null)
    {
        if (empty($filter)) {
            return $val;
        }
        if ($filter == 'int') {
            return intval($filter);
        } elseif ($filter == 'money') {
            return number_format(floatval(str_replace(',', '.', $val)), 2);
        } elseif ($filter == 'float') {
            return floatval(str_replace(',', '.', $val));
        } elseif ($filter == 'time') {
            if ($val instanceof DateTime) {
                $val = $val->format('H:i');
            }
            if (empty($val)) {
                return null;
            }
            if (strpos($val, ':') === false) {
                $val .= ':00';
            }

            return date('H:i', strtotime('1970-01-01 ' . $val));
        } elseif ($filter == 'date') {
            if (is_array($val)) {
                $val = $val['year'] . '-' . $val['month'] . '-' . $val['day'];
            }

            return date('Y-m-d', strtotime($val));
        } elseif ($filter == 'bool') {
            return $val ? true : false;
        } elseif ($filter == 'set') {
            $ret = array();
            if (!is_array($val)) {
                return $ret;
            }
            foreach ($val as $k => $v) {
                if ($v) {
                    $ret[] = $k;
                }
            }

            return $ret;
        } else {
            return $val;
        }
    }

    static function addUrlParam($url, $k, $v)
    {
        return $url . (strpos($url, '?') === false ? '?' : '&') . http_build_query(array($k => $v));
    }

    static function currPageUrl()
    {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }

        return $pageURL;
    }

    public static function getIntervalItems()
    {
        return array(
            ''            => __('Always','sln'),
            '+30 minutes' => __('half hour','sln'),
            '+1 hour'     => '1 '.__('hour','sln'),
            '+2 hours'    => '2 '.__('hours','sln'),
            '+3 hours'    => '3 '.__('hours','sln'),
            '+4 hours'    => '4 '.__('hours','sln'),
            '+8 hours'    => '8 '.__('hours','sln'),
            '+16 hours'   => '16 '.__('hours','sln'),
            '+1 day'      => '1 '.__('day','sln'),
            '+2 days'     => '2 '.__('days','sln'),
            '+3 days'     => '3 '.__('days','sln'),
            '+4 days'     => '4 '.__('days','sln'),
            '+1 week'     => '1 '.__('week','sln'),
            '+2 weeks'    => '2 '.__('weeks','sln'),
            '+3 weeks'    => '3 '.__('weeks','sln'),
            '+1 month'    => '1 '.__('month','sln'),
            '+2 months'   => '2 '.__('months','sln'),
            '+3 months'   => '3 '.__('months','sln')
        );

        return array(
            ''      => 'Always',
            'PT30M' => 'half hour',
            'PT1H'  => '1 hour',
            'PT2H'  => '2 hours',
            'PT3H'  => '3 hours',
            'PT4H'  => '4 hours',
            'PT8H'  => '8 hours',
            'PT16H' => '16 hours',
            'P1D'   => '1 day',
            'P2D'   => '2 days',
            'P3D'   => '3 days',
            'P4D'   => '4 days',
            'P1W'   => '1 week',
            'P2W'   => '2 weeks',
            'P3W'   => '3 weeks',
            'P1M'   => '1 month',
            'P2M'   => '2 months',
            'P3M'   => '3 months'
        );
    }

    public static function getMinutesIntervals($interval = null, $maxItems = null)
    {
        $start = "00:00";

        $curr     = strtotime($start);
        $interval = isset($interval) ?
            $interval :
            SLN_Plugin::getInstance()->getSettings()->getInterval();
        $maxItems = isset($maxItems) ?
            $maxItems : 1440;
        $items    = array();
        do {
            $items[] = date("H:i", $curr);
            $curr    = strtotime('+' . $interval . ' minutes', $curr);
            $maxItems--;
        } while (date("H:i", $curr) != $start && $maxItems > 0);

        return $items;
    }
    public static function getMinutesFromDuration($duration){
        if(is_string($duration)){
        $tmp = explode($duration,':');
        return ($tmp[0]*60) + $tmp[1];
        }else{
            return 0;
        }
    }
}
