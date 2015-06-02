<?php
/**
 * @var SLN_Shortcode_Salon_Step $step
 */

if (!isset($nextLabel)) {
    $nextLabel = __('Next step', 'sln');
}
$i       = 0;
$salon  = $step->getShortcode();
$steps   = $salon->getSteps();
$count   = count($steps);
$current = $salon->getCurrentStep();
$count   = count($steps);
foreach ($steps as $step) {
    $i++;
    if ($current == $step) {
        $currentNum = $i;
    }
}
$ajaxEnabled = $plugin->getSettings()->isAjaxEnabled();
?>
<div id="sln-notifications"></div>
<div class="form-actions row aligncenter">
    <div class="col-xs-6">
        <?php if ($backUrl && $currentNum > 1) : ?>
            <a class="btn btn-default pull-right"
                <?php if($ajaxEnabled): ?>
                    data-salon-data="<?php echo "sln_step_page=".$salon->getPrevStep() ?>" data-salon-toggle="direct"
                <?php endif?>
                href="<?php echo $backUrl ?> ">
                <i class="glyphicon glyphicon-chevron-left"></i> <?php _e('Back', 'sln') ?>
            </a>
        <?php endif ?>
    </div>
    <div class="col-xs-6">
        <div>
            <button
                <?php if($ajaxEnabled): ?>
                    data-salon-data="<?php echo "sln_step_page=$current&$submitName=next" ?>" data-salon-toggle="next"
                <?php endif?>
                id="sln-step-submit" type="submit" class="btn btn-danger btn-confirm" name="<?php echo $submitName ?>" value="next">
                <?php echo $nextLabel ?> <i class="glyphicon glyphicon-chevron-right"></i>
            </button>
            <?php if ($currentNum > 1): ?>
                <span class="sln-step-num"><?php echo sprintf(__('step %s of %s', 'sln'), $currentNum, $count) ?></span>
            <?php endif ?>
        </div>
    </div>
</div>
