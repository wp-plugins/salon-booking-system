<?php
/**
 * @var SLN_Plugin                        $plugin
 * @var string                            $formAction
 * @var string                            $submitName
 * @var SLN_Shortcode_Salon_ServicesStep $step
 */
$bb             = $plugin->getBookingBuilder();
$currencySymbol = $plugin->getSettings()->getCurrencySymbol();
$services = $step->getServices();
?>
<h2><?php _e('What do you need?','sln') ?></h2>
<form id="salon-step-services" method="post" action="<?php echo $formAction ?>" role="form">
    <?php include '_errors.php' ?>
    <?php include "_services.php"; ?>
    <?php include "_form_actions.php" ?>
</form>
