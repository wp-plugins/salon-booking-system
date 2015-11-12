<?php

class SLN_Wrapper_Booking_Builder
{
    protected $plugin;
    protected $data;
    protected $lastId;

    public function __construct(SLN_Plugin $plugin)
    {
        if (session_id() == '') {
            session_start();
        }
        $this->plugin = $plugin;
        $this->data   = isset($_SESSION[__CLASS__]) ? $_SESSION[__CLASS__] : $this->getEmptyValue();
        $this->lastId = isset($_SESSION[__CLASS__ . 'last_id']) ? $_SESSION[__CLASS__ . 'last_id'] : null;
    }

    public function save()
    {
        $_SESSION[__CLASS__]             = $this->data;
        $_SESSION[__CLASS__ . 'last_id'] = $this->lastId;
    }

    public function clear($id = null)
    {
        $this->data   = $this->getEmptyValue();
        $this->lastId = $id;
        $this->save();
    }

    /**
     * @return $this
     */
    public function removeLastId()
    {
        unset($_SESSION[__CLASS__ . 'last_id']);
        $this->lastId = null;

        return $this;
    }

    /**
     * @return SLN_Wrapper_Booking
     */
    public function getLastBooking()
    {
        if ($this->lastId) {
            return $this->plugin->createBooking(get_post($this->lastId));
        }
    }

    protected function getEmptyValue()
    {
        $from = $this->plugin->getSettings()->getHoursBeforeFrom();
        $d = new SLN_DateTime(date('Y-m-d H:i:00'));
        $d->modify($from);
        $tmp = $d->format('i');
        $i             = SLN_Plugin::getInstance()->getSettings()->getInterval();
        $diff = $tmp % $i;
        if($diff > 0)
            $d->modify('+'.( $i - $diff).' minutes');
        return array(
            'date'     => $d->format('Y-m-d'),
            'time'     => $d->format('H:i'),
            'services' => array(),
        );
    }

    public function get($k)
    {
        return isset($this->data[$k]) ? $this->data[$k] : null;
    }

    public function set($key, $val)
    {
        if (empty($val)) {
            unset($this->data[$key]);
        } else {
            $this->data[$key] = $val;
        }
    }

    public function getDate()
    {
        return $this->data['date'];
    }

    public function getTime()
    {
        return $this->data['time'];
    }

    public function getDateTime()
    {
        $ret =  new SLN_DateTime($this->getDate() . ' ' . $this->getTime());
        return $ret;
    }

    public function setDate($date)
    {
        $this->data['date'] = $date;

        return $this;
    }

    public function setTime($time)
    {
        $this->data['time'] = $time;

        return $this;
    }

    public function setAttendant(SLN_Wrapper_Attendant $attendant)
    {
        $this->data['attendant'] = $attendant->getId();
    }
    public function hasAttendant(SLN_Wrapper_Attendant $attendant)
    {
        
        return isset($this->data['attendant']) && $attendant->getId() == $this->data['attendant'];
    }



    public function hasService(SLN_Wrapper_Service $service)
    {
        return in_array($service->getId(), $this->data['services']);
    }
    /**
     * @return SLN_Wrapper_Attendant
     */
    public function getAttendant()
    {
        if(isset($this->data['attendant']) && $id = $this->data['attendant']){
            return $this->plugin->createAttendant($id);
        }
    }
    
    public function addService(SLN_Wrapper_Service $service)
    {
        $this->data['services'][] = $service->getId();
    }

    public function removeService(SLN_Wrapper_Service $service)
    {
        $k = array_search($service->getId(), $this->data['services']);
        if ($k !== false) {
            unset($this->data['services'][$k]);
        }
    }

    /**
     * @return SLN_Wrapper_Service[]
     */
    public function getServices()
    {
        $ids = $this->data['services'];
        $ret = array();
        foreach ($this->plugin->getServices() as $service) {
            if (in_array($service->getId(), $ids)) {
                $ret[$service->getId()] = $service;
            }
        }

        return $ret;
    }
    
    public function getTotal()
    {
        $ret = 0;
        foreach ($this->getServices() as $s) {
            $ret = $ret + SLN_Func::filter($s->getPrice(), 'float');
        }

        return $ret;
    }

    public function create()
    {
        update_option(SLN_Plugin::F, intval(get_option(SLN_Plugin::F))+1);
        $settings             = $this->plugin->getSettings();
        $datetime             = $this->plugin->format()->datetime($this->getDateTime());
        $name                 = $this->get('firstname') . ' ' . $this->get('lastname');
        $status               = $settings->get('confirmation') ?
            SLN_Enum_BookingStatus::PENDING
            : ($settings->get('pay_enabled') ?
                SLN_Enum_BookingStatus::PENDING
                : SLN_Enum_BookingStatus::PAY_LATER);
        $id                   = wp_insert_post(
            array(
                'post_type'   => SLN_Plugin::POST_TYPE_BOOKING,
                'post_title'  => $name . ' - ' . $datetime,
            )
        );
        $deposit              = $this->plugin->getSettings()->get('pay_deposit');
        $this->data['amount'] = $this->getTotal();
        if($deposit > 0) {
            $this->data['deposit'] = ($this->data['amount'] / 100) * $deposit;
        }
        foreach ($this->data as $k => $v) {
            add_post_meta($id, '_' . SLN_Plugin::POST_TYPE_BOOKING . '_' . $k, $v, true);
        }
        $this->clear($id);
        $this->getLastBooking()->setStatus($status);
    }

}
