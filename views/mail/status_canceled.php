<?php
/**
 * @var SLN_Plugin                $plugin
 * @var SLN_Wrapper_Booking       $booking
 */
if($data['to']){
    $data['to'] = $booking->getEmail();
}
if ($plugin->getSettings()->get('attendant_email')
    && ($attendant = $booking->getAttendant())
    && ($email = $attendant->getEmail())
) {
    $data['to'] = array($data['to'], $email);
}

$data['subject'] = __('Booking Canceled','sln')
    . ' ' . $plugin->format()->date($booking->getDate()) 
    . ' - ' . $plugin->format()->time($booking->getTime());

include dirname(__FILE__).'/_header.php';
include dirname(__FILE__).'/_summary_content.php';
include dirname(__FILE__).'/_footer.php';