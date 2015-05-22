<?php

class SLN_Action_Ajax_CheckDate extends SLN_Action_Ajax_Abstract
{
    private $date;
    private $time;
    private $errors = array();

    public function execute()
    {
        if (!isset($this->date)) {
            $this->date = $_POST['sln']['date'];
            $this->time = $_POST['sln']['time'];
        }
        $this->checkDateTime();
        if ($errors = $this->getErrors()) {
            $ret = compact('errors');
        } else {
            $ret = array('success' => 1);
        }
        $ret['intervals'] = $this->plugin->getIntervals($this->getDateTime())->toArray();

        return $ret;
    }

    public function checkDateTime()
    {
        $plugin = $this->plugin;
        $date   = $this->getDateTime();
//        $this->addError($plugin->format()->datetime($date));
        $ah   = $plugin->getAvailabilityHelper();
        $hb   = $ah->getHoursBeforeHelper();
        $from = $hb->getFromDate();
        $to   = $hb->getToDate();
        if (!$hb->isValidFrom($date)) {
            $txt = $plugin->format()->datetime($from);
            $this->addError(sprintf(__('the date is too old, the minimum allowed is %s', 'sln'), $txt));
        } elseif (!$hb->isValidTo($date)) {
            $txt = $plugin->format()->datetime($to);
            $this->addError(sprintf(__('the date is too far, the maximum allowed is %s', 'sln'), $txt));
        } elseif (!$ah->getItems()->isValidDatetime($date)) {
            $txt = $plugin->format()->datetime($date);
            $this->addError(sprintf(__('we are not available at %s', 'sln'), $txt));
        } else {
            $ah->setDate($date);
            if (!$ah->isValidDate($date)) {
                $this->addError(
                    __(
                        'you can\'t book in this day because there aren\'t free places, please choose a different day',
                        'sln'
                    )
                );
            } elseif (!$ah->isValidTime($date)) {
                $this->addError(
                    __(
                        'you can\'t book in this hour because there aren\'t free places, please choose a different hour',
                        'sln'
                    )
                );
            }
        }
    }

    protected function addError($err)
    {
        $this->errors[] = $err;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param mixed $date
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @param mixed $time
     * @return $this
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    protected function getDateTime()
    {
        $date = $this->date;
        $time = $this->time;
        $ret = new DateTime(
            SLN_Func::filter($date, 'date') . ' ' . SLN_Func::filter($time, 'time')
        );
        return $ret;
    }
}
