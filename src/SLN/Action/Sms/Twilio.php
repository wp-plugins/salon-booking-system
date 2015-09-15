<?php

class SLN_Action_Sms_Twilio extends SLN_Action_Sms_Abstract
{
    const API_URL = 'https://web.smscom.se/sendsms/sendsms.asmx?wsdl';

    public function send($to, $message)
    {
        require_once '_twilio.php';

        $client = new Services_Twilio($this->getAccount(), $this->getPassword());
//        $client->account->messages->sendMessage(
//            $this->getFrom(),
//            $this->processTo($to),
//            $message
//        );
        $client->account->sms_messages->create(
            $this->getFrom(),
            $this->processTo($to),
            $message
        );
    }
}