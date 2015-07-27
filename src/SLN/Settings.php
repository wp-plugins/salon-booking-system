<?php

class SLN_Settings
{
    const KEY = 'salon_settings';
    private $settings;

    public function __construct()
    {
        $this->settings = get_option(self::KEY);
    }
    
    public function get($k)
    {
        return isset($this->settings[$k]) ? $this->settings[$k] : null;
    }

    public function set($key, $val)
    {
        if (empty($val)) {
            unset($this->settings[$key]);
        } else {
            $this->settings[$key] = $val;
        }
    }

    public function save()
    {
        update_option(self::KEY, $this->settings);

        return $this;
    }

    public function clear()
    {
        delete_option(self::KEY, $this->settings);
    }

    public function getCurrency()
    {
        return empty($this->settings['pay_currency']) ? 'USD' : $this->settings['pay_currency'];
    }

    public function getCurrencySymbol()
    {
        return SLN_Currency::getSymbol($this->getCurrency());
    }

    public function getInterval()
    {
        return isset($this->settings['interval']) ? $this->settings['interval'] : 15;
    }

    public function getNoticesDisabled()
    {
        return isset($this->settings['notices_disabled']) ? $this->settings['notices_disabled'] : false;
    }

    public function setNoticesDisabled($val)
    {
        $this->settings['notices_disabled'] = $val;

        return $this;
    }

    public function isPaypalTest()
    {
        return $this->settings['pay_paypal_test'] ? true : false;
    }

    public function getPaypalEmail()
    {
        return $this->settings['pay_paypal_email'];
    }

    public function getThankyouPageId()
    {
        return $this->settings['thankyou'];
    }

    public function isDisabled()
    {
        return $this->get('disabled') ? true : false;
    }

    public function getDisabledMessage()
    {
        return nl2br(htmlentities($this->get('disabled_message')));
    }

    public function isAjaxEnabled(){
        return $this->get('ajax_enabled') ? true: false;
    }
}
