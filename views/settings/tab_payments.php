<div class="sln-tab" id="sln-tab-payments">
    <div class="row">
        <div class="col-md-4 col-sm-6">
			<?php $settings=$this->hidePriceSettings();?>
            <?php $this->row_input_checkbox('pay_enabled', __('Enable online payments', 'sln'),$settings); ?>
            <p><?php _e('Allow users to pay in advance using PayPal.', 'sln') ?></p>
        </div>
        <div class="col-md-4 col-sm-6">
            <?php $this->row_input_checkbox('pay_cash', __('Enable "Pay later" option', 'sln')); ?>
            <p><?php _e('Give users the option to pay once they are at your salon.', 'sln') ?></p>
        </div>
  </div>

    <div class="clearfix"></div>
            <div class="sln-separator"></div>

    <div class="row">
        <div class="col-md-4 col-sm-6">
            <?php $this->row_input_text('pay_paypal_email', __('Enter your PayPal e-mail address', 'sln')); ?>
        </div>
        <div class="col-md-3 col-sm-4">
             <label for="salon_settings_pay_currency"><?php _e('Set your currency','sln') ?></label>
                <?php echo SLN_Form::fieldCurrency(
                    "salon_settings[pay_currency]",
                    $this->settings->getCurrency()
                ) ?>
        </div>
        <div class="col-md-3 col-sm-4">
             <label for="salon_settings_pay_currency_pos"><?php _e('Set your currency position','sln') ?></label>
                 <?php echo SLN_Form::fieldSelect(
                        'salon_settings[pay_currency_pos]',
                        array('left' => __('on left side'),'right' => __('on right side')),
                        $this->settings->get('pay_currency_pos'),
                        array(),
                        true
                    ) ?>
        </div>
 
    </div> 
    <div class="clearfix"></div>
            <div class="sln-separator"></div>
    <div class="row">
        <div class="col-md-4 col-sm-6">
            <div class="form-group">
                <?php $this->row_input_checkbox('pay_paypal_test', __('Enable PayPal sandbox', 'sln')); ?>
                <p><?php _e('Check this option to test PayPal payments<br /> using your PayPal Sandbox account.', 'sln') ?></p>
            </div>
        </div>
    </div> 
</div>
