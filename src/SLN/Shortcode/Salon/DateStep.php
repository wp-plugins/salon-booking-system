<?php

class SLN_Shortcode_Salon_DateStep extends SLN_Shortcode_Salon_Step
{

    protected function dispatchForm()
    {
        $bb     = $this->getPlugin()->getBookingBuilder();
        $values = $_POST['sln'];
        $date   = SLN_Func::filter($values['date'], 'date');
        $time   = SLN_Func::filter($values['time'], 'time');
        $bb
            ->removeLastID()
            ->setDate($date)
            ->setTime($time);
        $obj = new SLN_Action_Ajax_CheckDate($this->getPlugin());
        $obj
            ->setDate($date)
            ->setTime($time)
            ->execute();
        foreach ($obj->getErrors() as $err) {
            $this->addError($err);
        }
        if (!$this->getErrors()) {
            $bb->save();

            return true;
        }
    }


}