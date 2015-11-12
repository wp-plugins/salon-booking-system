<?php

class SLN_Enum_AvailabilityModeProvider
{

    private static $labels = array();

    private static $classes = array(
        'basic' => 'SLN_Helper_Availability_Basic_DayBookings',
        'advanced' => 'SLN_Helper_Availability_Advanced_DayBookings'
    );

    public static function toArray()
    {
        return self::$labels;
    }

    public static function getLabel($key)
    {
        if (isset(self::$labels[$key])) {
            throw new \Exception('label not found');
        }
        return self::$labels[$key];
    }

    /**
     * @param $key
     * @param DateTime $date
     * @return SLN_Helper_Availability_AbstractDayBookings
     * @throws Exception
     */
    public static function getService($key,DateTime $date)
    {
        $name = self::getServiceName($key);
        return new $name($date);
    }

    public static function getServiceName($key){
        if (!isset(self::$classes[$key])) {
            throw new \Exception(sprintf('provider "%s" not found',$key));
        }
        return self::$classes[$key];
    }

    public static function init()
    {
        self::$labels = array(
            'basic' => __('Basic (checks only the booking date)', 'sln'),
            'advanced' => __('Advanced (evaluates also booking duration)', 'sln'),
        );
    }
}

SLN_Enum_AvailabilityModeProvider::init();