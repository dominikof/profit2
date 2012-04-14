<?
// ================================================================================================
// System : PrCSM05
// Module : sysAuthorization.class.php
// Version : 1.0.0
// Date : 09.02.2005
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose :
//
// ================================================================================================

// ================================================================================================
//    Class             : Panel
//    Version           : 1.0.0
//    Date              : 09.02.2005
//    Constructor       : Yes
//    Returns           : None
//    Description       : Panel
// ================================================================================================
//    Programmer        :  Andriy Lykhodid
//    Date              :  09.02.2005
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================

class Panel{


 function Panel()
 {
?>
<link id="luna-tab-style-sheet" type="text/css" rel="stylesheet" href="http://<?=NAME_SERVER?>/sys/js/tabpane/css/luna/tab.css" />
<script type="text/javascript" src="http://<?=NAME_SERVER?>/sys/js/tabpane/tabpane.js"></script>
<?
 }


 // ================================================================================================
 // Function : WritePanelHead()
 // Version : 1.0.0
 // Date : 09.02.2005
 // Parms :
 // Returns : true,false / Void
 // Description : Write Panels
 // ================================================================================================
 // Programmer : Andriy Lykhodid
 // Date : 09.02.2005
 // Reason for change : Reason Description / Creation
 // Change Request Nbr:
 // ================================================================================================


function WritePanelHead( $id = NULL )
{
?>
<div class="tab-pane" style="z-index:0;" <?if( $id ) echo 'id="'.$id.'"';?>>
<?
}


 // ================================================================================================
 // Function : WritePanelFooter()
 // Version : 1.0.0
 // Date : 09.02.2005
 // Parms :
 // Returns : true,false / Void
 // Description : Write Panel Footer
 // ================================================================================================
 // Programmer : Andriy Lykhodid
 // Date : 09.02.2005
 // Reason for change : Reason Description / Creation
 // Change Request Nbr:
 // ================================================================================================


function WritePanelFooter()
{
 ?>
</div>
 <?
}


 // ================================================================================================
 // Function : WriteItemHeader
 // Version : 1.0.0
 // Date : 09.02.2005
 // Parms :
 // Returns : true,false / Void
 // Description : Write Item Footer
 // ================================================================================================
 // Programmer : Andriy Lykhodid
 // Date : 09.02.2005
 // Reason for change : Reason Description / Creation
 // Change Request Nbr:
 // ================================================================================================


function WriteItemHeader( $name, $id=NULL )
{
?>
<div class="tab-page" <?if( $id ) echo 'id="'.$id.'"';?>>
<h4 class="tab"><?=$name?></h4>
<?
}


 // ================================================================================================
 // Function : WriteItemFooter
 // Version : 1.0.0
 // Date : 09.02.2005
 // Parms :
 // Returns : true,false / Void
 // Description : Write Item Footer
 // ================================================================================================
 // Programmer : Andriy Lykhodid
 // Date : 09.02.2005
 // Reason for change : Reason Description / Creation
 // Change Request Nbr:
 // ================================================================================================


function WriteItemFooter()
{
?>
</div>
<?
}

} // end of class
?>