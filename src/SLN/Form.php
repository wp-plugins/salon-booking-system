<?php

class SLN_Form
{
    static public function fieldCurrency($name, $value = null, $settings = array())
    {
        self::fieldSelect($name, SLN_Currency::toArray(), $value, $settings, true);
    }

    static public function fieldTime($name, $value = null, $settings = array())
    {
        if ($value instanceof DateTime) {
            $value = $value->format('H:i');
        }
        if (!empty($settings['items'])) {
            $items = $settings['items'];
        } else {
            $interval = isset($settings['interval']) ? $settings['interval'] : null;
            $maxItems = isset($settings['maxItems']) ? $settings['maxItems'] : null;
            $items    = SLN_Func::getMinutesIntervals($interval, $maxItems);
        }

        if ((!empty($value)) && (!in_array($value, $items))) {
            $items[$value] = $value;
        }
        self::fieldSelect($name, $items, $value, $settings);
    }

    static public function fieldDate($name, $value = null, $settings = array())
    {
        if (!($value instanceof DateTime)) {
            $value = new SLN_DateTime($value);
        }
        echo "<span class=\"sln-date\">";
        self::fieldDay($name . '[day]', $value, $settings);
        self::fieldMonth($name . '[month]', $value, $settings);
        self::fieldYear($name . '[year]', $value, $settings);
        echo "</span>";
    }

    static public function fieldJSDate($name, $value = null, $settings = array()){
       $f = SLN_Plugin::getInstance()->getSettings()->get('date_format');
       $jsFormat = SLN_Enum_DateFormat::getJsFormat($f);
       $phpFormat = SLN_Enum_DateFormat::getPhpFormat($f);
 
        ?><span class="sln-jsdate">
        <div class="sln_datepicker"><input type="text" name="<?php echo $name ?>" id="<?php echo self::makeID($name) ?>" 
            required="required" data-format="<?php echo $jsFormat?>" class="form-control" 
            value="<?php echo ucwords(date_i18n($phpFormat, $value->format('U'))) ?>" data-locale="<?php echo strtolower(substr(get_locale(),0,2))?>"/></div>
        </span><?php
    }

    static public function fieldJSTime($name, $value, $settings){ 
       $f = SLN_Plugin::getInstance()->getSettings()->get('time_format');
       $jsFormat = SLN_Enum_TimeFormat::getJsFormat($f);
       $phpFormat = SLN_Enum_TimeFormat::getPhpFormat($f);
            $interval = isset($settings['interval']) ? $settings['interval'] : 60;
        ?><span class="sln-jstime">
        <div class="sln_timepicker"><input type="text" name="<?php echo $name ?>" id="<?php echo self::makeID($name) ?>" 
            data-meridian="<?php echo strpos($phpFormat,'a') !== false ? 'true' : 'false' ?>"
            required="required" data-format="<?php echo $jsFormat ?>" class="form-control"
            value="<?php echo $value->format($phpFormat) ?>" data-interval="<?php echo $interval ?>" data-locale="<?php echo strtolower(substr(get_locale(),0,2))?>"/></div>
        </span><?php
    }

    static public function fieldDay($name, $value, $settings = array())
    {
        if ($value instanceof DateTime) {
            $value = $value->format('d');
        }
        self::fieldNumeric(
            $name,
            $value,
            array_merge(
                $settings,
                isset($settings['days']) ?
                    array('items' => $settings['days'])
                    : array('min' => 1, 'max' => 31)
            )
        );
    }

    static public function fieldMonth($name, $value, $settings = array())
    {
        if ($value instanceof DateTime) {
            $value = $value->format('m');
        }
        self::fieldSelect(
            $name,
            isset($settings['months']) ? $settings['months'] : SLN_Func::getMonths(),
            $value,
            $settings,
            true
        );
    }

