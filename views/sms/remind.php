<?php
/**
 * @var SLN_Plugin          $plugin
 * @var SLN_Wrapper_Booking $booking
 */
echo __('Your booking is coming'). $booking->getId()
    . ' ' . $plugin->format()->date($booking->getDate()) 
    . ' ' . $plugin->format()->time($booking->getTime())
    . ' ' . $booking->getFirstname() . ' ' . $booking->getLastname();
