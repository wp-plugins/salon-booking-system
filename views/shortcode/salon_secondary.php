<?php
/**
 * @var SLN_Plugin                        $plugin
 * @var string                            $formAction
 * @var string                            $submitName
 * @var SLN_Shortcode_Salon_ServicesStep $step
 */
$bb = $plugin->getBookingBuilder();
$services = $step->getServices();
?>
<h1><?php _e('Something more?','sln')?></h1>
<form id="salon-step-secondary" method="post" action="<?php echo $formAction ?>" role="form">
    <?php include "_services.php"; ?>
    <?php include "_form_actions.php" ?>
</form>
