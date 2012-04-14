<?php
include_once( SITE_PATH.'/admin/include/defines.inc.php' );  
 
 /**
  * SystemCurrencies
  * class for maniuplation with currencies
  * @package Currencies Package of SEOCMS
  * @author Igor Trokhymchuk  <ihor@seotm.com>
  * @version 1.1, 13.10.2011
  * @copyright (c) 2011+ by SEOTM
  * @access public
  */
 class SystemCurrencies {

   public $user_id = NULL;
   public $module = NULL;

   public $lang_id = NULL;
   public $db = NULL; 
   
   public $Msg = NULL;
   public $Right = NULL;
   public $Form = NULL;
   public $script = NULL;
   public $Err = NULL;
   
   public $id = NULL;
   public $name = NULL;
   public $short = NULL;
   public $prefix = NULL;
   public $sufix = NULL;
   public $value = NULL;
   public $is_default = NULL;
   public $move = NULL;
   public $visible = NULL;
   public $listShortNames = NULL;
   public $allCurrData = NULL; //array for store currency data with index as counter
   public $currItem = NULL; //array for store currency data with index as id of currency

    /**
    * Class Constructor SystemCurrencies
    * Init variables for al currencies.
    * @param integer $user_id - id of the user
    * @param integer $module - id of the module
    * @param string $front_back - can be 'front' or 'back'
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 13.10.2011
    */      
    function __construct($user_id=NULL, $module=NULL, $front_back = 'front') {
        //Check if Constants are overrulled
        ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
        ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
        ( $front_back   !="" ? $this->front_back  = $module   : $this->front_back  = NULL );

        if ( defined("_LANG_ID") ) $this->lang_id = _LANG_ID;
            
        if (empty($this->db)) $this->db = DBs::getInstance();
        if (empty($this->Right)) $this->Right = &check_init('Rights', 'Rights', '"'.$this->user_id.'"', '"'.$this->module.'"'); 
        if (empty($this->Msg)) $this->Msg = &check_init('ShowMsg', 'ShowMsg');
                if (empty($this->Form)) $this->Form = &check_init('FormCatalog', 'FrontForm', '"form_currencies"');
        if (empty($this->Spr)) $this->Spr = &check_init('SysSpr', 'SysSpr', '"'.$this->user_id.'"', '"'.$this->module.'"');
            
        $this->LoadData($front_back);
        //$this->def_curr = $this->GetDefaultData();
    } // End of SystemCurrencies Constructor
   
    /**
     * SystemCurrencies::LoadData()
     * Load all currency data to array
     * @param string $front_back
     * @return array $this->allCurrData
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0,13.10.2011
     */
    function LoadData($front_back = 'front')
    {
       $tmp_db = new DB();
       $q = "SELECT 
             `".TblSysCurrencies."`.*,
             `".TblSysCurrenciesSprName."`.`name`,
             `".TblSysCurrenciesSprName."`.`short`, 
             `".TblSysCurrenciesSprName."`.`pref`, 
             `".TblSysCurrenciesSprName."`.`suf` 
             FROM `".TblSysCurrencies."`
             LEFT JOIN `".TblSysCurrenciesSprName."` ON (`".TblSysCurrencies."`.`id`=`".TblSysCurrenciesSprName."`.`cod` AND `".TblSysCurrenciesSprName."`.`lang_id`='".$this->lang_id."') 
             WHERE 1";
       if( $front_back=='front') $q = $q." AND `".TblSysCurrencies."`.`visible`='2'";
       $q = $q." ORDER BY `".TblSysCurrencies."`.`move`";
       $res = $tmp_db->db_Query($q);
       //echo '<br>$q='.$q.' $res='.$res;
       if( !$res OR ! $tmp_db->result) return false;
       $rows = $tmp_db->db_GetNumRows();
       for($i=0;$i<$rows;$i++){
           $row = $tmp_db->db_FetchAssoc();
           $this->allCurrData[$i] = $row;
           $this->currItem[$row['id']] = $row;
           if($row['is_default']==1) $this->def_curr = $row;
       }
       //print_r($this->allCurrData);
       return true;
    } // end of function LoadData();
    
    /**
     * SystemCurrencies::ShowCurrSelect()
     * Show Combo box for select of currecncy
     * @param string $front_back
     * @return array $this->allCurrData
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0,13.10.2011
     */
    function ShowCurrSelect($front_back = 'front')
    {
        $cnt = count($this->allCurrData);
        $arr_curr = array();
        for($i=0;$i<$cnt;$i++){
            $row = $this->allCurrData[$i];
            $name = stripslashes($row['name']);
            $arr_curr[$row['id']] = $name;
        }
        //$arr_curr = $Currency->GetShortNamesInArray('front');
		$url = $_SERVER['REQUEST_URI'];
		$scriptlink = "onchange=\"location='".$_SERVER['REQUEST_URI']."?&curr_ch='+this.value\"";
		$this->Form->SelectAct($arr_curr, 'curr_ch', _CURR_ID, $scriptlink.", 'style=\"font-size:9px;\"'");
    }//end of function ShowCurrSelect()      
        
   
    // ================================================================================================
    // Function : Converting
    // Version : 1.0.0
    // Date : 26.09.2007
    //
    // Parms :  $from_currency  - currency from
    //          $to_currency - currency to
    //          $value_currency - value of Convertinc
    //          $float - count of digits after comma  
    // Returns : value
    // Description : make convertign of $val_currency from one currency $from_currency to another $to_currency
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 26.09.2007
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function Converting($from_currency, $to_currency, $summa, $float = 2)
    {
        $ret = NULL;
        $summa = str_replace(',', '.',$summa);
        if($from_currency!=$to_currency){
           $val_def_currency = $this->GetValue($from_currency);
           $val_id_currency = $this->GetValue($to_currency);
           if($val_def_currency!=0){
               $ret0 = floatval($summa/$val_def_currency); 
               $ret = floatval($ret0*$val_id_currency);
           }
       }
       else $ret = floatval($summa);
       return round($ret, $float);
    } // end of function Converting();  
   
    // ================================================================================================
    // Function : ShowPrice
    // Date : 21.01.2011
    // Parms :  $price  - price
    //             $showText - show additional Text true/false
    //             $digits  - digits after coma
    // Returns : price
    // Description : show price
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ShowPrice($price, $showText = true, $digits=2, $curr_id=NULL)
    {
        if(empty($curr_id)) $currData = $this->def_curr;
        else $currData = $this->currItem[$curr_id];
        if($showText)
            $price = stripslashes($currData['pref']).sprintf("%.".$digits."f", $price).' '.stripslashes($currData['suf']);
        else
            $price = sprintf("%.".$digits."f", $price);
        return $price;
        
    } // end of function ShowPrice();    
   
    // ================================================================================================
    // Function : GetDefaultCurrency
    // Version : 1.0.0
    // Date : 26.09.2007
    //
    // Parms :  
    // Returns : value
    // Description : return default currency
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 26.09.2007 
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetDefaultCurrency()
    {
       $tmp_db = DBs::getInstance();
       $q = "SELECT `id` FROM `".TblSysCurrencies."` WHERE `is_default`='1'";
       $res = $tmp_db->db_Query($q);
       if( !$res OR ! $tmp_db->result) return false;
       $row = $tmp_db->db_FetchAssoc();
       return $row['id'];
    } // end of function GetDefaultCurrency();         

    
   function GetDefaultData()
    {
       $tmp_db = DBs::getInstance();
       $q = "SELECT `".TblSysCurrencies."`.*, 
                    `".TblSysCurrenciesSprName."`.`cod`,
                    `".TblSysCurrenciesSprName."`.`name`,
                    `".TblSysCurrenciesSprName."`.`pref`,
                    `".TblSysCurrenciesSprName."`.`suf`,
                    `".TblSysCurrenciesSprName."`.`short`
             FROM `".TblSysCurrencies."` , `".TblSysCurrenciesSprName."`
             WHERE `".TblSysCurrencies."`.is_default ='1' 
             AND `".TblSysCurrencies."`.id = `".TblSysCurrenciesSprName."`.cod 
             AND `".TblSysCurrenciesSprName."`.lang_id='".$this->lang_id."'";
       
       $res = $tmp_db->db_Query($q);
       if( !$res OR ! $tmp_db->result) return false;
       $row = $tmp_db->db_FetchAssoc();
       return $row;
    } // end of function GetDefaultData();
    
    // ================================================================================================
    // Function : GetCurrencyData
    // Date : 31.05.2010
    // Returns : value
    // Description : return array
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetCurrencyData($id)
    {
       $tmp_db = DBs::getInstance();
       $q = "SELECT * 
             FROM `".TblSysCurrencies."` , `".TblSysCurrenciesSprName."`
             WHERE `".TblSysCurrencies."`.`id`='".$id."' 
             AND `".TblSysCurrencies."`.id = `".TblSysCurrenciesSprName."`.cod 
             AND `".TblSysCurrenciesSprName."`.lang_id='".$this->lang_id."'";       
       $res = $tmp_db->db_Query($q);
       //echo '<br />$q='.$q.' $res='.$res;
       if( !$res OR ! $tmp_db->result) return false;
       $row = $tmp_db->db_FetchAssoc();
       return $row;
    } // end of function GetCurrencyData();             

   
    // ================================================================================================
    // Function : GetValue
    // Version : 1.0.0
    // Date : 26.09.2007
    //
    // Parms :  $id  - id of the currency
    // Returns : value
    // Description : return value of currency
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 26.09.2007 
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetValue($id)
    {
       $tmp_db = DBs::getInstance();
       $q = "SELECT `value`,`cashless` FROM `".TblSysCurrencies."` WHERE `id`='$id'";
       $res = $tmp_db->db_Query($q);
       if( !$res OR ! $tmp_db->result) return false;
       $row = $tmp_db->db_FetchAssoc();
       //echo '$row[value]='.$row['value'];
       return $row['value'];
    } // end of function GetValue(); 

    // ================================================================================================
    // Function : GetShortNamesInArray
    // Version : 1.0.0
    // Date : 26.09.2007
    //
    // Parms :  $front_back - 'front' or 'back'
    // Returns : value
    // Description : get short names f all currencies to array (for show in combobox or in radio)
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 26.09.2007 
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetShortNamesInArray($front_back = 'front')
    {
       if( is_array($this->listShortNames)) return $this->listShortNames;
       $tmp_db = DBs::getInstance();
       /*$q = "SELECT * FROM `".TblSysCurrencies."` WHERE 1";
       if( $front_back=='front') $q = $q." AND `visible`='2'";
       $q = $q." ORDER BY `move`";*/

       $q = "SELECT  
            `".TblSysCurrencies."`.*,    
            `".TblSysCurrenciesSprName."`.`short`    
            FROM `".TblSysCurrencies."`, `".TblSysCurrenciesSprName."` 
            WHERE `".TblSysCurrencies."`.`id` = `".TblSysCurrenciesSprName."`.`cod` 
            AND `".TblSysCurrenciesSprName."`.`lang_id` = '".$this->lang_id."'"; 
       if( $front_back=='front') $q .= " AND `".TblSysCurrencies."`.`visible`='2'";
       $q .= "ORDER BY `".TblSysCurrencies."`.`move` "; 
       
       $res = $tmp_db->db_Query($q);
       //echo "<br /> q = ".$q." res = ".$res;
       if( !$res OR ! $tmp_db->result) 
                return false;
       $rows = $tmp_db->db_GetNumRows();
       $this->listShortNames = array();
       for($i=0;$i<$rows;$i++){
           $row = $tmp_db->db_FetchAssoc();
           $this->listShortNames[$row['id']] = $row['short'];
           //$this->Spr->GetNameByCod(TblSysCurrenciesSprShort, $row['id'], $this->lang_id, 1);
       }
       return $this->listShortNames;
    } // end of function GetShortNamesInArray(); 

    // ================================================================================================
    // Function : ShowCurrFront
    // Version : 1.0.0
    // Date : 26.01.2010
    // Parms :  $front_back - 'front' or 'back'
    // Returns : value
    // Description :  Show Currensies on front
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ShowCurrFront($def_curr=5){
    //$def_curr = 5;
     $tmp_db = DBs::getInstance();
       /*$q = "SELECT * FROM `".TblSysCurrencies."` WHERE 1";
       $q = $q." AND `visible`='2' and `id`!='".$def_curr."'";
       $q = $q." ORDER BY `move`"; */
    
       $q = "SELECT  
                    `".TblSysCurrencies."`.*,    
                    `".TblSysCurrenciesSprName."`.short    
               FROM `".TblSysCurrencies."`, `".TblSysCurrenciesSprName."` 
               WHERE 
                    `".TblSysCurrencies."`.id = `".TblSysCurrenciesSprName."`.cod AND 
                    `".TblSysCurrenciesSprName."`.lang_id = ".$this->lang_id." AND 
                    `".TblSysCurrencies."`.visible ='2' AND
                    `".TblSysCurrencies."`.id !='".$def_curr."'
               ORDER BY 
                    `".TblSysCurrencies."`.move ";   
          
       $res = $tmp_db->db_Query($q);
       //echo "<br /> q = ".$q." res = ".$res;
       if( !$res OR ! $tmp_db->result) return false;
       $rows = $tmp_db->db_GetNumRows();
       $str = '';
       for($i=0; $i<$rows; $i++){
           $row = $tmp_db->db_FetchAssoc();
           //$str= $str."<div class='curs'><span> 1</span> ".$this->Spr->GetNameByCod(TblSysCurrenciesSprShort, $row['id'], $this->lang_id, 1).":  <span>".$this->Converting( $row['id'], $def_curr, 1)."</span> ".$this->Spr->GetNameByCod(TblSysCurrenciesSprShort, $def_curr, $this->lang_id, 1)."</div>";
           $str= $str."<div class='curs'><span> 1</span> ".$row['short'].":  <span>".$this->Converting( $row['id'], $def_curr, 1)."</span> ".$this->Spr->GetFieldByCod(TblSysCurrenciesSprName, 'short', $def_curr, $this->lang_id, 1)."</div>";
       }
       return $str;
    } // end of ShowCurrFront


                      
 }  //end of class SystemCurrencies
