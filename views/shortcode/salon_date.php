<?php
/**
 * @var SLN_Plugin                   $plugin
 * @var string                       $formAction
 * @var string                       $submitName
 * @var SLN_Shortcode_Salon_DateStep $step
 */
function salon_date_hoursbefore($hoursBefore)
{
    if ($hoursBefore->from && $hoursBefore->to) : ?>
        <em><?php echo sprintf(
                __('you can book from %s up to %s in advance', 'sln'),
                $hoursBefore->from,
                $hoursBefore->to
            ) ?></em>
    <?php elseif ($hoursBefore->from): ?>
        <em><?php echo sprintf(__('you can book %s in advance', 'sln'), $hoursBefore->from) ?></em>
    <?php
    elseif ($hoursBefore->to) : ?>
        <em><?php echo sprintf(__('you can book up to %s in advance', 'sln'), $hoursBefore->to) ?></em>
    <?php endif;
}

if ($plugin->getSettings()->isDisabled()):
    $message =  $plugin->getSettings()->getDisabledMessage(); 
    ?>
    <div class="alert alert-danger">
        <p><?php echo empty($message) ? __('Booking is disabled', 'sln') : $message ?></p>
    </div>
<?php
else:
    $bb        = $plugin->getBookingBuilder();
    $intervals = $plugin->getIntervals($bb->getDateTime());
    $date      = $intervals->getSuggestedDate();
    ?>
    <h2><?php _e('When do you want to come?', 'sln') ?>
        <?php salon_date_hoursbefore($plugin->getAvailabilityHelper()->getHoursBeforeString()) ?>
    </h2>
    <form method="post" action="<?php echo $formAction ?>" id="salon-step-date" 
          data-intervals="<?php echo esc_attr(json_encode($intervals->toArray()));?>">
        <?php include '_errors.php' ?>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="<?php echo SLN_Form::makeID('sln[date][day]') ?>"><?php _e(
                            'select a day',
                            'sln'
                        ) ?></label>
                    <?php SLN_Form::fieldJSDate('sln[date]', $date) ?>
                </div>
            </div>
        </div>
        <div class="row">
           <div class="col-md-12">
                <div class="form-group">
                    <label for="<?php echo SLN_Form::makeID('sln[date][time]') ?>"><?php _e(
                            'select an hour',
                            'sln'
                        ) ?></label>
                    <?php SLN_Form::fieldJSTime('sln[time]', $date, array('interval' =>  $plugin->getSettings()->get('interval') )) ?>
                </div>
            </div>
        </div>
        <?php include "_form_actions.php" ?>
    </form>
<?php endif ?>
