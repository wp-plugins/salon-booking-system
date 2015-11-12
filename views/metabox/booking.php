<?php
/**
 * @var SLN_Metabox_Helper $helper
 */
$helper->showNonce($postType);
?>
<?php if(isset($_SESSION['_sln_booking_user_errors'])): ?>
    <div class="error">
    <?php foreach($_SESSION['_sln_booking_user_errors'] as $error): ?>
        <p><?php echo $error ?></p>
    <?php endforeach ?>
    </div>
    <?php unset($_SESSION['_sln_booking_user_errors']); ?>
<?php endif ?>

<div class="sln-bootstrap">
    <?php
    $intervals = $plugin->getIntervals($booking->getDate());
    $date = $intervals->getSuggestedDate();
    ?>
<span id="salon-step-date"
      data-intervals="<?php echo esc_attr(json_encode($intervals->toArray())); ?>"
      data-isnew="<?php echo $booking->isNew() ? 1 : 0 ?>"
      data-deposit="<?php echo $settings->get('pay_deposit') ?>">
    <div class="row form-inline">
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="<?php echo SLN_Form::makeID($helper->getFieldName($postType, 'date')) ?>"><?php _e(
                        'Select a day',
                        'sln'
                    ) ?></label>
                <?php SLN_Form::fieldJSDate($helper->getFieldName($postType, 'date'), $booking->getDate()) ?>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <label for="<?php echo SLN_Form::makeID($helper->getFieldName($postType, 'time')) ?>"><?php _e(
                        'Select an hour',
                        'sln'
                    ) ?></label>
                <?php SLN_Form::fieldJSTime(
                    $helper->getFieldName($postType, 'time'),
                    $booking->getTime(),
                    array('interval' => $plugin->getSettings()->get('interval'))
                ) ?>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="form-group sln_meta_field sln-select-wrapper">
                <label><?php _e('Status', 'sln'); ?></label>
                <?php SLN_Form::fieldSelect(
                    $helper->getFieldName($postType, 'status'),
                    SLN_Enum_BookingStatus::toArray(),
                    $booking->getStatus(),
                    array('map' => true)
                ); ?>
            </div>
        </div>
        <div class="col-md-3 col-sm-6" id="sln-notifications"  data-valid-message="<?php _e('OK! the date and time slot you selected is available','sln'); ?>"></div>
    </div>
