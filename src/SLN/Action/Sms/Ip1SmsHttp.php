<?php

class SLN_Action_Sms_Ip1SmsHttp extends SLN_Action_Sms_Fake
{
    const API_URL = 'https://web.smscom.se/sendsms.aspx';

    public function send($to, $message)
    {
        $settings = $this->plugin->getSettings();
        $prefix = str_replace('+','',$settings->get('sms_prefix'));
        $to = str_replace(' ','',$to);
        // Set parameters
        $data = http_build_query(
            array(
                'acc' => $this->plugin->getSettings()->get('sms_account'),
                'pass' => $this->plugin->getSettings()->get('sms_password'),
                'msg' => $message,
                'from' => $this->plugin->getSettings()->get('sms_from'),
                'to' => $to,
                'prio' => 1,
                'type' => ''
            )
        );
        $opts = array('http' =>
            array(
                'method' => 'GET',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $data
            )
        );
        $context = stream_context_create($opts);
        $ret = file_get_contents(self::API_URL . '?' . $data, false, $context);
    }
}
