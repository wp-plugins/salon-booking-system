<?php
$helper->showNonce($postType);
?>
<div class="row sln-service-price-time">
    <div class="col-xs-6 col-md-2 col-sm-2 col-lg-2">
        <div class="form-group">
            <label><?php _e('Price', 'sln') . ' (' . $settings->getCurrencySymbol() . ')' ?></label>
            <?php SLN_Form::fieldText($helper->getFieldName($postType, 'price'), $service->getPrice()); ?>
        </div>
    </div>
    <div class="col-xs-6 col-md-3 col-sm-2 col-lg-2">
        <div class="form-group">

            <label><?php _e('Unit per hour', 'sln'); ?></label>
            <?php SLN_Form::fieldNumeric($helper->getFieldName($postType, 'unit'), $service->getUnitPerHour()); ?>
        </div>
    </div>
    <div class="col-xs-6 col-md-3 col-sm-2 col-lg-2">
        <div class="form-group">

            <label><?php _e('Duration', 'sln'); ?></label>
            <?php SLN_Form::fieldTime($helper->getFieldName($postType, 'duration'), $service->getDuration()); ?>
        </div>
    </div>
    <div class="col-xs-6 col-md-4 col-sm-6 col-lg-6">
        <div class="form-group">
            <label><?php _e('Secondary', 'sln'); ?></label>
            <?php SLN_Form::fieldCheckbox($helper->getFieldName($postType, 'secondary'), $service->isSecondary()) ?>
            <br/><em><?php _e('Select this if you want this service considered as secondary level service','sln'); ?></em>
        </div>
    </div>
    <div class="sln-clear"></div>
</div>
<h3><?php _e('Not available on','sln'); ?></h3>
<?php
$days = SLN_Func::getDays();
?>
<div class="row">
    <div class="col-md-12 services-notavailable">
        <?php foreach ($days as $k => $day) { ?>
            <label>
                <?php SLN_Form::fieldCheckbox(
                    $helper->getFieldName($postType, 'notav_' . $k),
                    $service->getNotAvailableOn($k)
                ) ?>
                <?php echo substr($day, 0, 3) ?>
            </label>
        <?php } ?>
    </div>
    <div class="col-xs-6 col-md-3 col-lg-2 col-sm-3  services-notavailable-h">
        <label>
            <?php echo __('From', 'sln') ?>
            <?php SLN_Form::fieldTime(
                $helper->getFieldName($postType, 'notav_from'),
                $service->getNotAvailableFrom()
            ) ?>
        </label>
    </div>
    <div class="col-xs-6 col-md-3 col-lg-2 col-sm-3  services-notavailable-h">
        <label>
            <?php echo __('To', 'sln') ?>
            <?php SLN_Form::fieldTime(
                $helper->getFieldName($postType, 'notav_to'),
                $service->getNotAvailableTo()
            ) ?>
        </label>

    </div>
</div>
<em><?php _e('Leave blank if you want this service available every working days at every working hour', 'sln') ?></em>
<div class="sln-clear"></div>
