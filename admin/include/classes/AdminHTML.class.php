<?
// ================================================================================================
//    System     : PrCSM05
//    Module     : AdminHTML
//    Version    : 1.0.0
//    Date       : 28.01.2005
//    Licensed To:
//                 Igor  Trokhymchuk  ihoru@mail.ru
//                 Andriy Lykhodid    las_zt@mail.ru
//
//    Purpose    : Class definition for Admin Forms of Content System Management
//
// ================================================================================================



// ================================================================================================

//    Class             : AdminHTML
//    Version           : 1.0.0
//    Date              : 13.02.2005
//    Constructor       : Yes
//    Parms             :
//    Returns           : None
//    Description       : Admin HTML Module
// ================================================================================================
//    Programmer        :  Andriy Lykhodid
//    Date              :  13.02.2005
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================

class AdminHTML
{


// ================================================================================================
// Function : PanelMainH()
// Version : 1.0.0
// Date : 13.02.2005
// Parms :
// Returns :     Void
// Description : Echo Main Panel Header
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 13.02.2005
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================

 function PanelMainH( $img = NULL, $text = NULL )
 {
?>
    <div class="dPanelMainL">
        <div class="inner"><?=$text?></div>
    </div>
    <div class="ContentExect">
<?
 }


// ================================================================================================
// Function : PanelMainF()
// Version : 1.0.0
// Date : 13.02.2005
// Parms :
// Returns :     Void
// Description : Echo Main Panel Footer
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 13.02.2005
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================

 function PanelMainF()
 {
?>
 </div>
<?
 }


// ================================================================================================
// Function : PanelSubH()
// Version : 1.0.0
// Date : 13.02.2005
// Parms :
// Returns :     Void
// Description : Echo Sub Panel  Header
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 13.02.2005
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================

 function PanelSubH( $text, $property=NULL )
 {
?>
<div class="PanelSubH"><?=$text?></div>
<?
 }



// ================================================================================================
// Function : PanelSubF()
// Version : 1.0.0
// Date : 13.02.2005
// Parms :
// Returns :     Void
// Description : Echo Sub Panel  Footer
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 13.02.2005
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================

 function PanelSubF()
 {
?>
<?
 }


// ================================================================================================
// Function : PanelSimpleH()
// Version : 1.0.0
// Date : 13.02.2005
// Parms :
// Returns :     Void
// Description : Echo Simple Panel Header
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 13.02.2005
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================

 function PanelSimpleH()
 {
?>
<table border="0" width="100%" cellspacing="3" cellpadding="4" class="PanelSimpleL">
<?
 }


// ================================================================================================
// Function : PanelSimpleF()
// Version : 1.0.0
// Date : 13.02.2005
// Parms :
// Returns :     Void
// Description : Echo Simple Panel Footer
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 13.02.2005
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================

 function PanelSimpleF()
 {
?>
</table>
<div class="space"></div>
<?
 }


// ================================================================================================
// Function : TablePartH()
// Version : 1.0.0
// Date : 24.02.2005
// Parms :
// Returns :     Void
// Description : Echo Table Part Panel Header
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 24.02.2005
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================

 function TablePartH()
 {
?><table border="0" width="100%" cellspacing="0" cellpadding="0" class="ContentTable"><?
 }


// ================================================================================================
// Function : TablePartF()
// Version : 1.0.0
// Date : 24.02.2005
// Parms :
// Returns :     Void
// Description : Echo Table Part Panel Footer
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 24.02.2005
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================

 function TablePartF()
 {
?>
</table>
<?
 }
} //--- end of class