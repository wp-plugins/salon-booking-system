<?php

class SLN_Action_Sms_Ip1SmsWebservice extends SLN_Action_Sms_Fake
{
    const API_URL = 'https://web.smscom.se/sendsms/sendsms.asmx?wsdl';
    public function send($to, $message)
    {
        $settings = $this->plugin->getSettings();
        $prefix = str_replace('+','',$settings->get('sms_prefix'));
        $to = str_replace(' ','',$to);
        $to = $prefix + $to;
        $client = new SoapClient(self::API_URL);
        $ret = $client->sms(array(
            'konto' => $this->plugin->getSettings()->get('sms_account'),
            'passwd' => $this->plugin->getSettings()->get('sms_password'),
            'till' => $to,
            'from' => $this->plugin->getSettings()->get('sms_from'),
            'meddelande' => $message,
            'prio' => 1
        ));
        $ret = print_r($ret, true);
//        trigger_error('sms sent to '.$to.' with status:'.$ret, E_USER_NOTICE);
    }
}