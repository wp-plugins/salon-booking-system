<?php

class SLN_Helper_AvailabilityDayBookings
{
    private $bookings;
    private $date;

    public function __construct(DateTime $date)
    {
        $this->date     = $date;
        $this->bookings = $this->buildBookings($date);
    }

    private function buildBookings()
    {
        $args  = array(
            'post_type'  => SLN_Plugin::POST_TYPE_BOOKING,
            'nopaging'   => true,
            'meta_query' => array(
                array(
                    'key'     => '_sln_booking_date',
                    'value'   => $this->date->format('Y-m-d'),
                    'compare' => '=',
                )
            )
        );
        $query = new WP_Query($args);
        $ret   = array();
        foreach ($query->get_posts() as $p) {
            $ret[] = SLN_Plugin::getInstance()->createBooking($p);
        }
        wp_reset_query();
        wp_reset_postdata();

        return $ret;
    }

    public function countBookingsByDay()
    {
        return count($this->bookings);
    }

    public function countBookingsByHour($hour = null)
    {
        return count($this->getBookingsByHour($hour));
    }

    /**
     * @return SLN_Wrapper_Booking[]
     */
    public function getBookingsByHour($hour)
    {
        if (!isset($hour)) {
            $hour = $this->date->format('H');
        }
        $ret = array();
        foreach ($this->bookings as $b) {
            $t = $b->getTime();
            if ($t instanceof DateTime) {
                $t = $t->format('H');
            } else {
                $t = explode(':', $b->getTime());
                $t = $t[0];
            }
            if ($t == $hour) {
                $ret[] = $b;
            }
        }

        return $ret;
    }

    public function countServicesByHour($hour = null)
    {
        $ret = array();
        foreach ($this->getBookingsByHour($hour) as $b) {
            foreach ($b->getServicesIds() as $id) {
                if (isset($ret[$id])) {
                    $ret[$id]++;
                } else {
                    $ret[$id] = 1;
                }
            }
        }

        return $ret;
    }
}
