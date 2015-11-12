<?php
/**
 * @var SLN_Plugin                        $plugin
 * @var string                            $formAction
 * @var string                            $submitName
 * @var SLN_Shortcode_Salon_AttendantsStep $step
 */
$bb = $plugin->getBookingBuilder();
$attendants = $step->getAttendants();
?>
<h1><?php _e('Select your assistant','sln')?></h1>
<form id="salon-step-secondary" method="post" action="<?php echo $formAction ?>" role="form">
    <?php include "_attendants.php"; ?>
    <?php include "_form_actions.php" ?>
</form>
