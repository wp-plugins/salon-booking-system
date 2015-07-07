<?php
$helper->showNonce($postType);
?>

<?php
$days = SLN_Func::getDays();
?>
<div class="row">

    <div class="col-xs-3 col-md-3 col-lg-3 col-sm-3  attendants-notavailable-h">
        <?php
        $helper->showFieldtext(
            $helper->getFieldName($postType, 'email'),
            __('E-mail', 'sln'),
            $attendant->getEmail()
        ); ?>
    </div>

</div>
<h3><?php _e('Not available on','sln'); ?></h3>
<div class="row">
    <div class="col-md-12 attendants-notavailable">
        <?php foreach ($days as $k => $day) { ?>
            <label>
                <?php SLN_Form::fieldCheckbox(
                    $helper->getFieldName($postType, 'notav_' . $k),
                    $attendant->getNotAvailableOn($k)
                ) ?>
                <?php echo substr($day, 0, 3) ?>
            </label>
        <?php } ?>
    </div>
    <div class="col-xs-6 col-md-3 col-lg-2 col-sm-3  attendants-notavailable-h">
        <label>
            <?php echo __('From', 'sln') ?>
            <?php SLN_Form::fieldTime(
                $helper->getFieldName($postType, 'notav_from'),
                $attendant->getNotAvailableFrom()
            ) ?>
        </label>
    </div>
    <div class="col-xs-6 col-md-3 col-lg-2 col-sm-3  attendants-notavailable">
        <label>
            <?php echo __('To', 'sln') ?>
            <?php SLN_Form::fieldTime(
                $helper->getFieldName($postType, 'notav_to'),
                $attendant->getNotAvailableTo()
            ) ?>
        </label>
    </div>
    <div class="col-xs-12 col-md-12 col-lg-12 col-sm-12  attendants-notavailable-h">
    <em><?php _e('Leave this option blank if you want this assistant available for every hour each day', 'sln') ?></em>
</div>

</div>
<div class="sln-clear"></div>
