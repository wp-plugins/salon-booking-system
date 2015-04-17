<?php

class SLN_Formatter
{
    private $plugin;

    public function __construct(SLN_Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function money($val, $showFree = true)
    {
        $symbol = $this->plugin->getSettings()->getCurrencySymbol();
        if (!$showFree) {
            return (number_format($val, 2) . $symbol);
        }

        return $val > 0 ? (number_format($val, 2) . $symbol) : 'free';
    }

    public function datetime($val)
    {
        if ($val instanceof DateTime) {
            $val = $val->format('Y-m-d H:i:s');
        }

        return date_i18n(__('l M j, Y @ G:i', 'sln'), strtotime($val));
    }

    public function date($val)
    {
        if ($val instanceof DateTime) {
            $val = $val->format('Y-m-d H:i');
        } else {
            $val = strtotime($val);
        }

        return date_i18n(__('M j, Y', 'sln'), strtotime($val));
    }

    public function time($val)
    {
        if ($val instanceof DateTime) {
            return $val->format('H:i');
        }
    }
}
