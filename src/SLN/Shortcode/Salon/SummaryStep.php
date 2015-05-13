<?php

class SLN_Shortcode_Salon_SummaryStep extends SLN_Shortcode_Salon_Step
{
    protected function dispatchForm()
    {
        $bb     = $this->getPlugin()->getBookingBuilder();
        $values = isset($_POST['sln']) ? $_POST['sln'] : array();
        if (!$bb->getLastBooking()) {
            $bb->set('note', SLN_Func::filter($values['note']));
            $bb->create();
            if($this->getPlugin()->getSettings()->get('confirmation')){
                $this->getPlugin()->sendMail(
                    'mail/summary',
                    array('booking' => $bb->getLastBooking())
                );
                $this->getPlugin()->sendMail(
                    'mail/summary_admin',
                    array('booking' => $bb->getLastBooking())
                );
            }
        }

        return true;
    }

    public function render()
    {
        $bb = $this->getPlugin()->getBookingBuilder();
        if ($bb->getLastBooking()) {
            $data = $this->getViewData();
            wp_redirect(
                add_query_arg(array('submit_' . $this->getStep() => 1), $data['formAction'])
            );
        } elseif (!$bb->getServices()) {
            wp_redirect(
                add_query_arg(array('sln_step_page' => 'services'))
            );
        } else {
            return parent::render();
        }
    }
}
