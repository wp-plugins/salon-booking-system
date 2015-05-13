<?php

class SLN_Shortcode_Salon_AttendantStep extends SLN_Shortcode_Salon_Step
{

    protected function dispatchForm()
    {
        $bb     = $this->getPlugin()->getBookingBuilder();
        $values = isset($_POST['sln']) ? $_POST['sln'] : array();
        foreach ($this->getAttendants() as $attendant) {
            if (isset($values['attendant']) && $values['attendant'] == $attendant->getId()) {
                $bb->setAttendant($attendant);
            }
        }
        $bb->save();

        return true;
    }

    /**
     * @return SLN_Wrapper_Attendant[]
     */
    public function getAttendants()
    {
        if (!isset($this->attendants)) {
            $this->attendants = array();
            foreach ($this->getPlugin()->getAttendants() as $attendant) {
                $this->attendants[] = $attendant;
            }
        }

        return $this->attendants;
    }

    public function isValid()
    {
        $tmp = $this->getAttendants();

        return (!empty($tmp)) && parent::isValid();
    }
}
