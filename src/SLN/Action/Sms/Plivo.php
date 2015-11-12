<?php

class SLN_Action_Sms_Plivo extends SLN_Action_Sms_Abstract
{

    public function send($to, $message)
    {
        require_once '_plivo.php';

        $p = new RestAPI($this->getAccount(), $this->getPassword());
        $to = $this->processTo($to);
        $params = array(
            'src' => str_replace('+','',$this->getFrom()),
            'dst' => $to,
            'text' => $message,
            'type' => 'sms',
        );
        $response = @$p->send_message($params);
        $tmp = array_values($response);
        if (array_shift($tmp) != "202") {
            throw new Exception('Plivo: Please ensure that From number is a valid and sms feature enabled Plivo DID number');
        }
    }
/*
    protected function processTo($to){
        return str_replace('+','',parent::processTo($to));
    }
*/
}