    static public function fieldYear($name, $value, $settings = array())
    {
        if ($value instanceof DateTime) {
            $value = $value->format('Y');
        }
        $currY = date('Y');
        self::fieldSelect(
            $name,
            isset($settings['years']) ? $settings['years'] : SLN_Func::getYears($value < $currY ? $value : $currY - 1),
            $value,
            $settings
        );
    }

    static public function fieldNumeric($name, $value = null, $settings = array())
    {
        //if($value != null) $value = (int) $value;
        if (!empty($settings['items'])) {
            $items = $settings['items'];
        } else {
            $min      = isset($settings['min']) ? $settings['min'] : 1;
            $max      = isset($settings['max']) ? $settings['max'] : 20;
            $interval = isset($settings['inverval']) ? $settings['interval'] : 1;
            $items    = array();

            for ($i = $min; $i <= $max; $i = $i + $interval) {
                $items[] = $i;
            }
        }
        self::fieldSelect($name, $items, $value, $settings);
    }

    static public function fieldSelect($name, $items, $value, $settings = array(), $map = false)
    {
        if (isset($settings['map'])) {
            $map = $settings['map'];
        }
        $settings['attrs']['class'] = "form-control";
        ?>
        <select name="<?php echo $name ?>" id="<?php echo self::makeID($name) ?>" <?php echo self::attrs($settings) ?>>
            <?php
            foreach ($items as $key => $label) {
                $key      = $map ? $key : $label;
                $selected = $key == $value ? 'selected="selected"' : '';
                ?>
                <option value="<?php echo esc_attr($key) ?>" <?php echo $selected ?>><?php echo $label ?></option>
            <?php
            }
            ?>
        </select>
    <?php
    }

    static public function fieldCheckbox($name, $value = false, $settings = array())
    {
        ?>
        <input type="checkbox" name="<?php echo $name ?>" id="<?php echo self::makeID($name) ?>"
               value="1"  <?php echo $value ? 'checked="checked"' : '' ?> <?php echo self::attrs($settings) ?>/>
    <?php
    }

    static public function fieldRadiobox($name, $value, $checked = false, $settings = array())
    {
        ?>
        <input type="radio" name="<?php echo $name ?>" id="<?php echo self::makeID($name.'['.$value.']') ?>"
               value="<?php echo $value?>"  <?php echo $checked ? 'checked="checked"' : '' ?> <?php echo self::attrs($settings) ?>/>
    <?php
    }

   static public function fieldText($name, $value = false, $settings = array())
    {
        if (!isset($settings['required'])) {
            $settings['required'] = false;
        }
        $settings['attrs']['class'] = "form-control";
        ?>
        <input type="<?php echo isset($settings['type']) ? $settings['type'] : 'text' ?>" name="<?php echo $name ?>"
               id="<?php echo self::makeID($name) ?>"
               value="<?php echo esc_attr($value) ?>" <?php echo self::attrs($settings) ?>/>
    <?php
    }

    static public function fieldTextarea($name, $value = false, $settings = array())
    {
        if (!isset($settings['required'])) {
            $settings['required'] = false;
        }
        $settings['attrs']['class'] = "form-control";
        ?>
        <textarea name="<?php echo $name ?>" id="<?php echo self::makeID($name) ?>" <?php echo self::attrs(
            $settings
        ) ?>><?php echo esc_attr($value) ?></textarea>
    <?php
    }

    static public function makeID($val)
    {
        return str_replace('[', '_', str_replace(']', '', $val));
    }

    static private function attrs($settings)
    {
        if (is_array($settings)) {
            $ret = (isset($settings['required']) && $settings['required']) ?
                'required="required" ' : '';
            if (isset($settings['attrs']) && is_array($settings['attrs'])) {
                foreach ($settings['attrs'] as $k => $v) {
                    $ret .= " $k=\"$v\"";
                }
            } else {
                $ret .= (string)isset($settings['attrs']) ? $settings['attrs'] : '';
            }

            return $ret;
        } elseif (is_string($settings)) {
            return $settings;
        }

    }
}
