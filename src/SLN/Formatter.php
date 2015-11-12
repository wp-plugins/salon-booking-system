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
        $s = $this->plugin->getSettings();
        $isLeft = $s->get('pay_currency_pos') == 'left';
        $rightSymbol = $isLeft ? '' : $s->getCurrencySymbol();
        $leftSymbol = $isLeft ? $s->getCurrencySymbol() : '';
        
        if (!$showFree) {
            return ($leftSymbol. number_format($val, 2) . $rightSymbol);
        }
        return $val > 0 ? ($leftSymbol . number_format($val, 2) . $rightSymbol) : __('free','sln');
    }

    public function datetime($val)
    {
        return self::date($val).' '.self::time($val);
    }

    public function date($val)
    {
        if ($val instanceof DateTime) {
            $val = $val->format('Y-m-d H:i');
        } else {
            $val = strtotime($val);
        }

        $f = SLN_Plugin::getInstance()->getSettings()->get('date_format');
        $phpFormat = SLN_Enum_DateFormat::getPhpFormat($f);
        return date_i18n($phpFormat, strtotime($val));
    }

    public function time($val)
    {
        $f = SLN_Plugin::getInstance()->getSettings()->get('time_format');
        $phpFormat = SLN_Enum_TimeFormat::getPhpFormat($f);
        if ($val instanceof DateTime) {
            $val = $val->format('Y-m-d H:i');
        } else {
            $val = strtotime($val);
        }


        return date_i18n($phpFormat, strtotime($val));
    }
}
