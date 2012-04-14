<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of jqueryUi
 *
 * @author Сергей
 */
class jqueryUi {
    function __construct($calendar_lib_path = '', $lang = 'en') {
        $this->calendar_lib_path = 'http://' . NAME_SERVER . '/sys/js/jqueryUi/';
        $this->UIcore = 'js/jquery-ui-1.8.16.custom.min.js';
        $this->cookiePlugin = 'js/cookie.js';
        $this->localizationPath = 'js/localization/';
        $this->datePickerUA = "jquery.ui.datepicker-ua.js";
        $this->datePickerRU = "jquery.ui.datepicker-ru.js";
        $this->theme = "css/redmond/jquery-ui-1.8.16.custom.css";
       //$this->theme = "css/start/jquery-ui-1.8.16.custom.css";
        //$this->theme = "css/hot-sneaks/jquery-ui-1.8.16.custom.css";
        if (_LANG_SHORT != 'en')
            $this->lang = _LANG_SHORT;
    }
    function load_files() {
        ?>
        <link rel="stylesheet" type="text/css" media="all" href="<?=$this->calendar_lib_path.$this->theme?>" />
        <script type="text/javascript" src="<?=$this->calendar_lib_path . $this->UIcore?>"></script>
        <script type="text/javascript" src="<?=$this->calendar_lib_path . $this->cookiePlugin?>"></script>
        <?
         switch (_LANG_SHORT) {
            case 'ua':
                ?>
                <script type="text/javascript" src="<?=$this->calendar_lib_path . $this->localizationPath . $this->datePickerUA?>"></script>
                <?
            break;
            case 'ru':
                ?>
                <script type="text/javascript" src="<?=$this->calendar_lib_path . $this->localizationPath . $this->datePickerRU?>"></script>
                 <?
            break;
            default:

                break;
        }
        
    }
}

?>
