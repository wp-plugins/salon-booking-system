<?php

class SLN_Action_Ajax_CheckServices extends SLN_Action_Ajax_Abstract
{
    private $date;
    private $time;
    private $errors = array();

    public function execute()
    {
        if($timezone = get_option('timezone_string'))
            date_default_timezone_set($timezone);

        if (!isset($this->date)) {
            if(isset($_POST['sln'])){
                $this->date = $_POST['sln']['date'];
                $this->time = $_POST['sln']['time'];
            }
            if(isset($_POST['_sln_booking_date'])) {
                $this->date = $_POST['_sln_booking_date'];
                $this->time = $_POST['_sln_booking_time'];
            }
        }

        $ret = array(
            'success' => 1,
            'services' => $this->getServices()
        );
        
        return $ret;
    }

    protected function getServices()
    {

        $plugin = $this->plugin;
        $date   = $this->getDateTime();
        $ah   = $plugin->getAvailabilityHelper();
        $ah->setDate($date);
        $ret = array();
        foreach($plugin->getServices() as $s){
            $ret[$s->getId()] = $ah->validateService($s);
        }
        return $ret;
    }

    protected function getDateTime()
    {
        $date = $this->date;
        $time = $this->time;
        $ret = new SLN_DateTime(
            SLN_Func::filter($date, 'date') . ' ' . SLN_Func::filter($time, 'time'.':00')
        );
        return $ret;
    }
}
