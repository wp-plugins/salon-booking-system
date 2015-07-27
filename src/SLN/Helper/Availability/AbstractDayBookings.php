<?php


abstract class SLN_Helper_Availability_AbstractDayBookings
{
    private $bookings;
    private $date;

    /** @return SLN_Wrapper_Booking[] :q*/
    abstract public function getBookingsByHour($hour, $minutes = null);


    public function __construct(DateTime $date)
    {
        $this->date = $date;
        $this->bookings = $this->buildBookings($date);
    }

    private function buildBookings()
    {
        $args = array(
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
        $ret = array();
        foreach ($query->get_posts() as $p) {
            $ret[] = SLN_Plugin::getInstance()->createBooking($p);
        }
        wp_reset_query();
        wp_reset_postdata();

        SLN_Plugin::addLog(__CLASS__.' - buildBookings('.$this->date->format('Y-m-d').')');
        foreach($ret as $b)
            SLN_Plugin::addLog(' - '.$b->getId());
        return $ret;
    }

    public function countBookingsByDay()
    {
        return count($this->bookings);
    }

    public function countBookingsByHour($hour = null, $minutes = null)
    {
        return count($this->getBookingsByHour($hour, $minutes));
    }

    public function countAttendantsByHour($hour = null, $minutes = null)
    {
        $ret = array();
        foreach ($this->getBookingsByHour($hour) as $b) {
            $id = $b->getAttendantId();
            $ret[$id] = 1 + (isset($ret[$id]) ? $ret[$id] : 0);
        }

        return $ret;
    }

    public function countServicesByHour($hour = null, $minutes = null)
    {
        SLN_Plugin::addLog(__CLASS__.' - count services by hour('.$hour.')');
        $ret = array();
        foreach ($this->getBookingsByHour($hour, $minutes) as $b) {
            foreach ($b->getServicesIds() as $id) {
                $ret[$id] = 1 + (isset($ret[$id]) ? $ret[$id] : 0);
            }
        }
        SLN_Plugin::addLog(print_r($ret, true)); 
        return $ret;
    }

    /**
     * @return DateTime
     */
    protected function getDate()
    {
        return $this->date;
    }

    /**
     * @return SLN_Wrapper_Booking[]
     */
    protected function getBookings()
    {
        return $this->bookings;
    }
}
