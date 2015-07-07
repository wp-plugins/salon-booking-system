<?php
/**
 * @var SLN_Plugin                $plugin
 * @var string                    $formAction
 * @var string                    $submitName
 * @var SLN_Shortcode_Salon_Step $step
 */
$bb             = $plugin->getBookingBuilder();
$currencySymbol = $plugin->getSettings()->getCurrencySymbol();
$datetime       = $bb->getDateTime();
$confirmation = $plugin->getSettings()->get('confirmation'); 
$showPrices = ($plugin->getSettings()->get('hide_prices') != '1')? true : false;
?>
<h2><?php _e('Booking summary', 'sln') ?></h2>
<form method="post" action="<?php echo $formAction ?>" role="form"  id="salon-step-summary">
    <p class="dear"><?php _e('Dear', 'sln') ?>
        <strong><?php echo esc_attr($bb->get('firstname')) . ' ' . esc_attr($bb->get('lastname')); ?></strong>
        <br/>
        <?php _e('Here the details of your booking:', 'sln') ?>
    </p>

    <div class="row summ-row">
        <div class="col-md-5"><span class="label"><?php _e('Date and time booked', 'sln') ?></span></div>
        <div class="col-md-7"><p class="date"><strong><?php echo $plugin->format()->date($datetime); ?></strong><br/>
            <span class="time"><?php echo $plugin->format()->time($datetime) ?></span></p>
        </div>
    </div>

    <?php if($attendant = $bb->getAttendant()) :  ?>
    <div class="row summ-row">
        <div class="col-md-5"><span class="label"><?php _e('Assistant', 'sln') ?></span></div>
        <div class="col-md-7">
            <span class="attendant-label"><?php echo $attendant->getName(); ?></span></li>
        </div>
    </div>
    <?php endif ?>
    <div class="row summ-row">
        <div class="col-md-5"><span class="label"><?php _e('Services booked', 'sln') ?></span></div>
        <div class="col-md-7">
            <ul class="list-unstyled">
                <?php foreach ($bb->getServices() as $service): ?>
                    <li> <span class="service-label"><?php echo $service->getName(); ?>
                    <?php if($showPrices){?>
					<span class="service-price"><?php echo $plugin->format()->money($service->getPrice()) ?>
					<?php } ?>
					</li>
                <?php endforeach ?>
                <?php if($showPrices){?>
				<li><span class="total-label"><?php _e('Total amount', 'sln') ?></span>
                <span class="total-price"><?php echo $plugin->format()->money(
                        $plugin->getBookingBuilder()->getTotal()
                    ) ?></span></li>
				<?php } ?>
            </ul>
        </div>
    </div>

    <br/>
    <div class="row">
    <div class="form-group">
        <label><?php _e('Do you have any message for us?', 'sln') ?></label>
        <?php SLN_Form::fieldTextarea(
            'sln[note]',
            $bb->get('note'),
            array('attrs' => array('placeholder' => __('Leave a message', 'sln')))
        ); ?>
    </div>
    </div>
    <?php $nextLabel = __('Finalise', 'sln');
    include "_form_actions.php" ?>
</form>
