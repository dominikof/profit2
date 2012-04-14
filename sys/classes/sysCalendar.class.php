<?php

// ================================================================================================
// System : PrCSM05
// Module : sysAuthorize.class.php
// Version : 1.0.0
// Date : 14.02.2005
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for DHTML Calendar
//
// ================================================================================================

/**
 *  File: calendar.php | (c) dynarch.com 2004
 *  Distributed as part of "The Coolest DHTML Calendar"
 *  under the same terms.
 *  -----------------------------------------------------------------
 *  This file implements a simple PHP wrapper for the calendar.  It
 *  allows you to easily include all the calendar files and setup the
 *  calendar by instantiating and calling a PHP object.
 */
define('NEWLINE', "\n");

class DHTML_Calendar {

    var $calendar_lib_path;
    var $calendar_timePickerAddon;
    var $localizationPath;
    var $datePickerUA;
    var $datePickerRU;
    var $timePickerUA;
    var $timePickerRU;
    var $teme;
    var $lang = "";
    var $calendar_options;

    function DHTML_Calendar($calendar_lib_path = '', $lang = 'en') {

        $this->calendar_lib_path = 'http://' . NAME_SERVER . '/sys/js/datapicker/';
        $this->calendar_timePickerAddon = 'timePickerAddon.js';
        $this->localizationPath = 'localization/';
        $this->timePickerUA = "jquery-ui-timepicker-ua.js";
        $this->timePickerRU = "jquery-ui-timepicker-ru.js";
        $this->calendar_options = array("stepMinute" => 1,
            "changeMonth" => true,
            "changeYear" => true,
            "dateFormat" => "yy-mm-dd",
            "buttonImage" => $this->calendar_lib_path . 'img.gif',
            "buttonImageOnly" => "true",
            "hourGrid" => "4",
            "minuteGrid" => "10"
        );
        if (_LANG_SHORT != 'en')
            $this->lang = _LANG_SHORT;
    }

//    function set_option($name, $value) {
//        $this->calendar_options[$name] = $value;
//    }

    function load_files() {
        echo $this->get_load_files_code();
    }

    function get_load_files_code() {
        
        $code = ( '<script type="text/javascript" src="' .
                $this->calendar_lib_path . $this->calendar_timePickerAddon .
                '"></script>' . NEWLINE );
        switch (_LANG_SHORT) {
            case 'ua':
                $code .= ( '<script type="text/javascript" src="' .
                        $this->calendar_lib_path . $this->localizationPath . $this->timePickerUA .
                        '"></script>' . NEWLINE );

                break;
            case 'ru':
                $code .= ( '<script type="text/javascript" src="' .
                        $this->calendar_lib_path . $this->localizationPath . $this->timePickerRU .
                        '"></script>' . NEWLINE );

                break;
            default:

                break;
        }
        return $code;
    }

    function _make_calendar($selector=".datepicker", $other_options = array()) {
        $js_options = $this->_make_js_hash(array_merge($this->calendar_options, $other_options));
        $code = " <script type='text/javascript'>
            $(document).ready(function(){
                      $('" . $selector . "').datetimepicker($.extend($.datepicker.regional['" . $this->lang . "'],{
                          " . $js_options . "
                      }));
          });
        </script>  
        ";
        return $code;
    }

    function make_input_field($field_attributes = array(), $cal_options = array()) {
        $id = $this->_gen_id();
        // echo '$this->_field_id($id)='.$this->_field_id($id);
        // echo '$this->_trigger_id($id)='.$this->_trigger_id($id);
        $options = array_merge($cal_options, array('inputField' => $this->_field_id($id),
            'button' => $this->_trigger_id($id)));
        $attrstr = $this->_make_html_attr(array_merge(
                        $field_attributes, array(
                    'id' => $this->_field_id($id),
                    'type' => 'text',
                    'class' => 'datepicker',
                    'style' => ''), $options));
        echo '<input ' . $attrstr . '/>';
        echo $this->_make_calendar("#" . $this->_field_id($id));
    }

    function _gen_id() {
        static $id = 0;
        return++$id;
    }

    function _field_id($id) {
        return 'f-calendar-field-' . $id;
    }

    function _trigger_id($id) {
        return 'f-calendar-trigger-' . $id;
    }

    function _make_js_hash($array) {
        $jstr = '';
        reset($array);
        while (list($key, $val) = each($array)) {
            if (is_bool($val))
                $val = $val ? 'true' : 'false';
            else if (!is_numeric($val))
                $val = '"' . $val . '"';
            if ($jstr)
                $jstr .= ',';
            $jstr .= '"' . $key . '":' . $val;
        }
        return $jstr;
    }

    function _make_html_attr($array) {
        $attrstr = '';
        reset($array);
        while (list($key, $val) = each($array)) {
            $attrstr .= $key . '="' . $val . '" ';
        }
        return $attrstr;
    }

}

;
?>