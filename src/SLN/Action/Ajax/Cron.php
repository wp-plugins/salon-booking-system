<?php

class SLN_Action_Ajax_Cron extends SLN_Action_Ajax_Abstract
{
    private $date;
    private $time;
    private $errors = array();

    public function execute()
    {
        $remind = $this->plugin->getSettings()->get('sms_remind');
        if($remind){
            $this->dispatchAdvice();
        }
        return array('status' => 'OK');
    }
    private function dispatchAdvice(){
        $plugin = $this->plugin;
        $interval = $plugin->getSettings()->get('sms_remind_interval');
        $date = new DateTime();
        $date->modify($interval);
        $now = new DateTime(); 
        $smsProvider = SLN_Enum_SmsProvider::getService(
            $plugin->getSettings()->get('sms_provider'),
            $this->plugin
        );
        $args = array(
            'post_type'  => SLN_Plugin::POST_TYPE_BOOKING,
            'nopaging'   => true,
            'meta_query' => array(
                array(
                    'key'     => '_sln_booking_date',
                    'value'   => $date->format('Y-m-d'),
                    'compare' => '=',
                )
            )
        );
        $query = new WP_Query($args);
        $ret = array();
        foreach ($query->get_posts() as $p) {
            $booking = $plugin->createBooking($p);
            $d = $booking->getStartsAt();
            if($d >= $now && $d <= $date){
                $smsProvider->send($booking->getPhone(), $plugin->loadView('sms/remind', compact('booking'))); 
            }
        }
        wp_reset_query();
        wp_reset_postdata();


    }
}
