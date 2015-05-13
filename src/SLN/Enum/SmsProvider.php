<?php

class SLN_Enum_SmsProvider
{

    private static $labels = array(
        'fake' => 'test (sms code is sent by mail)',
        'ip1smshttp' => 'ip1sms http',
        'ip1smswebservice' => 'ip1sms webservice'
    );

    private static $classes = array(
        'fake' => 'SLN_Action_Sms_Fake',
        'ip1smshttp' => 'SLN_Action_Sms_Ip1SmsHttp',
        'ip1smswebservice' => 'SLN_Action_Sms_Ip1SmsWebservice',
        'twilio' => 'SLN_Action_Sms_Twilio',
        'plivo' => 'SLN_Action_Sms_Plivo',
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
     * @param SLN_Plugin $plugin
     * @return SLN_Action_Sms_Abstract
     * @throws Exception
     */
    public static function getService($key, SLN_Plugin $plugin)
    {
        $name = self::getServiceName($key);
        return new $name($plugin);
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
            'fake' => __('test (sms code is sent by mail to the admin)', 'sln'),
//            'ip1smshttp' => 'ip1sms http',
            'ip1smswebservice' => 'ip1sms',
            'twilio' => 'Twilio',
            'plivo' => 'Plivo'
        );
    }
}

SLN_Enum_SmsProvider::init();