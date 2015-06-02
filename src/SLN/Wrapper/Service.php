<?php

class SLN_Wrapper_Service extends SLN_Wrapper_Abstract
{
    function getPrice()
    {
        $post_id = $this->getId();
        $ret     = apply_filters('sln_service_price', get_post_meta($post_id, '_sln_service_price', true));
        $ret     = empty($ret) ? 0 : floatval($ret);

        return $ret;
    }


    function getUnitPerHour()
    {
        $post_id = $this->getId();
        $ret     = apply_filters('sln_service_unit', get_post_meta($post_id, '_sln_service_unit', true));
        $ret     = !empty($ret) ? 0 : intval($ret);

        return $ret;
    }

    function getDuration()
    {
        $post_id = $this->getId();
        $ret     = apply_filters('sln_service_duration', get_post_meta($post_id, '_sln_service_duration', true));
        if(empty($ret)){
            $ret = '00:00';
        }
        $ret     = SLN_Func::filter($ret, 'time');
        return new DateTime('1970-01-01 ' . $ret);
    }


    function isSecondary()
    {
        $post_id = $this->getId();
        $ret     = apply_filters('sln_service_secondary', get_post_meta($post_id, '_sln_service_secondary', true));
        $ret     = empty($ret) ? false : ($ret ? true : false);

        return $ret;
    }

    function getNotAvailableOn($key)
    {
        $post_id = $this->getId();
        $ret     = apply_filters(
            'sln_service_notav_' . $key,
            get_post_meta($post_id, '_sln_service_notav_' . $key, true)
        );
        $ret     = empty($ret) ? false : ($ret ? true : false);

        return $ret;
    }


    function getNotAvailableFrom()
    {
        return $this->getNotAvailableTime('from');
    }

    function getNotAvailableTo()
    {
        return $this->getNotAvailableTime('to');
    }

    function isNotAvailableOnDate(DateTime $date)
    {
        $key              = array_search(SLN_Func::getDateDayName($date), SLN_Func::getDays());
        $notAvailableDay  = $this->getNotAvailableOn($key);
        $time             = new DateTime('1970-01-01 ' . $date->format('H:i'));
        $notAvailableTime = $this->getNotAvailableFrom()
            && $this->getNotAvailableFrom() <= $time
            && $this->getNotAvailableTo()
            && $this->getNotAvailableTo() >= $time;

        return $notAvailableDay && $notAvailableTime;
    }

    function getNotAvailableTime($key)
    {
        $post_id = $this->getId();
        $ret     = apply_filters(
            'sln_service_notav_' . $key,
            get_post_meta($post_id, '_sln_service_notav_' . $key, true)
        );
        $ret     = SLN_Func::filter($ret, 'time');

        return new DateTime('1970-01-01 ' . $ret);
    }

    public function getNotAvailableString()
    {
        foreach (SLN_Func::getDays() as $k => $day) {
            if ($this->getNotAvailableOn($k)) {
                $ret[] = $day;
            }
        }
        $ret  = $ret ? __('on ', 'sln') . implode(', ', $ret) : '';
        $from = $this->getNotAvailableFrom()->format('H:i');
        $to   = $this->getNotAvailableTo()->format('H:i');
        if ($from != '00:00') {
            $ret .= __(' from ', 'sln') . $from;
        }
        if ($to != '00:00') {
            $ret .= __(' to ', 'sln') . $to;
        }

        return $ret;
    }

    public function getName()
    {
        return $this->object->post_title;
    }

    public function getContent()
    {
        return $this->object->post_excerpt;
    }

    public function __toString(){
        return $this->getName();
    }
}
