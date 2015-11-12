<?php

class SLN_Action_Ajax_Calendar extends SLN_Action_Ajax_Abstract
{
    private $from;
    private $to;
    public function execute()
    {
       $this->from = new SLN_DateTime(date("c", $_GET['from']/ 1000));
       $this->to = new SLN_DateTime(date("c", $_GET['to']/ 1000));
       $ret = array(
           'success' => 1,
           'result' => $this->getResults()
       );
       return $ret;
    }

    private function getResults()
    {
        $bookings = $this->buildBookings();
        $ret = array();
        foreach($bookings as $b){
            $ret[] = $this->wrapBooking($b);
        }
        return $ret;
    }

    private function wrapBooking($booking){
        return array(
	"id" => $booking->getId(),
	"title" => $this->getTitle($booking),
	"url" => get_edit_post_link($booking->getId()),
	"class" => "event-".SLN_Enum_BookingStatus::getColor($booking->getStatus()),
	"start" => $booking->getStartsAt()->format('U') * 1000,
	"end" => $booking->getEndsAt()->format('U') * 1000,
        "event_html" => $this->getEventHtml($booking)
	);
    }

    private function buildBookings()
    {
        $args  = array(
            'post_type'  => SLN_Plugin::POST_TYPE_BOOKING,
            'nopaging'   => true,
            'meta_query' => $this->getCriteria()
        );
        $query = new WP_Query($args);
        $ret   = array();
        foreach ($query->get_posts() as $p) {
            $ret[] = $this->plugin->createBooking($p);
        }
        wp_reset_query();
        wp_reset_postdata();

        return $ret;
    }

    private function getCriteria(){
        $from = $this->from->format('Y-m-d');
        $to = $this->to->format('Y-m-d');
        if($from == $to){
            $criteria =  array(
                array(
                    'key'     => '_sln_booking_date',
                    'value'   => $from,
                    'compare' => '=',
                ),
            );
        }else{
            $criteria = array(
                array(
                    'key'     => '_sln_booking_date',
                    'value'   => $from,
                    'compare' => '>=',
                ),
                array(
                    'key'     => '_sln_booking_date',
                    'value'   => $to,
                    'compare' => '<=',
                )
           );
        }
        return $criteria;
    }

    private function getTitle($booking){
        return $this->plugin->loadView('admin/_calendar_title', compact('booking'));
    }
    private function getEventHtml($booking){
        return $this->plugin->loadView('admin/_calendar_event', compact('booking'));
    }
}
