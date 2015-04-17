<?php
/**
 * @var SLN_Plugin          $plugin
 * @var SLN_Wrapper_Booking $booking
 */
$data['to']      = get_option('admin_email');
$data['subject'] = 'New booking for '
     . $plugin->format()->date($booking->getDate())
     . ' - ' . $plugin->format()->time($booking->getTime());
$forAdmin = true;
include dirname(__FILE__) . '/_header.php';
include dirname(__FILE__) . '/_summary_content.php';
include dirname(__FILE__) . '/_footer.php';
