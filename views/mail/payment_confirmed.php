<?php
/**
 * @var SLN_Plugin                $plugin
 * @var SLN_Wrapper_Booking       $booking
 */
$data['to'] = $booking->getEmail();
$data['subject'] = 'Payment for booking #'.$booking->getId().' has been confirmed';
include dirname(__FILE__).'/_header.php';
?>
<p ><?php _e('Dear', 'sln') ?>
    <strong><?php echo esc_attr($booking->getFirstname()) . ' ' . esc_attr($booking->getLastname()); ?></strong>
    <br/>
    <?php _e('We received the payment for your booking.', 'sln') ?>
</p>
<?php
include dirname(__FILE__).'/_footer.php';
?>
