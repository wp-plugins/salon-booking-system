<?php

class SLN_Enum_TimeFormat
{
    const _DEFAULT = 'default';
    const _SHORT   = 'short';

    private static $labels = array();
    private static $phpFormats = array(
        self::_DEFAULT => 'H:i',
        self::_SHORT => 'g:ia',
    );
    private static $jsFormats = array(
        self::_DEFAULT => 'hh:ii',
        self::_SHORT => 'H:iip'
    );

    public static function toArray()
    {
        return self::$labels;
    }

    public static function getLabel($key)
    {
        return isset(self::$labels[$key]) ? self::$labels[$key] : self::$labels[self::_DEFAULT];
    }
    public static function getPhpFormat($key)
    {
        return isset(self::$phpFormats[$key]) ? self::$phpFormats[$key] : self::$phpFormats[self::_DEFAULT];
    }
    public static function getJsFormat($key)
    {
        return isset(self::$jsFormats[$key]) ? self::$jsFormats[$key] : self::$jsFormats[self::_DEFAULT];
    }

    public static function init()
    {
        $d = new \DateTime;
        $d = $d->format('U');
        foreach(self::$phpFormats as $k => $v){
            self::$labels[$k] = date_i18n($v,$d); 
        }
    }
}

SLN_Enum_TimeFormat::init();
