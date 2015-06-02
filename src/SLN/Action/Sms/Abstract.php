<?php

abstract class SLN_Action_Sms_Abstract
{
    protected $plugin;

    public function __construct(SLN_Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    abstract public function send($to, $message);

    protected function getAccount(){
        return $this->plugin->getSettings()->get('sms_account');
    }

    protected function getPassword(){
        return $this->plugin->getSettings()->get('sms_password');
    }

    protected function getFrom(){
        return $this->plugin->getSettings()->get('sms_from');
    }

    protected function processTo($to){
        $prefix = $this->plugin->getSettings()->get('sms_prefix');
        //$prefix = str_replace('+','',$prefix);
        $to = str_replace(' ','',$to);
        return $prefix . $to;
    }
}