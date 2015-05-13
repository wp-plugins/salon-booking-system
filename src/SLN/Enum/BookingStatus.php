<?php

class SLN_Enum_BookingStatus
{
    const ERROR = 'sln-b-error';
    const PENDING = 'sln-b-pending';
    const PAID = 'sln-b-paid';
    const PAY_LATER = 'sln-b-paylater';
    const CANCELED = 'sln-b-canceled';
    const CONFIRMED = 'sln-b-confirmed';

    private static $labels;

    private static $colors = array(
            self::ERROR     => 'default',
            self::PENDING   => 'warning',
            self::PAID      => 'success',
            self::PAY_LATER => 'info',
            self::CANCELED  => 'danger',
            self::CONFIRMED => 'success'
    );

    public static function toArray()
    {
        return self::$labels;
    }

    public static function getLabel($key)
    {
        return isset(self::$labels[$key]) ? self::$labels[$key] : self::$labels[self::ERROR];
    }
    public static function getColor($key)
    {
        return isset(self::$colors[$key]) ? self::$colors[$key] : self::$colors[self::ERROR];
    }


    public static function init()
    {
        self::$labels = array(
            self::ERROR     => __('ERROR', 'sln'),
            self::PENDING   => __('Pending', 'sln'),
            self::PAID      => __('Paid', 'sln'),
            self::PAY_LATER => __('Pay later', 'sln'),
            self::CANCELED  => __('Canceled', 'sln'),
            self::CONFIRMED => __('Confirmed', 'sln')
        );
    }
}

SLN_Enum_BookingStatus::init();
