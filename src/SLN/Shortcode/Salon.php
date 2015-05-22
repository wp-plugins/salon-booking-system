<?php

class SLN_Shortcode_Salon
{
    const STEP_KEY = 'sln_step_page';
    const STEP_DEFAULT = 'date';

    private $plugin;
    private $attrs;

    private $steps;
    private $currentStep;


    function __construct(SLN_Plugin $plugin, $attrs)
    {
        $this->plugin = $plugin;
        $this->attrs = $attrs;
    }

    public static function init(SLN_Plugin $plugin)
    {
        add_shortcode('salon', array('SLN_Shortcode_Salon', 'create'));
    }

    public static function create($attrs)
    {
        $obj = new SLN_Shortcode_Salon(SLN_Plugin::getInstance(), $attrs);

        return $obj->execute();
    }

    public function execute()
    {
        return $this->dispatchStep($this->getCurrentStep());
    }

    private function dispatchStep($curr)
    {
        $found = false;
        foreach ($this->getSteps() as $step) {
            if ($curr == $step || $found) {
                $found = true;
                $this->currentStep = $step;
                $obj = $this->getStepObject($step);
                if (!$obj->isValid()) {
                    return $this->render($obj->render());
                }
            }
        }
    }

    /**
     * @param $step
     * @return SLN_Shortcode_Salon_Step
     * @throws Exception
     */
    private function getStepObject($step)
    {
        $class = __CLASS__ . '_' . ucwords($step) . 'Step';
        $obj = new $class($this->plugin, $this, $step);
        if ($obj instanceof SLN_Shortcode_Salon_Step) {
            return $obj;
        } else {
            throw new Exception('bad object ' . $class);
        }
    }

    protected function render($content)
    {
        $salon = $this;
        if(get_option(SLN_Plugin::F)> SLN_Plugin::F1){
            return $this->plugin->loadView('trial/shortcode', compact('salon'));
        } else {
            return $this->plugin->loadView('shortcode/salon', compact('content', 'salon'));
        }
    }


    public function getCurrentStep()
    {
        if (!isset($this->currentStep)) {
            $this->currentStep = isset($_GET[self::STEP_KEY]) ? $_GET[self::STEP_KEY] : self::STEP_DEFAULT;
        }

        return $this->currentStep;
    }

    public function getPrevStep()
    {
        $curr = $this->getCurrentStep();
        $prev = null;
        foreach ($this->getSteps() as $step) {
            if ($curr == $step) {
                return $prev;
            } else {
                $prev = $step;
            }
        }
    }

    private function needSecondary()
    {
        foreach ($this->plugin->getServices() as $service) {
            if ($service->isSecondary()) {
                return true;
            }
        }
    }

    private function needPayment()
    {
        return true;
    }

    private function needAttendant()
    {
        return $this->plugin->getSettings()->get('attendant_enabled') ? true : false;
    }

    public function needSms()
    {
        return (
            $this->plugin->getSettings()->get('sms_enabled')
            && !is_user_logged_in()
        ) ? true : false;
    }

    public function getSteps()
    {
        if (!isset($this->steps)) {
            $this->steps = array(
                'date',
                'services',
                'secondary',
                'attendant',
                'details',
                'sms',
                'summary',
                'thankyou'
            );
            if (!$this->needSecondary()) {
                unset($this->steps[array_search('secondary', $this->steps)]);
            }
            if (!$this->needPayment()) {
                unset($this->steps[array_search('thankyou', $this->steps)]);
            }
            if (!$this->needAttendant()) {
                unset($this->steps[array_search('attendant', $this->steps)]);
            }
            if (!$this->needSms()) {
                unset($this->steps[array_search('sms', $this->steps)]);
            }

        }

        return $this->steps;
    }
}
