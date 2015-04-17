<?php
/**
 * @var SLN_Metabox_Helper $helper
 */
$helper->showNonce($postType);
?>

<div class="sln-bootstrap">

<div class="sln_booking-topbuttons">
    <div class="row">
        <div class="col-lg-5 col-md-6 col-sm-6">
            <h2><?php _e('Re-send email notification to ','sln') ?></h2>
            <div class="row">
            <div class="col-lg-7 col-md-8 col-sm-8"><input type="text" class="form-control" name="emailto"/></div>
            <div class="col-lg-4 col-md-4 col-sm-4">
                <button class="btn btn-success" type="submit" name="emailto_submit" value="submit">Invia</button>

            </div>
            </div>
        </div>

        <?php if ($plugin->getSettings()->get('confirmation') && $booking->getStatus() == SLN_Enum_BookingStatus::PENDING){ ?>
        <div class="col-lg-5 col-md-5 col-sm-6 sln_accept-refuse">
            <h2><?php _e('This booking waits for confirmation!','sln')?></h2>
            <div class="row">
            <div class="col-lg-5 col-md-6 col-sm-6">
               <button id="booking-refuse" class="btn btn-success" data-status="<?php echo SLN_Enum_BookingStatus::CONFIRMED ?>">
                <?php _e('Accept', 'sln') ?></button>
            </div>
            <div class="col-lg-5 col-md-6 col-sm-6"> <button id="booking-accept" class="btn btn-danger" data-status="<?php echo SLN_Enum_BookingStatus::CANCELED ?>">
                <?php _e('Refuse', 'sln') ?></button>
            </div>
            </div>
        </div>
        <?php } ?>
    </div>
    
</div>


    <div class="row">
        <div class="col-md-3 col-sm-6">
            <?php
            $helper->showFieldtext(
                $helper->getFieldName($postType, 'firstname'),
                __('Firstname', 'sln'),
                $booking->getFirstname()
            );
            ?>
        </div>
        <div class="col-md-3 col-sm-6">
            <?php
            $helper->showFieldtext(
                $helper->getFieldName($postType, 'lastname'),
                __('Lastname', 'sln'),
                $booking->getLastname()
            );
            ?>
        </div>
        <div class="col-md-3 col-sm-6">
            <?php
            $helper->showFieldtext(
                $helper->getFieldName($postType, 'email'),
                __('E-mail', 'sln'),
                $booking->getEmail()
            ); ?>
        </div>
        <div class="col-md-3 col-sm-6">

            <?php
            $helper->showFieldtext(
                $helper->getFieldName($postType, 'phone'),
                __('Phone', 'sln'),
                $booking->getPhone()
            );
            ?>
        </div>
    </div>
    <div class="row form-inline">
        <div class="col-md-6 col-sm-8">
            <div class="form-group sln_meta_field">
                <label><?php _e('Date', 'sln'); ?>
                    <?php SLN_Form::fieldDate($helper->getFieldName($postType, 'date'), $booking->getDate()); ?>
                    <?php SLN_Form::fieldTime($helper->getFieldName($postType, 'time'), $booking->getTime()); ?>
                </label>
            </div>
        </div>
        <div class="col-md-6 col-sm-4">
            <div class="form-group sln_meta_field ">
                <label><?php _e('Status', 'sln'); ?></label>
                <?php SLN_Form::fieldSelect(
                    $helper->getFieldName($postType, 'status'),
                    SLN_Enum_BookingStatus::toArray(),
                    $booking->getStatus(),
                    array('map' => true)
                ); ?>
            </div>
        </div>
    </div>
    <div class="sln-separator"></div>
    <div class="form-group sln_meta_field">
        <label><?php _e('Services', 'sln'); ?></label><br/>
        <?php foreach ($plugin->getServices() as $service) : ?>
            <div class="row">
                <div class="col-md-1 col-sm-1 col-xs-2">
            <span class="service-checkbox">
                <?php SLN_Form::fieldCheckbox(
                    $helper->getFieldName($postType, 'services[' . $service->getId() . ']'),
                    $booking->hasService($service)
                ) ?>
            </span>
                </div>
                <div class="col-md-8 col-sm-9 col-xs-10 sln_booking-service-info">
                    <label for="<?php echo SLN_Form::makeID('sln[services][' . $service->getId() . ']') ?>">
                        <strong class="service-name"><?php echo $service->getName(); ?></strong>
                        <span class="service-description"><?php echo $service->getContent() ?></span>
                        <span class="service-duration">Duration: <?php echo $service->getDuration()->format(
                                'H:i'
                            ) ?></span>
                    </label>
                </div>
                <div class="show--phone col-xs-2"></div>
                <div class="col-md-3 col-sm-2 col-xs-10 sln_booking-service-price">
                    <?php echo $plugin->format()->money($service->getPrice()) ?>
                </div>
            </div>
        <?php endforeach ?>
    </div>
    <div class="sln-separator"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group sln_meta_field ">
                <label><?php _e('Personal message', 'sln'); ?></label>
                <?php SLN_Form::fieldTextarea(
                    $helper->getFieldName($postType, 'note'),
                    $booking->getNote()
                ); ?>
            </div>
        </div>
    </div>
    <div class="sln-separator"></div>
    <div class="row">
        <div class="col-md-3 col-sm-4">
            <div class="form-group sln_meta_field ">
                <label><?php _e('Duration', 'sln'); ?></label>
                <?php SLN_Form::fieldTime(
                    $helper->getFieldName($postType, 'duration'),
                    $booking->getDuration(),
                    array('interval' => 10, 'maxItems' => 61)
                ); ?>
            </div>
        </div>
        <div class="col-md-3 col-sm-4">
            <?php
            $helper->showFieldtext(
                $helper->getFieldName($postType, 'amount'),
                __('Amount', 'sln') . ' (' . $settings->getCurrencySymbol() . ')',
                $booking->getAmount()
            );
            ?>
        </div>
        <div class="col-md-6 col-sm-4">
            <div class="form-group">
                <label for="">Transaction</label>

                <p><?php echo $booking->getTransactionId() ? $booking->getTransactionId() : __('n.a.', 'sln') ?></p>
            </div>
        </div>
    </div>
    <div class="sln-clear"></div>
</div>
