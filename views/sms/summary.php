<?php
/**
 * @var SLN_Plugin          $plugin
 * @var SLN_Wrapper_Booking $booking
 */
echo
    $booking->getFirstname() . ' ' . $booking->getLastname()
    . __(' has booked at ','sln') . $plugin->getSettings()->getSalonName() . __(' on ','sln')
    . ' ' . $plugin->format()->date($booking->getDate()) 
    . ' ' . $plugin->format()->time($booking->getTime())
    . ' - ' .$booking->getId();



