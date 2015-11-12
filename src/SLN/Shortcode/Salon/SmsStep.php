<?php

class SLN_Shortcode_Salon_SmsStep extends SLN_Shortcode_Salon_AbstractUserStep
{
    public function render()
    {
        if (!isset($_SESSION['sln_sms_tests'])) {
            $_SESSION['sln_sms_tests'] = 0;
        }
        $tests = intval($_SESSION['sln_sms_tests']);
        $valid = isset($_SESSION['sln_sms_valid']) ? $_SESSION['sln_sms_valid'] : false;

        if (!$valid) {
            if (!isset($_POST['sln_verification'])) {
                $values = isset($_SESSION['sln_detail_step']) ? $_SESSION['sln_detail_step'] : array();
                if(isset($values['phone'])){
                    $_SESSION['sln_sms_tests']++;
                    $_SESSION['sln_sms_code'] = rand(0, 999999);
                    SLN_Enum_SmsProvider::getService(
                        $this->getPlugin()
                            ->getSettings()
                            ->get('sms_provider'),

                        $this->getPlugin()
                    )->send($values['phone'], $_SESSION['sln_sms_code']);
                }else{
                    $this->addError(__('Phone number wrong or not defined, you need to define a valid phone number', 'sln'));
                }
            }
        }

        return parent::render();
    }

    protected function dispatchForm()
    {
        $values = isset($_SESSION['sln_detail_step']) ? $_SESSION['sln_detail_step'] : array();
        $valid = isset($_SESSION['sln_sms_valid']) ? $_SESSION['sln_sms_valid'] : false;

        if (!$valid) {
            if (isset($_POST['sln_verification'])) {
                if ($_POST['sln_verification'] == $_SESSION['sln_sms_code']) {
                    $_SESSION['sln_sms_valid'] = true;
                    $this->successRegistration($values);
                    return true;
                } else {
                    $_SESSION['sln_sms_valid'] = false;
                    $this->addError(__('Your verification code is not valid', 'sln'));
                    return false;
                }
            }
        }
    }

}
