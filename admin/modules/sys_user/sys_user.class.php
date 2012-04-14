<?php
/**
* Class UserBackend
* Class definition for all action with control of system users
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
class UserBackend  extends SysUser {

    public  $module = NULL;
    public  $sort = NULL;
    public  $display = 20;
    public  $start = 0;
    public  $width = 500;
    public  $fltr;
    public  $Err = NULL;

    public  $Msg = NULL;
    public  $Msg_text = NULL;
    public  $Rights = NULL;
    public  $db = NULL;
    public  $Form = NULL;

    /**
    * SysGroup::__construct()
    * 
    * @param integer $user_id
    * @param integer $module_id
    * @param integer $display
    * @param string $sort
    * @param integer $start
    * @param integer $width
    * @return void
    */
    function UserBackend($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width=NULL) {
            //Check if Constants are overrulled
            ( $user_id  !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
            ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
            ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
            ( $display  !="" ? $this->display = $display  : $this->display = 10   );
            ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
            ( $width    !="" ? $this->width   = $width    : $this->width   = '100%');

            if (empty($this->Rights)) $this->Rights = new Rights($this->user_id, $this->module);
            if (empty($this->db)) $this->db = new DB();
            //if (empty($this->Msg)) $this->Msg = new ShowMsg();
	        if (empty($this->Msg)) $this->Msg = &check_init('ShowMsg', 'ShowMsg');
            if (empty($this->Msg_text)) $this->Msg_text = &check_init_txt('TblBackMulti',TblBackMulti);
            if (empty($this->Form)) $this->Form = new Form('form_sys_user');
            //add field login_multi_use
            //$this->db->AutoInsertColumnToTable(TblSysUser, 'login_multi_use', 'int(1) unsigned default NULL');
            //add field alias
            //$this->db->AutoInsertColumnToTable(TblSysUser, 'alias', 'VARCHAR( 255 ) NOT NULL');

    } // End of UserBackend Constructor

       
       // ================================================================================================
       // Function : ShowSearchForm
       // Version : 1.0.0
       // Date : 07.06.2007
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Show search form 
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 07.06.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function ShowSysSearchForm()
       { 
	    /* Write Table Part */
	    AdminHTML::TablePartH();
	    //phpinfo();
	    //$this->Form->WriteHeader( $this->script );
	    if(empty($this->srch_dtfrom)) {
		//$DateCalc = new Date_Calc();
		//$tmp_dt = explode("-",date("Y-m-d"));
		//$this->srch_dtfrom = $DateCalc->beginOfPrevWeek($tmp_dt[2], $tmp_dt[1], $tmp_dt[0],"%Y-%m-%d");  
		$this->srch_dtfrom = '2000-01-01';
	    }
	    if(empty($this->srch_dtto)) {
		$this->srch_dtto = date("Y-m-d");  
	    }
	    ?>
	    <form name="search_form" action="<?=$this->script;?>" method="post"  title="<?=$this->Msg->show_text('TXT_SEARCH_FORM');?>">
        <?$this->Form->Hidden('display', "20");?>
		<table border="0" cellpadding="2" cellspacing="1" width="350">
		    <tr><td><h4><?=$this->Msg->show_text('TXT_SEARCH_FORM');?></h4></td></tr>
		    <tr class="tr1">
			<td align="right"><?=$this->Msg->show_text('FLD_USER_LOGIN').',<br/>'.$this->Msg->show_text('_FLD_ALIAS');?></td>
			<td align="left"><?$this->Form->TextBox('srch', $this->srch, 30);?></td>  
		    </tr>
		    <tr class="tr2">
			<td align="right"><?=$this->Msg->show_text('_FLD_ENROL_DATE')?></td>
			<td align="left"><?=$this->Msg->show_text('TXT_ENROLE_DATE_FROM'); $this->Form->TextBox('srch_dtfrom', $this->srch_dtfrom, 10);?>- <?=$this->Msg->show_text('TXT_ENROLE_DATE_TO'); $this->Form->TextBox('srch_dtto', $this->srch_dtto, 10);?></td>  
		    </tr>
		    <tr class="tr1">
			<td align="right"><?=$this->Msg->show_text('_FLD_GROUP');?></td>
			<td align="left">
			    <?
			    $logon = &check_init('logon','Authorization');
			    $q_spr = "select * from `".TblSysGroupUsers."` WHERE 1 AND `id`>='".$logon->user_type."' AND `adm_menu`='1'";                   
			    $res_spr = $this->Rights->Query($q_spr, $this->user_id, $this->module);
			    //echo '<br>$q_spr='.$q_spr;
			    $rows_spr = $this->Rights->db_GetNumRows();
			    $mas_tmp['']='';
			    for($i=0; $i<$rows_spr; $i++){
			    $row_spr=$this->Rights->db_FetchAssoc();
			    $mas_tmp[$row_spr['id']]=$row_spr['name'];
			    }
			    if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->group_id : $val=$row['group_id'];
			    else $val=$this->group_id;           
			    $this->Form->Select( $mas_tmp, 'fltr2', $this->fltr2 );
			    ?>
			</td>  
		    </tr>
		    <tr class="tr1">
			<td colspan="2" align="center"><?$this->Form->Button( '', $this->Msg->show_text('TXT_BUTTON_SEARCH'), 50 );?></td>
		    <tr>
		</table>
	    </form>
	    <br />
	    <?
	    //$this->Form->WriteFooter();
	    AdminHTML::TablePartF();
             
       } //end of fuinction ShowSearchForm()       
             
       
    
    // ================================================================================================
    // Function : show
    // Version : 1.0.0
    // Date : 09.01.2005
    //
    // Parms :         $user_id  / user ID
    //                 $module   / Module read  / Void
    //                 $display  / How many records to show / Void
    //                 $sort     / Sorting data / Void
    //                 $start    / First record for show / Void
    // Returns : true,false / Void
    // Description : Show users
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 09.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function show( $user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL)
    {
        if( $user_id ) $this->user_id = $user_id;
        if( $module ) $this->module = $module;
        if( $display ) $this->display = $display;
        if( $sort ) $this->sort = $sort;
        if( $start ) $this->start = $start;

        $logon = &check_init('logon','Authorization'); 

        $scriptact = 'module='.$this->module;
        $scriplink = $_SERVER['PHP_SELF']."?$scriptact";
        if( empty($this->sort) ) $this->sort='id';
        $q="
	    SELECT `".TblSysUser."`.*, `".TblSysGroupUsers."`.name 
	    FROM `".TblSysUser."`,`".TblSysGroupUsers."` 
	    WHERE `".TblSysGroupUsers."`.id=`".TblSysUser."`.group_id ";
	    if( $this->srch ) $q .=" AND (`login` LIKE '%".$this->srch."%' OR `alias` LIKE '%".$this->srch."%')";
	    if( $this->fltr2 ) $q .= " AND `group_id`='".$this->fltr2."'";
	    if( $this->srch_dtfrom ) $q .= " AND `".TblSysUser."`.enrol_date>='".$this->srch_dtfrom."'";
	    if( $this->srch_dtto ) $q .= " AND `".TblSysUser."`.enrol_date<='".$this->srch_dtto."'";
	    if ( $this->fltr ) $q=$q." AND `group_id`='".$this->fltr."'";
	    $q .= " AND `group_id`>='".$logon->user_type."' AND `".TblSysGroupUsers."`.`adm_menu`='1'";
	    $q = $q." order by `".$this->sort."`";
        
        //$res = $this->Rights->Query($q, $this->user_id, $this->module);
        // select (R)
        $result = $this->Rights->QueryResult( $q, $this->user_id, $this->module );
        //echo '<br> $q='.$q.' $this->Rights->result='.$this->Rights->result;
        $rows = count($result);
	
	    $this->ShowSysSearchForm();
        /* Write Form Header */
        $this->Form->WriteHeader( $scriplink );

        /* Write Table Part */
        AdminHTML::TablePartH();

        ?><tr><td colspan="17" align="center">
        <?
        //$this->Form->WriteLinkPages( $scriplink.'&fltr='.$this->fltr, $rows, $this->display, $this->start, $this->sort );
        $this->Form->WriteLinkPages( $this->script, $rows, $this->display, $this->start, $this->sort );
        ?>
        
        <div class="topPanel"><div class="SavePanel"><?
        /* Write Links on Pages */
        $scriplink = $scriplink.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort;
        $this->Form->WriteTopPanel( $scriplink.'&fltr='.$this->fltr);
        ?></div><div class="SelectType"><?
        /* Write Top Panel (NEW,DELETE - Buttons) */
//        $this->GroupFLTR($scriplink, $logon->user_type);
//        $scriplink = $scriplink.'&fltr='.$this->fltr;
        ?> </div>
        </div>
         </td>
         </tr>
         <tr>
         <Th class="THead">*</Th>
         <Th class="THead"><? $this->Form->Link($this->script."&sort=id", $this->Msg_text['FLD_ID']);?></Th>
         <Th class="THead"><? $this->Form->Link($this->script."&sort=login", $this->Msg_text['FLD_LOGIN']);?></Th>
         <Th class="THead"><? $this->Form->Link($this->script."&sort=alias", $this->Msg_text['_FLD_ALIAS']);?></Th>
         <Th class="THead"><? echo $this->Msg_text['_FLD_CHANGE_PASSWORD'];?></Th>   
         <Th class="THead"><? $this->Form->Link($this->script."&sort=group_id", $this->Msg_text['FLD_GROUP']);?></Th>
         <Th class="THead"><? $this->Form->Link($this->script."&sort=active", $this->Msg_text['FLD_STATUS']);?></Th>
         <Th class="THead"><?=$this->Msg_text['_FLD_MULTI_USE']?></Th>
         <Th class="THead"><? $this->Form->Link($this->script."&sort=enrol_date", $this->Msg_text['_FLD_ENROL_DATE']);?></Th>
         <Th class="THead"><? $this->Form->Link($this->script."&sort=last_active_counter", $this->Msg_text['_FLD_LAST_ACT_COUNTER']);?></Th>
         <Th class="THead"><? $this->Form->Link($this->script."&sort=used_counter", $this->Msg_text['_FLD_USED_COUNTER']);?></Th>

          <?
          $a=$rows;
          for( $i = 0; $i < $rows; ++$i )
          {
            $row = $result[$i];
            if( $i >=$this->start && $i < ( $this->start+$this->display ) )
            {
               if ( (float)$i/2 == round( $i/2 ) ) $class="TR1";
               else $class="TR2";
               ?>
               <tr class="<?=$class;?>">
                <td><?=$this->Form->CheckBox( "id_del[]", $row['id'] );?></td>
                <td><?=$this->Form->Link( "$scriplink&task=edit&id=".stripslashes($row['id']), stripslashes($row['id']), $this->Msg_text['TXT_EDIT']);?></td>
                <td><?=stripslashes($row['login']);?></td>
                <td><?=stripslashes($row['alias']);?></td>
                <td><?=$this->Form->Link("$scriplink&task=newpass&id=".$row['id'], '&nbsp;&nbsp;'.$this->Msg_text['_FLD_CHANGE_PASSWORD'].'&nbsp;&nbsp');?></td>
                <?
                /*$q1 = "select * from `".TblSysGroupUsers."` where id='".$row['group_id']."'";
                $res1 = mysql_query($q1);
                if( !$res1 ) return false;
                $mas_g = mysql_fetch_array($res1);*/
                echo '<TD>'.stripslashes($row['name']).'</TD>';

                if ($row['active']==1) $user_status=$this->Msg_text['_FLD_USER_ONLINE'];
                else $user_status='-'; //$this->Msg_text['_FLD_USER_OFFLINE'];
                ?>
                <td><?=$user_status;?></td>
                <?
                if ( $row['login_multi_use']==1 ) $user_multi_use='Multi Use';
                else $user_multi_use='Unique Use';
                ?>
                <td><?=$user_multi_use;?></td>               
                <td><?=stripslashes($row['enrol_date']);?></td>
                <td><?=$row['last_active_counter'];?></td>
                <?
                $ModulesPlug = new ModulesPlug();
                $id_mod_stat = $ModulesPlug->GetModuleIdByPath( '/admin/modules/sys_stat/stat.php' );               
                ?>
                <td><?=$row['used_counter'];?><br />
                <a href="<?=$_SERVER['PHP_SELF']?>?module=<?=$this->module;?>&amp;task=show_stat&amp;fltr_user=<?=$row['id'];?>"><?=$this->Msg_text['FLD_IP_STATISTIC'];?></a>
                <br /><a href="<?=$_SERVER['PHP_SELF']?>?module=<?=$id_mod_stat;?>&amp;fltr_dtfrom=2000-01-01&amp;fltr_dtto=2100-01-01&amp;fltr_user=<?=$row['id'];?>"><?=$this->Msg_text['TXT_DETAIL_STATISTIC'];?></a>
                </td> 
                <?
                $a=$a-1;
            }
          }
          ?>
        </table>
        <?
        AdminHTML::TablePartF();
        /* Write Form Footer */
        $this->Form->WriteFooter();
        return true;
    } //end of fuinction show

    // ================================================================================================
    // Function : edit
    // Version : 1.0.0
    // Date : 09.01.2005
    //
    // Parms :         $module     / Module read  / Void
    //                 $id         / id of the record in table / Void
    //                 $group      / group id of the user / Void
    //                 $login      / user's login / Void
    //                 $change_pas / id of the record in table / Void
    // Returns : true,false / Void
    // Description : Show data for editing
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 09.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function edit()
    {
        $logon = &check_init('logon','Authorization');
        $Panel = new Panel();

        /* set action page-adress with parameters */
        $scriptact = $this->script;

        if( $this->id!=NULL ){
            $q="select * from `".TblSysUser."` where id='".$this->id."'";
            // edit (U)
            $res = $this->Rights->Query($q, $this->user_id, $this->module);
            if( !$res OR !$this->Rights->result ) return false;
            $row = $this->Rights->db_FetchAssoc();
        }

        /* Write Form Header */
        $this->Form->WriteHeader( $scriptact );

        $this->Form->Hidden( 'sort', $this->sort );
        $this->Form->Hidden( 'fltr', $this->fltr );
        $this->Form->Hidden( 'display', 20 );
        $this->Form->Hidden( 'start', $this->start );

        if( $this->id!=NULL ) $txt = $this->Msg_text['TXT_EDIT'];
        else $txt = $this->Msg_text['_TXT_ADD_DATA'];

        AdminHTML::PanelSubH( $txt );

        $this->ShowErrBackEnd();
        
        /* Write Simple Panel*/
        AdminHTML::PanelSimpleH();
        ?>
         <tr>
          <td><b><?=$this->Msg_text['FLD_ID'];?></b></td>
          <td>
           <?
           if( $this->id!=NULL ){
               echo $row['id'];
               $this->Form->Hidden( 'id', $row['id'] );
           }
           ?>
          </td>
         </tr>
         <tr>
          <td><b><?=$this->Msg_text['FLD_GROUP'];?>:<span class="red">*</span></b></td>
          <td>
           <?
           $q_spr = "select * from `".TblSysGroupUsers."` WHERE 1 AND `id`>='".$logon->user_type."' AND `adm_menu`='1'";                   
           $res_spr = $this->Rights->Query($q_spr, $this->user_id, $this->module);
           //echo '<br>$q_spr='.$q_spr;
           $rows_spr = $this->Rights->db_GetNumRows();
           if($rows_spr>1) $mas_tmp['']='';
           for($i=0; $i<$rows_spr; $i++){
               $row_spr=$this->Rights->db_FetchAssoc();
               $mas_tmp[$row_spr['id']]=$row_spr['name'];
           }
           if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->group_id : $val=$row['group_id'];
           else $val=$this->group_id;           
           $this->Form->Select( $mas_tmp, 'group_id', $val );
           ?>
          </td>
         </tr>
         <?
         $url = '/admin/modules/sys_user/sys_user.php?'.$this->script_ajax;
         $formname=$this->Form->name;         
         ?>
         <tr>
          <td><b><?=$this->Msg_text['FLD_LOGIN'];?>:<span class="red">*</span></b></td>
          <td>
           <?
           if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->login : $val=$row['login'];
           else $val=$this->login;
           $this->Form->Hidden('old_login', stripslashes($val));
           $params = " onBlur=\"if(document.$formname.old_login.value!=document.$formname.login.value) isLoginAlias('".$url."', 'islogin', 'login_checkup')\" ";
           $this->Form->TextBox('login', stripslashes($val), 50, $params ); ?>&nbsp;<? $this->Form->ButtonSimple( 'check_login', $this->Msg_text['_BTN_CHECK_UP'], NULL, "onClick=\"if(document.$formname.login.value!='') isLoginAlias('".$url."&check_up=1', 'islogin', 'login_checkup')\"" ); /*?><input type="button" name="check_login" id="check_login" value="Проверить" onclick="islogin('<?=$url;?>', 'islogin')"><?*/
           ?>
           <br />
           <div id="islogin"></div>
          </td>
         </tr>
         <tr>
          <td><b><?=$this->Msg_text['_FLD_ALIAS'];?>:</b></td>
          <td>
           <?
           if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->alias : $val=$row['alias'];
           else $val=$this->alias;
           if( $this->id!=NULL ) $this->Form->Hidden('old_alias', stripslashes($val));
           //$params = " onBlur=\"if(document.$formname.alias.value!='' AND document.$formname.old_alias.value!=document.$formname.alias.value) isLoginAlias('".$url."', 'isalias', 'alias_checkup')\" ";
           $this->Form->TextBox('alias', stripslashes($val), 50); ?>&nbsp;<? //$this->Form->ButtonSimple( 'check_alias', $this->Msg_text['_BTN_CHECK_UP'], NULL, "onClick=\"if(document.$formname.alias.value!='') isLoginAlias('".$url."&check_up=1', 'isalias', 'alias_checkup')\"" );
           ?>
          </td>
          <td>
           <div id="isalias"></div>
          </td>          
         </tr>
         <?
         if( $this->id!=NULL ){
         ?>
         <tr>
          <td><b><?=$this->Msg_text['FLD_PASSWORD'];?>:</b></td>
          <td>
           <?
           if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->pass : $val=$row['pass'];
           else $val=$this->pass;   
           if( $this->IsEncodePass($row['login']) ) echo '*** PASSWORD ENCODE ***';
           else echo $val;
           ?>&nbsp;&nbsp;<?$this->Form->Link($this->script."&task=newpass&id=".$row['id'], $this->Msg_text['_FLD_CHANGE_PASSWORD']);
           $this->Form->Hidden( 'pass', $row['pass'] );
           $this->Form->Hidden( 'confir_pass', $row['pass'] );
           ?>
          </td>
         </tr>
         <?
         }
         else{
         ?>
         <tr>
          <td><b><?=$this->Msg_text['FLD_PASSWORD'];?>:<span class="red">*</span></b></td>
          <td>
           <?
           if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->pass : $val=$row['pass'];
           else $val=$this->pass;           
           $this->Form->TextBox('pass', $val, 50);
           ?>
          </td>
         </tr>          
         <tr>
          <td><b><?=$this->Msg_text['_FLD_CONFIRM_PASSWORD'];?>:<span class="red">*</span></b></td>
          <td>
           <?
           if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->confirm_pass : $val=$row['pass'];
           else $val=$this->confirm_pass;              
           $this->Form->TextBox('confirm_pass', $val, 50);
           ?>
          </td>
         </tr>
         <?
         }
         ?>
         <tr>
          <td><b><?=$this->Msg_text['_FLD_MULTI_USE'];?>:<span class="red">*</span></b></td>
          <td>
           <?  
           $mas_tmp1[0] = $this->Msg_text['TXT_UNIQUE_USE'];
           $mas_tmp1[1] = $this->Msg_text['TXT_MULTIPLE_USE'];
           if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->login_multi_use : $val=$row['login_multi_use'];
           else $val=$this->login_multi_use;
           $this->Form->Select( $mas_tmp1, 'login_multi_use', stripslashes($val) );  
           ?>                      
          </td>
         </tr>      
         <tr>
          <td><b><?=$this->Msg_text['_FLD_ENROL_DATE'];?></b></td>
          <td>
           <?
           if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->enrol_date : $val = $row['enrol_date'];
           else $val = date('Y-m-d');
           $this->Form->TextBox('enrol_date', stripslashes($val), 10);
           /*
           echo $row['enrol_date'];
           $this->Form->Hidden( 'enrol_date', $row['enrol_date'] );
           $this->Form->Hidden( 'pass', $row['pass'] );
           $this->Form->Hidden( 'confirm_pass', $row['pass'] );
           */
           ?>
          </td>
         </tr>
         <tr>
            <td colspan="2"><table><tr><td><img src="images/icons/warning.png" alt="" title="" border="0"></td><td class="warning"><?=$this->Msg_text['_MSG_OBLIGATORY_FOR_FILLING'];?></td></tr></table></td>
         </tr>
        <?  
        AdminHTML::PanelSimpleF();
        $this->Form->WriteSavePanel( $scriptact );
        $this->Form->WriteCancelPanel( $scriptact ); 
        AdminHTML::PanelSubF();
        $this->Form->WriteFooter();
        ?>
        <script language="JavaScript"> 
         function isLoginAlias(uri, div_id, task){
             add_task = $("#<?=$this->Form->name?>").serialize();
             $.ajax({
                    type: "POST",
                    data: add_task+"&task="+task,
                    url: uri,
                    success: function(msg){
//                    alert(msg);
                    $("#"+div_id).html( msg );
                } });
         }
        </script>
        <?
        return true;
    } //end of fuinction edit

    // ================================================================================================
    // Function : change_pass_form
    // Version : 1.0.0
    // Date : 17.01.2005
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Change password
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 17.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function change_pass_form()
    {
         /* set action page-adress with parameters */
         $scriptact = $this->script.'&change_pass='.$this->change_pass;

         $q="select * from `".TblSysUser."` where `id`='".$this->id."'";
         // edit (U)
         $res = $this->Rights->Query($q, $this->user_id, $this->module);
         if( !$res ) return false;
         $row = $this->Rights->db_FetchAssoc();

         /* Write Form Header */
         $this->Form->WriteHeader( $scriptact );

         $this->Form->Hidden( 'sort', $this->sort );
         $this->Form->Hidden( 'fltr', $this->fltr );
         $this->Form->Hidden( 'display', $this->display );
         $this->Form->Hidden( 'start', $this->start );
         
         $this->Form->Hidden( 'id', $row['id'] );
         $this->Form->Hidden( 'group_id', $row['group_id'] );
         $this->Form->Hidden( 'login', $row['login'] );

         $txt = $this->Msg_text['_FLD_CHANGE_PASSWORD'];
         AdminHTML::PanelSubH( $txt );

         $this->ShowErrBackEnd();
         
         /* Write Simple Panel*/
         AdminHTML::PanelSimpleH();
         ?>
          <tr>
           <td><b><?=$this->Msg_text['FLD_LOGIN'];?>:</b></td>
           <td><?=$row['login'];?></td>
          </tr>
          <tr>
           <td><b><?=$this->Msg_text['_FLD_CURRENT_PASSWORD'];?>:</b></td>
           <td>
            <?
            if( $this->IsEncodePass($row['login']) ) echo '*** PASSWORD ENCODE ***';
            else echo $row['pass'];
            ?>
           </td>
          </tr>
          <tr>
           <td><b><?=$this->Msg_text['_FLD_NEW_PASSWORD'];?>:<span class="red">*</span></b></td>
           <td><?$this->Form->Password( 'pass', '', 20 ); ?></td>
          </tr>
          <tr>
           <td><b><?=$this->Msg_text['_FLD_CONFIRM_PASSWORD'];?>:<span class="red">*</span></b></td>
           <td><?$this->Form->Password( 'confirm_pass', '', 20 );?></td>
          </tr>
          <tr>
            <td colspan="2"><table><tr><td><img src="images/icons/warning.png" alt="" title="" border="0"></td><td class="warning"><?=$this->Msg_text['_MSG_OBLIGATORY_FOR_FILLING'];?></td></tr></table></td>
          </tr>  
         <?
        AdminHTML::PanelSimpleF();
        $this->Form->WriteSavePanel( $scriptact );
        $this->Form->WriteCancelPanel( $scriptact );
        AdminHTML::PanelSubF();
        $this->Form->WriteFooter();
        return true;
    } //end of fuinction change_pass_form

   // ================================================================================================
   // Function : CheckFields()
   // Version : 1.0.0
   // Date : 23.04.2008
   //
   // Parms : 
   // Returns :      true,false / Void
   // Description :  Checking all fields for filling and validation
   // ================================================================================================
   // Programmer :  Igor Trokhymchuk
   // Date : 23.04.2008
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function CheckFields()
   {
       $this->Err=NULL;

       if( empty($this->group_id) ) $this->Err = $this->Err.$this->Msg_text['_EMPTY_GROUP_FIELD'].'<br>';   
       
       if( empty($this->login) ) $this->Err = $this->Err.$this->Msg_text['_EMPTY_LOGIN_FIELD'].'<br>';
       else{
           if( $this->old_login!=$this->login AND !$this->unique_login($this->login) ) $this->Err=$this->Err.$this->Msg_text['FLD_LOGIN'].' "'.stripslashes($this->login).'" '.$this->Msg_text['MSG_NOT_UNIQUE_LOGIN2'].'<br>';
       }      
       
       //if( empty($this->alias) ) $this->Err = $this->Err.$this->Msg->show_text('_EMPTY_ALIAS_FIELD', TblSysMsg).'<br>';
       //else{
           //if( $this->old_alias!=$this->alias AND !$this->unique_alias($this->alias) ) $this->Err=$this->Err.$this->Msg->show_text('MSG_NOT_UNIQUE_ALIAS', TblSysMsg).' "'.$this->alias.'" '.$this->Msg->show_text('MSG_NOT_UNIQUE_ALIAS2', TblSysMsg).'<br>';
       //} 
       
       if( !$this->id ) $this->CheckPassFieldsSysUser($this->login, $this->pass, $this->confirm_pass);
       /*
       if( empty( $this->pass ) ) $this->Err = $this->Err.$this->Msg->show_text('MSG_FLD_PASSWORD_EMPTY', TblSysMsg).'<br>';         
       if( empty( $this->confirm_pass ) ) $this->Err = $this->Err.$this->Msg->show_text('MSG_FLD_CONFIRM_PASSWORD_EMPTY', TblSysMsg).'<br>';
       else {
           if ( $this->pass!=$this->confirm_pass ) $this->Err = $this->Err.$this->Msg->show_text('MSG_NOT_MATCH_CONFIRM_PASSWORD', TblSysMsg).'<br>';
       }
       */         
       return $this->Err;
   } //end of fuinction CheckFields()      
    
    // ================================================================================================
    // Function : CheckPassFieldsSysUser()
    // Version : 1.0.0
    // Date : 30.01.2006
    //
    // Parms :
    // Returns :      true,false / Void
    // Description :  Check fields of password for validation
    // ================================================================================================
    // Programmer :  Igor Trokhymchuk
    // Date : 30.01.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function CheckPassFieldsSysUser($login, $new_pass, $new_pass2)
    {
        //$logon = new  UserAuthorize(); 
        //$this->Err=NULL;
       if( empty( $new_pass ) ) $this->Err = $this->Err.$this->Msg_text['MSG_FLD_PASSWORD_EMPTY'].'<br>';         
       if( empty( $new_pass2 ) ) $this->Err = $this->Err.$this->Msg_text['MSG_FLD_CONFIRM_PASSWORD_EMPTY'].'<br>';
       else {
           if ( $new_pass!=$new_pass2 ) $this->Err = $this->Err.$this->Msg_text['MSG_NOT_MATCH_CONFIRM_PASSWORD'].'<br>';
       }  
       return $this->Err; 
    } //end of fuinction CheckPassFieldsSysUser()        

    // ================================================================================================
    // Function : save
    // Version : 1.0.0
    // Date : 08.01.2005
    // Parms :
    // Returns : true,false / Void
    // Description : Store data to the table
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 09.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function save()
    {
        $q="select `id` from `".TblSysUser."` where `id`='".$this->id."'";
        $res = $this->Rights->Query($q, $this->user_id, $this->module);
        if( !$res ) return false;
        $rows = $this->Rights->db_GetNumRows();

        if($rows>0){  //update (U)
          if (empty($this->pass)) $q="UPDATE `".TblSysUser."` SET `group_id`='".$this->group_id."', `login`='".$this->login."', `login_multi_use`='".$this->login_multi_use."', `alias`='".$this->alias."' WHERE id='".$this->id."'";
          else $q="UPDATE `".TblSysUser."` SET `group_id`='".$this->group_id."', `login`='".$this->login."', `pass`='".$this->pass."', `login_multi_use`='".$this->login_multi_use."', `alias`='".$this->alias."' WHERE id='".$this->id."'";
          //echo '<br>'.$q.' $res='.$res.' $this->Right->result='.$this->Rights->result; 
          $res = $this->Rights->Query($q, $this->user_id, $this->module);
          if( !$res ) return false;
        }
        else{ //insert (W)
          $pass = $this->EncodePass($this->login, $this->pass, $this->group_id);
            
          $q="select `id` from `".TblSysUser."` where id='".$this->id."'";
          $res = $this->Rights->Query($q, $this->user_id, $this->module);
          if( !$res ) return false;
          if($rows>0) return 0;

          $q="INSERT INTO `".TblSysUser."` SET
              `group_id`='".$this->group_id."',
              `login`='".$this->login."',
              `pass`='".$pass."',
              `enrol_date`='".$this->enrol_date."',
              `login_multi_use`='".$this->login_multi_use."',
              `alias`='".$this->alias."'
              ";
          $res = $this->Rights->Query($q, $this->user_id, $this->module);
          //echo '<br>'.$q.' $res='.$res.' $this->Right->result='.$this->Rights->result;
          if( !$res ) return false;
        }
        return true;
    } //end of fuinction save


    // ================================================================================================
    // Function : del
    // Version : 1.0.0
    // Date : 09.01.2005
    //
    // Parms :         $user_id  / user ID
    //                 $module   / Module read  / Void
    //                 $id_del   / array of the records which must be deleted / Void
    // Returns : true,false / Void
    // Description : Remove data from the table
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 09.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function del($id_del)
    {
       $kol = count( $id_del );
       $del = 0;
       for( $i=0; $i<$kol; $i++ )
       {
        $u = $id_del[$i];
        $q = "SELECT * FROM ".TblSysUser." WHERE `id`='".$u."'";
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
        $row = $this->Rights->db_FetchAssoc();
        //$cod = $row['cod'];

        if(defined("MOD_USER")){
            $q="DELETE FROM `".TblModUser."` WHERE `email`='".$this->GetUserLoginByUserId($u)."'";
            $res = $this->Rights->Query( $q, $this->user_id, $this->module );
        }
        
        $q="DELETE FROM `".TblSysUser."` WHERE `id`='".$u."'";
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );

        if ( $res )
         $del=$del+1;
        else
         return false;
       }
     return $del;
    } //end of fuinction del()

    // ================================================================================================
    // Function : GroupFLTR
    // Version : 1.0.0
    // Date : 01.03.2005
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Show the Combobox with group of the users
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 01.03.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GroupFLTR($scriplink, $fltr = NULL)
    {
      $DB = DBs::getInstance();
      
      $q = "select `id`,`name` from `".TblSysGroupUsers."` WHERE 1";
      if ( !empty($fltr) ) $q = $q." AND `id`>='$fltr'"; 
      $res = $DB->db_Query($q);
      //echo '<br>$q='.$q;
      if( !$DB->result ) return false;
      $rows =  $DB->db_GetNumRows();

      $arr['']='';
      for( $i = 0; $i < $rows; $i++ )
      {
       $row1 = $DB->db_FetchAssoc();
       $arr[$row1['id']] = $row1['name'];
      }
       $this->Form->SelectAct( $arr, 'fltr', $this->fltr, "onChange=\"location='$scriplink&fltr='+this.value\"" );
    }

    // ================================================================================================
    // Function : ShowStatByUserId
    // Version : 1.0.0
    // Date : 15.06.2007
    //
    // Parms :         $sec / Module read  / Void
    // Returns : true,false / Void
    // Description : show statistic of log on by user ID
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 15.01.2007
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowStatByUserId($user_id)
    {
        $this->script = $this->script.'&amp;task=show_stat';
        
        if( !$this->sort ) $this->sort='id';
        $q = "SELECT `".TblSysUserStat."`.*,`".TblSysUser."`.login FROM `".TblSysUserStat."`,`".TblSysUser."` WHERE `".TblSysUserStat."`.user_id=`".TblSysUser."`.id ";
        if( !empty($this->fltr_user) ) $q = $q." AND `user_id`='$this->fltr_user'";
        $q = $q." ORDER BY `$this->sort` desc";
        $result = $this->Rights->QueryResult( $q );
//        echo '<br>'.$q.' $this->Rights->result='.$this->Rights->result; 
         $rows = count($result);
        /* Write Form Header */
        $this->Form->WriteHeader( $this->script );            
        
        /* Write Table Part */
        AdminHTML::TablePartH();            
        
    /* Write Links on Pages */
    echo '<TR><TD COLSPAN=17>';
    $script1 = 'module='.$this->module.'&amp;task=show_stat&amp;fltr='.$this->fltr;
    $script1 = $_SERVER['PHP_SELF']."?$script1";
    $this->Form->WriteLinkPages( $script1, $rows, $this->display, $this->start, $this->sort );

    $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr;
    $script2 = $_SERVER['PHP_SELF']."?$script2";
    
    ?>
    <TR>
    <td class="THead">*</Th>
    <td class="THead"><A HREF=<?=$this->script?>&sort=id><?=$this->Msg_text['FLD_ID']?></A></Th>
    <td class="THead"><A HREF=<?=$this->script?>&sort=user_id><?=$this->Msg_text['FLD_LOGIN']?></A></Th>
    <td class="THead" width="70"><A HREF=<?=$this->script?>&sort=dt><?=$this->Msg_text['FLD_LOGIN_DATE']?></A></Th>
    <td class="THead"><A HREF=<?=$this->script?>&sort=tm><?=$this->Msg_text['FLD_LOGIN_TIME']?></A></Th>
    <td class="THead"><A HREF=<?=$this->script?>&sort=ip_user><?=$this->Msg_text['FLD_IP_USER_COMP']?></A></Th>
    <td class="THead"><A HREF=<?=$this->script?>&sort=ip_remote_server><?=$this->Msg_text['FLD_IP_REMOTE_SERVER']?></A></Th>
    <td class="THead"><A HREF=<?=$this->script?>&sort=hostname><?=$this->Msg_text['FLD_HOSTNAME']?></A></Th>
    <td class="THead"><A HREF=<?=$this->script?>&sort=agent><?=$this->Msg_text['SYS_STAT_USER_AGENT']?></A></Th>
    <?


    $a = $rows;
    $j = 0;
    $row_arr = NULL;
    for( $i = 0; $i < $rows; ++$i )
    {
      $row = $result[$i];
      if( $i >= $this->start && $i < ( $this->start+$this->display ) )
      {
        $row_arr[$j] = $row;
        $j = $j + 1;
      }
    }
     
    $style1 = 'TR1';
    $style2 = 'TR2';

    for( $i = 0; $i < count( $row_arr ); $i++ )
    {
      $row = $row_arr[$i];

      if ( (float)$i/2 == round( $i/2 ) )
      {
       echo '<tr class="',$style1,'">';
      }
      else echo '<tr class="',$style2,'">';

      ?>
      <td><?=$this->Form->CheckBox( "id_del[]", $row['id'] );?></td>
      <td><?=$row['id'];?></td>
      <td><?=$row['login'];?></td>
      <td><?=$row['dt'];?></td>
      <td><?=$row['tm'];?></td>
      <td><?=$row['ip_user'];?></td>
      <td><?=$row['ip_remote_server'];?></td>
      <td><?=$row['hostname'];?></td>
      <td><?=$row['agent'];?></td>
      <?
    } //-- end for

    AdminHTML::TablePartF();
    $this->Form->WriteFooter();

    } // End of ShowStatByUserId()
    
    // ================================================================================================
    // Function : ShowErrBackEnd()
    // Version : 1.0.0
    // Date : 10.01.2006
    //
    // Parms :
    // Returns :      true,false / Void
    // Description :  Show errors
    // ================================================================================================
    // Programmer :  Igor Trokhymchuk
    // Date : 10.01.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowErrBackEnd()
    {
     if ($this->Err){
        ?>
        <fieldset class="err" title="<?=$this->Msg_text['MSG_ERRORS'];?>"> <legend><?=$this->Msg_text['MSG_ERRORS'];?></legend>
        <div class="err_text"><?=$this->Err;?></div>
        </fieldset>
        <?
     }
    } //end of fuinction ShowErrBackEnd()                

 } // End of class UserBackend
?>
