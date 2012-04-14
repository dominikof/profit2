<?php
// ================================================================================================
//    System     : PrCSM05
//    Module     : sysCrypt.class.php
//    Version    : 1.0.0
//    Date       : 14.03.2005
//
//    Purpose    : Class definition for all actions with generating of some random strings
//
//    Called by  : *ANY
//
// ================================================================================================


// ================================================================================================
//    Class             : Crypt
//    Version           : 1.0.0
//    Date              : 14.03.2005
//
//    Constructor       : Yes
//    Parms             :
//    Returns           : None
//    Description       : Class definition for all actions with generating of some random strings
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  14.03.2005
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class Crypt {
  var $Rights = NULL;
  var $str = NULL;    

       // ================================================================================================
       //    Function          : User (Constructor)
       //    Version           : 1.0.0
       //    Date              : 21.02.2005
       //    Parms
       //    Returns           : Error Indicator
       //
       //    Description       :
       // ================================================================================================
       function Crypt()
       {
          if (empty($this->Rights)) $this->Rights = new Rights();
          $this->str = NULL;
       } //end of User Constructor

       // ================================================================================================
       // Function : GetRandNumStr
       // Version : 1.0.0
       // Date : 14.03.2005
       //
       // Parms :   $length - length of generated number
       // Returns : $new - the random number after generaing
       // Description : Generate random number string but not very simple.
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 21.01.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetRandNumStr($length)
       {
           $new_str='';
           while (strlen($new_str)!=$length)
           {
              $new=rand(0,9);
              $new_str=$new_str.$new;
           }
           $this->str = $new_str;
           return $this->str;
       } //end of function GetRandNumStr()

       // ================================================================================================
       // Function : GetRandLetterStr
       // Version : 1.0.0
       // Date : 14.03.2005
       //
       // Parms :   $length - length of generated number
       //           $case   - case of the string (1 - lower case, 2 - upper case).
       //                     If no then upper and lower together.
       // Returns : $new - the random letter string after generaing
       // Description : Generate random letter string but not very simple.
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 21.01.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetRandLetterStr($length, $case = NULL)
       {
           $s1 = "abcdifghijklmnopqrstuvwxyz";
           $s2 = strtoupper($s1);
           $str=$s1.$s2;
           if ($case==1) $str=$s1;
           if ($case==2) $str=$s2;
           $new_str = NULL;
           //$new_str = str_shuffle($str);
           //$new_str = substr($new_str, 0, $length);
           while (strlen($new_str)!=$length)
           {
              $new = str_shuffle($str);
              $new = substr($new, 0, 1);
              $new_str=$new_str.$new;
           }
           $this->str = $new_str;
           return $this->str;
       } //end of function GetRandCharStr()

       // ================================================================================================
       // Function : GetRandLetterStr
       // Version : 1.0.0
       // Date : 14.03.2005
       //
       // Parms :   $length - length of generated number
       //           $case   - case of the string (1 - lower case, 2 - upper case).
       //                     If no then upper and lower together.
       // Returns : $new - the random letter string after generaing
       // Description : Generate random letter string but not very simple.
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 21.01.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetRandCharStr($length, $case = NULL)
       {
           $num_str = $this->GetRandNumStr($length);
           $letter_str = $this->GetRandLetterStr($length, $case);
           $new_str = str_shuffle($num_str.$letter_str);
           $new_str = substr($new_str, 0, $length);
           /*
           while (strlen($new_str)!=$length)
           {
              $new = str_shuffle($num_str.$letter_str);
              $new = substr($new, 0, 1);
              $new_str=$new_str.$new;
           }
           */
           $this->str = $new_str;
           return $this->str;
       } //end of function GetRandCharStr()
       
       // ================================================================================================
       // Function : GetRandLetterStr
       // Version : 1.0.0
       // Date : 14.03.2005
       //
       // Parms :   $length - length of generated number
       //           $case   - case of the string (1 - lower case, 2 - upper case).
       //                     If no then upper and lower together.
       // Returns : $new - the random letter string after generaing
       // Description : Generate random letter string but not very simple.
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 21.01.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function CryptStr($str = NULL)
       {
         if ( $str!=NULL ) $this->str = $str;

         $this->str = md5( md5(strrev($this->str)).md5($this->str) );
         return $this->str;
       }       

     // ================================================================================================
     // Function : GetTranslitStr()
     // Version : 1.0.0
     // Parms :
     // Returns : st
     // Description : convert symbols to translit
     // ================================================================================================
     // Programmer : Ihor Trokhymchuk
     // Date : 11.05.2007 
     // Reason for change : Reason Description / Creation
     // Change Request Nbr:
     // ================================================================================================
     function GetTranslitStr($st) {
         //echo '<br>$st='.$st;
         $search =  array("/А/", "/Б/", "/В/", "/Г/", "/Д/", "/Е/", "/З/", "/И/", "/Й/", "/К/", "/Л/", "/М/", "/Н/", "/О/", "/П/", "/Р/", "/С/", "/Т/", "/У/", "/Ф/", "/Х/", "/Ъ/", "/Ы/", "/Э/", "/а/", "/б/", "/в/", "/г/", "/д/", "/е/", "/з/", "/и/", "/й/", "/к/", "/л/", "/м/", "/н/", "/о/", "/п/", "/р/", "/с/", "/т/", "/у/", "/ф/", "/х/", "/ъ/", "/ы/", "/э/", "/ж/", "/ц/", "/ч/", "/ш/", "/щ/",  "/ь/", "/ю/", "/я/", "/Ж/", "/Ц/", "/Ч/", "/Ш/", "/Щ/",  "/Ь/", "/Ю/", "/Я/", "/ї/", "/Ї/", "/є/", "/Є/", "/ё/", "/Ё/", "/\?/", "/'/", "/\"/", "/«/", "/»/", "/, /", "/,/", "/\./", '/\$/', "/\&/", "/:/", "/і/", "/І/", "/`/", "/´/", "/№/", "/%/", "/\+/", "/\{/", "/\}/", "/\[/", "/\]/", "/</", "/>/", "/\|/", "/”/", "/™/", "/“/", "/”/", "/\//", "/_/", "/ - /", "/ /", "/-/", "//", "/’/", "/\!/", "/„/", "/#/", "/\n/", "/\r/", "/\t/", "/²/", "/ґ/", "/\\\/");
         $replace = array("A",   "B",   "V",   "G",   "D",   "E",   "Z",   "I",   "Y",   "K",   "L",   "M",   "N",   "O",   "P",   "R",   "S",   "T",   "U",   "F",   "H",   "",    "I",   "E",   "a",   "b",   "v",   "g",   "d",   "e",   "z",   "i",   "y",   "k",   "l",   "m",   "n",   "o",   "p",   "r",   "s",   "t",   "u",   "f",   "h",   "",    "i",   "e",   "zh",  "ts",  "ch",  "sh",  "shch", "",    "yu",  "ya",  "ZH",  "TS",  "CH",  "SH",  "SHCH", "",    "YU",  "YA",  "yi",  "Yi",  "ye",  "Ye",  "ie",  "Ie",  "",     "",    "",     "",    "",    "-",    "-",   "-",    "-",    "-",    "-",   "i",   "I",   "",    "",    "N",   "",    "-",    "(",    ")",    "(",    ")",    "(",   ")",   "-" ,   "",    "_tm", "",    "" ,   "-",    "-",   "-",     "-",   "-",   "",   "",    "",     "",    "",    "-",    "-",    "-",     "",   "g",   "" );
         $st = preg_replace($search, $replace, $st);
         $st = strtolower($st);
         //============= delete dublicates ================
         //                 4latin  3latin   center russian  2latin
         $search =  array("/----/", "/---/", "/-–-/",        "/--/");
         $replace = array("-",      "-",     "-",            "-");
         $st = preg_replace($search, $replace, $st);
         //================================================
         //echo '<br>$st='.$st;
         return $st;
     }// end of GetTranslitStr()

     
     // ================================================================================================
     // Function : Truncate Str()
     // Version : 1.0.0
     // Description : Truncates string to $maxLength symbols
     // Programmer : Yaroslav Gyryn
     // ================================================================================================
     function TruncateStr($text, $maxLength = 500) {
        $short = $text;
        $col = strlen(substr( $short , 0 , $maxLength ));
        if($col >= $maxLength ) {
            $pos = strrpos(substr( $short , 0 , $maxLength) ," ");
            $short = substr( $short , 0 , $pos).'...';
        }  
        return $short;
     }   
       
 } //end of class Crypt
?>