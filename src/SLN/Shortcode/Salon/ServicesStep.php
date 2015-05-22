<?php

class SLN_Shortcode_Salon_ServicesStep extends SLN_Shortcode_Salon_Step
{
    private $services;

    protected function dispatchForm()
    {
        $bb     = $this->getPlugin()->getBookingBuilder();
        $values = isset($_POST['sln']) ? $_POST['sln'] : array();
        foreach ($this->getServices() as $service) {
            if (isset($values['services']) && isset($values['services'][$service->getId()])) {
                $bb->addService($service);
            } else {
                $bb->removeService($service);
            }
        }
        $bb->save();
        if (empty($values['services'])) {
            $this->addError(__('You must choose at least one service', 'sln'));

            return false;
        } else {
            return true;
        }
    }

    /**
     * @return SLN_Wrapper_Service[]
     */
    public function getServices()
    {
        if (!isset($this->services)) {
            $this->services = array();
            foreach ($this->getPlugin()->getServices() as $service) {
                if (!$service->isSecondary()) {
                    $this->services[] = $service;
                }
            }
        }

        return $this->services;
    }

}