</span>

    <div class="sln_booking-topbuttons">
        <div class="row">
            <?php if ($plugin->getSettings()->get('confirmation') && $booking->getStatus(
                ) == SLN_Enum_BookingStatus::PENDING
            ) { ?>
                <div class="col-lg-5 col-md-5 col-sm-6 sln_accept-refuse">
                    <h2><?php _e('This booking waits for confirmation!', 'sln') ?></h2>

                    <div class="row">
                        <div class="col-lg-5 col-md-6 col-sm-6">
                            <button id="booking-refuse" class="btn btn-success"
                                    data-status="<?php echo SLN_Enum_BookingStatus::CONFIRMED ?>">
                                <?php _e('Accept', 'sln') ?></button>
                        </div>
                        <div class="col-lg-5 col-md-6 col-sm-6">
                            <button id="booking-accept" class="btn btn-danger"
                                    data-status="<?php echo SLN_Enum_BookingStatus::CANCELED ?>">
                                <?php _e('Refuse', 'sln') ?></button>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

<div class="row">
        <div class="col-md-12"><label for="sln-update-user-field"><?php _e('Search for existing users', 'sln') ?></label></div>
        <div class="col-md-6 col-sm-6">
            <select id="sln-update-user-field"
                 data-nomatches="<?php _e('no users found','sln')?>"
                 data-placeholder="<?php _e('Start typing the name or email')?>"
                 class="form-control">
            </select>
        </div>
        <div class="col-md-6 col-sm-6" id="sln-update-user-message">
        </div>
        </div>
        <div class="clearfix"></div>
<div class="sln-separator"></div>
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <?php
            $helper->showFieldText(
                $helper->getFieldName($postType, 'firstname'),
                __('Firstname', 'sln'),
                $booking->getFirstname()
            );
            ?>
        </div>
        <div class="col-md-3 col-sm-6">
            <?php
            $helper->showFieldText(
                $helper->getFieldName($postType, 'lastname'),
                __('Lastname', 'sln'),
                $booking->getLastname()
            );
            ?>
        </div>
        <div class="col-md-3 col-sm-6">
            <?php
            $helper->showFieldText(
                $helper->getFieldName($postType, 'email'),
                __('E-mail', 'sln'),
                $booking->getEmail()
            ); ?>
        </div>
        <div class="col-md-3 col-sm-6">
            <?php
            $helper->showFieldText(
                $helper->getFieldName($postType, 'phone'),
                __('Phone', 'sln'),
                $booking->getPhone()
            );
            ?>
        </div>
        <div class="col-md-6 col-sm-12">
            <?php
            $helper->showFieldTextArea(
                $helper->getFieldName($postType, 'address'),
                __('Address', 'sln'),
                $booking->getAddress()
            );
            ?>
        </div>
        <div class="col-md-6 col-sm-12">
            <label><input type="checkbox" name="_sln_booking_createuser" <?php if($booking->isNew()){ ?>checked="checked"<?php } ?>/><?php _e('Create a new user') ?></label>
        </div>
    </div>

    <div class="sln-separator"></div>
    <div class="form-group sln_meta_field row">
        <div class="col-xs-12 col-sm-6 col-md-6 sln-select-wrapper">
            <h3><?php _e('Attendant', 'sln'); ?></h3>
            <select class="sln-select" name="_sln_booking_attendant" id="_sln_booking_attendant">
                <?php foreach ($plugin->getAttendants() as $attendant) : ?>
                    <option data-id="<?php echo SLN_Form::makeID('sln[attendant]['.$attendant->getId().']') ?>"
                            value="<?php echo $attendant->getId();?>"
                        <?php echo $booking->hasAttendant($attendant) ? 'selected="selected"' : '' ?>
                        ><strong class="service-name"><?php echo $attendant->getName(); ?></option>
                <?php endforeach ?>
            </select>
        </div>
    </div>
    <div class="sln-separator"></div>
    <div class="form-group sln_meta_field row">
        <div class="col-xs-12 col-sm-6 col-md-6 sln-select-wrapper">
            <h3><?php _e('Services', 'sln'); ?></h3>
            <select class="sln-select" multiple="multiple" data-placeholder="<?php _e('Select or search one or more services')?>"
                    name="_sln_booking_services[]" id="_sln_booking_services">
                <?php foreach ($plugin->getServices() as $service) : ?>
                    <option
                        class="red"
                        value="sln_booking_services_<?php echo $service->getId() ?>"
                        data-price="<?php echo $service->getPrice(); ?>"
                        <?php echo $booking->hasService($service) ? 'selected="selected"' : '' ?>
                        ><?php echo $service->getName(); ?>
                        (<?php echo $plugin->format()->money($service->getPrice()) ?>)
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 sln-select-wrapper" id="sln-services-notifications">
        </div>
    </div>

    <div class="sln-separator"></div>
    <div class="row">
        <div class="col-md-3 col-sm-4">
            <div class="form-group sln_meta_field sln-select-wrapper">
                <label><?php _e('Duration', 'sln'); ?></label>
                <input type="text" value="<?php echo $booking->getDuration()->format('H:i') ?>" class="form-control" readonly="readonly"/>
            </div>
        </div>
        <div class="col-md-3 col-sm-4">
            <?php
            $helper->showFieldText(
                $helper->getFieldName($postType, 'amount'),
                __('Amount', 'sln').' ('.$settings->getCurrencySymbol().')',
                $booking->getAmount()
            );
            ?>
            <button class="btn btn-block btn-primary" id="calculate-total"><?php _e('Calculate total', 'sln') ?></button>
        </div>
        <div class="col-md-3 col-sm-4">
            <?php
            $helper->showFieldText(
                $helper->getFieldName($postType, 'deposit'),
                __('Deposit', 'sln').' '.$settings->get('pay_deposit').'% ('.$settings->getCurrencySymbol().')',
                $booking->getDeposit()
            );
            ?>
        </div>

        <div class="col-md-3 col-sm-4">
            <div class="form-group">
                <label for="">Transaction</label>

                <p><?php echo $booking->getTransactionId() ? $booking->getTransactionId() : __(
                        'n.a.',
                        'sln'
                    ) ?></p>
            </div>
        </div>
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
    <div class="row">
        <div class="col-md-12">
            <div class="form-group sln_meta_field ">
                <label><?php _e('Administration notes', 'sln'); ?></label>
                <?php SLN_Form::fieldTextarea(
                    $helper->getFieldName($postType, 'admin_note'),
                    $booking->getAdminNote()
                ); ?>
            </div>
        </div>
    </div>

</div>
