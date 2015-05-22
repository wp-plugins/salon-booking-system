<?php

class SLN_Action_Sms_Fake extends SLN_Action_Sms_Abstract
{
    public function send($to, $message)
    {
        $message = print_r(array($to, $message),true);
        wp_mail(get_option('admin_email'), 'sms verification', $message);
    }
}