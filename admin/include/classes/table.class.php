<?php
// ================================================================================================
//    System     : PrCSM05
//    Module     : AdminHTML
//    Version    : 1.0.0
//    Date       : 28.01.2005
//    Licensed To:
//                 Igor  Trokhymchuk  ihoru@mail.ru
//                 Andriy Lykhodid    las_zt@mail.ru
//
//    Purpose    : 
//
// ================================================================================================
// ================================================================================================
//
//    Programmer        :  Andriy Lykhodid
//    Date              :  31.01.2005
//    Reason for change :  Creation
//    Change Request Nbr:
//
//    Function          :  Class write tables
//
//  ================================================================================================
//
//    Restrictions/     :  *NONE
//    Limitations
//
// ================================================================================================
//    Class             : html_table
//    Version           : 1.0.0
//    Date              : 31.01.2005
//
//    Constructor       : Yes
//    Parms             :
//    Returns           : None
//    Description       : Class write html table
// ================================================================================================
//    Programmer        :  Andriy Lykhodid
//    Date              :  31.01.2005
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================

class html_table {
 var $border = NULL;
 var $align = NULL;
 var $width = NULL;
 var $cellspacing = NULL;
 var $cellpadding = NULL;
 var $css_class = NULL;

// ================================================================================================
//    Function          : html_table() (Constructor)
//    Version           : 1.0.0
//    Date              : 31.01.2005
//    Parms             :
//    Returns           :
//    Description       : Tables Designer (Show Form Header, Footer and Content)
// ================================================================================================

function html_table( $border = NULL, $align = NULL, $width = NULL, $cellspacing = NULL, $cellpadding = NULL )
{
 if( $border ) $this->border = $border;
 if( $align ) $this->align = $align;
 if( $width ) $this->width = $width;
 if( $cellspacing ) $this->cellspacing = $cellspacing;
 if( $cellpadding ) $this->cellpadding = $cellpadding;
}
// ================================================================================================
//    Function          : table_header()
//    Version           : 1.0.0
//    Date              : 31.01.2005
//    Parms             :
//    Returns           :
//    Description       : Table header
// ================================================================================================
function table_header()
{
echo '<table';
if( $this->border )echo ' border="'.$this->border.'"';
if( $this->align )echo ' align="'.$this->align.'"';
if( $this->width )echo ' width="'.$this->width.'"';
if( $this->cellspacing )echo ' cellspacing="'.$this->cellspacing.'"';
if( $this->cellpadding )echo ' cellpadding="'.$this->cellpadding.'"';
if( $this->css_class )echo ' class="'.$this->css_class.'"';
echo '>';
}
// ================================================================================================
//    Function          : table_footer()
//    Version           : 1.0.0
//    Date              : 31.01.2005
//    Parms             :
//    Returns           :
//    Description       : Table footer
// ================================================================================================

function table_footer()
{
?>
</table>
<?
}
// ================================================================================================
//    Function          : table_tr()
//    Version           : 1.0.0
//    Date              : 31.01.2005
//    Parms             :
//    Returns           :
//    Description       : Table <TR>
// ================================================================================================
function tr( $param = NULL )
{
?>
<tr <?if( $param )echo $param;?>>
<?
}
// ================================================================================================
//    Function          : table_td()
//    Version           : 1.0.0
//    Date              : 31.01.2005
//    Parms             :
//    Returns           :
//    Description       : Table <TD>
// ================================================================================================

function td( $colspan = NULL, $rowspan = NULL )
{
?>
<td <? if( $colspan )echo "colspan=$colspan"; if( $rowspan )echo "rowspan=$rowspan"; ?>>
<?
}
// ================================================================================================
//    Function          : table_th()
//    Version           : 1.0.0
//    Date              : 31.01.2005
//    Parms             :
//    Returns           :
//    Description       : Table <TH>
// ================================================================================================

function th( $colspan = NULL, $rowspan = NULL )
{
?>
<th <? if( $colspan )echo "colspan=$colspan"; if( $rowspan )echo "rowspan=$rowspan"; ?>>
<?
}

} // End of Page Class
?>