<?php

class SLN_Shortcode_Salon_ThankyouStep extends SLN_Shortcode_Salon_Step
{
    private $op;

    protected function dispatchForm()
    {
        $bb      = $this->getPlugin()->getBookingBuilder();
        $booking = $bb->getLastBooking();
        if (isset($_GET['op'])) {
            $op       = explode('-', $_GET['op']);
            $this->op = $op[0];
            if ($this->op == 'success') {
                if($this->getPlugin()->getSettings()->isPaypalTest()){
                    update_post_meta($booking->getId(), '_sln_booking_transaction_id', 'test');
                    $booking->setStatus(SLN_Enum_BookingStatus::PAID);
                }
                $this->goToThankyou();
            } elseif ($this->op == 'notify') {
                $booking = $this->getPlugin()->createBooking($op[1]);
                update_post_meta($booking->getId(), '_sln_paypal_ipn_' . uniqid(), $_POST);
                $ppl = new SLN_Payment_Paypal($this->getPlugin());
                ob_end_clean();
                if ($ppl->reverseCheckIpn() && $ppl->isCompleted($booking->getAmount())) {
                    update_post_meta($booking->getId(), '_sln_booking_transaction_id', $ppl->getTransactionId());
                    $booking->setStatus(SLN_Enum_BookingStatus::PAID);
                    echo('ipn success');
                }else{
                    echo('ipn_failed');
                }
            }
        } elseif ($_GET['mode'] == 'paypal') {
            $ppl = new SLN_Payment_Paypal($this->getPlugin());
            $url = $ppl->getUrl($booking->getId(), $booking->getAmount(), $booking->getTitle());
            wp_redirect($url);
        } elseif ($_GET['mode'] == 'later') {
            $bb->getLastBooking()->setStatus(SLN_Enum_BookingStatus::PAY_LATER);
            $this->goToThankyou();
        }


        return false;
    }

    public function goToThankyou()
    {
        $id = $this->getPlugin()->getSettings()->getThankyouPageId();
        if ($id) {
            wp_redirect(get_permalink($id));
        }
    }

    public function getViewData()
    {
        $ret        = parent::getViewData();
        $formAction = $ret['formAction'];
        $formAction = remove_query_arg('op', $formAction);

        return array_merge(
            $ret,
            array(
                'formAction' => $formAction,
                'booking'    => $this->getPlugin()->getBookingBuilder()->getLastBooking(),
                'laterUrl'   => add_query_arg(
                    array('mode' => 'later', 'submit_' . $this->getStep() => 1),
                    $formAction
                ),
                'paypalUrl'  => add_query_arg(
                    array('mode' => 'paypal', 'submit_' . $this->getStep() => 1),
                    $formAction
                ),
                'paypalOp'   => $this->op
            )
        );
    }
}
