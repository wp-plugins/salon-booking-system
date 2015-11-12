<?php

class SLN_DateTime extends DateTime
{
    public static $Format = 'Y-m-d H:i:s';
 
    public function formatWithWordpress($format){
        return date_i18n($format, $this->format('U'));
    }
    public function formatLocal($format){
        $off = get_option('gmt_offset');
        $this->modify(($off > 0 ? '+'.$off : $off).' hours');
        $ret = parent::format($format);
        $this->modify(($off > 0 ? '-'.$off : '+'.abs($off) ).' hours');
        return $ret;
    } 

    public function __toString()
    {
        return (string)parent::format(self::$Format);
    }

    public static function getWpTimezone() {
        $timezone = get_option( 'timezone_string' );
        if( empty( $timezone ) ) {
            $timezone = sprintf( 'UTC%+.4g', get_option( 'gmt_offset', 0 ) );
        }
        return $timezone;
    }
}
