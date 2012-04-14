///// menu left open and clode 
//// BEGIN
function flip_div( nname )
{
        if ( $('#'+nname).css('display') == 'none' ) {
                $('#'+nname).show("slow");
                set_menu_state( nname, "0" );
        } else {
                $('#'+nname).hide("slow");
                set_menu_state( nname, "1" );
        }
}
function set_menu_state( item, state )
{
  expires = new Date();
  expires.setTime(expires.getTime()+24*60*60*1000*365*3);
  document.cookie = item+"="+state+";expires="+expires.toGMTString();
}
function flip_arrow( item )
{
        //window.alert(item.src.lastIndexOf( 'item_a.gif' ));
        //window.alert(item.src.lastIndexOf( 'arrow_down.gif' ));
        if ( item.src.lastIndexOf( 'item_a.gif' ) != -1 )
                item.src = '/admin/images/design/item_n.gif';
        else
                item.src = '/admin/images/design/item_a.gif';
}
//// END


// 0 state is collapsed
// 1 state is oppen
function get_menu_state( item )
{
  var cookieFound = false;
  var start = 0;
  var end = 0;
  var cookieString = document.cookie;
  var i = 0;
  // SCAN THE COOKIE FOR name
  while (i <= cookieString.length) {
    start = i;
    end = start + item.length;
    if (cookieString.substring(start,end) == item) {
      cookieFound = true;
      break;
    }
    i++;
  }
 if (cookieFound) {
    start = end + 1;
    end = document.cookie.indexOf(";",start);
    if (end < start)
      end = document.cookie.length;
    return document.cookie.substring(start,end);
  }
  return "1";
}

function MM_findObj(n, d) { //v4.01
 var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
  d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);
 }
 if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
 for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
 if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
 var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
 if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
function MM_swapImgRestore() { //v3.0
 var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
 var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
 var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
 if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function goToCategory(loc){
 location=loc + '&fltr='+ document.fltr.id_category.options[document.fltr.id_category.selectedIndex].value+'';
 //window.alert(location);
}


function previewImage( Form, list, image, base_path, server_path ) {
        form = eval( "document." + Form );
        srcList = eval( "form." + list );
        srcImage = eval( "document." + image );
        srcURL =  eval( "form." + "img_url" );
        var fileName = srcList.options[srcList.selectedIndex].text;
        var fileName2 = srcList.options[srcList.selectedIndex].value;
        if (fileName.length == 0 || fileName2.length == 0) {
                srcImage.src = server_path + '/images/blank.png';
                srcURL.value = srcImage.src;
        } else {
                srcImage.src = base_path + fileName2;
                srcURL.value = srcImage.src;
        }
}

function preload(image)
{var d=document; if(!d.wb_pre) d.wb_pre=new Array();
var l=d.wb_pre.length; d.wb_pre[l]=new Image; d.wb_pre[l].src=image;
}

function CheckCatalogPosition(t, val, txt){
//alert('t='+t+' val='+val+' txt='+txt);
var myArray = val.split('='); 
 if(myArray[0]=="categ"){
  alert(txt); 
  t.selectedIndex = 0;
 }
}


function makesure() {
 if (confirm('Вы действительно хотите удалить эту запись?')) {
  return true;
 } else {
  return false;
 }
}        

function flexToggle(el) {
  if (el.className == 'flexOpen') {
    el.className = 'flexShut';
  } else {
    el.className = 'flexOpen';
  }
}

function ge()
{
  var ea;
  for( var i = 0; i < arguments.length; i++ ) {
    var e = arguments[i];
    if( typeof e == 'string' )
      e = document.getElementById(e);
    if( arguments.length == 1 )
      return e;
    if( !ea )
      ea = new Array();
    ea[ea.length] = e;
  }
  return ea;
}

function show()
{
  for( var i = 0; i < arguments.length; i++ ) {
    var element = ge(arguments[i]);
    if (element && element.style) element.style.display = 'block';
  }
}

function show2()
{
  for( var i = 0; i < arguments.length; i++ ) {
    var element = ge(arguments[i]);
       if (element && element.style) element.style.display = "inline";
  }
  return false;
}

function hide()
{
  for( var i = 0; i < arguments.length; i++ ) {
    var element = ge(arguments[i]);
    if (element && element.style) element.style.display = 'none';
  }
}

function shown(el) {
    el = ge(el);
    return (el.style.display != 'none');
}

function shide(el) {if (shown(el)) {hide(el);} else {show(el);}}

function textLimit(ta, count) {
  var text = ge(ta);
  if(text.value.length > count) {
    text.value = text.value.substring(0,count);
    if(arguments.length>2) { // id of an error block is defined
      ge(arguments[2]).style.display='block';
    }
  }
}

function isIE() {
 return (navigator.userAgent.toLowerCase().indexOf("msie") != -1);
}   

function placeholderSetup(id) {
    var el = ge(id);
    if(!el) return;
    if(el.type != 'text') return;
    if(el.type != 'text') return;
    
    var ph = el.getAttribute("placeholder");
    if( ph && ph != "" ) {
        el.value = ph;
        el.style.color = '#777';
        el.is_focused = 0;
        el.onfocus = placeholderFocus;
        el.onblur = placeholderBlur;
    }
}

function placeholderFocus() {
  if(!this.is_focused) {
    this.is_focused = 1;
    this.value = '';
    this.style.color = '#000';

    var rs = this.getAttribute("radioselect");
    if( rs && rs != "" ) {
      var re = document.getElementById(rs);
      if(!re) { return; }
      if(re.type != 'radio') return;

      re.checked=true;
    }
  }
}

function placeholderBlur() {
  var ph = this.getAttribute("placeholder")
  if( this.is_focused && ph && this.value == "" ) {
        this.is_focused = 0;
    this.value = ph;
    this.style.color = '#777';
  }
}


function doAct(t){
 ge('actForm').submit();
}

function checkAll(rNum){
 ge("cAll").checked = true; ge("cAll").value = 1;
 for(i=0;i<rNum;i++){ge('check'+i).checked = 1;}
}

function unCheckAll(rNum){
 ge("cAll").checked = false; ge("cAll").value = 0;
 for(i=0;i<rNum;i++){ge('check'+i).checked = 0;}
}

function checkRead(Num){
 ge("cAll").checked = false; ge("cAll").value = 0;
 for(i=0;i<Num;i++){ge('1msg'+i).checked = 1;}
}
function unCheckRead(Num){
 ge("cAll").checked = false; ge("cAll").value = 0;
 for(i=0;i<Num;i++){ge('1msg'+i).checked = 0;}
}

function checkNew(Num){
 ge("cAll").checked = false; ge("cAll").value = 0;
 for(i=0;i<Num;i++){ge('0msg'+i).checked = 1;}
}

function unCheckNew(Num){
 ge("cAll").checked = false; ge("cAll").value = 0;
 for(i=0;i<Num;i++){ge('0msg'+i).checked = 0;}
}
$(document).ready(function(){  
    $(".TR1").hover(function() {
        $(this).css('background-color', '#d2e0f8');
        }, function() {
             $(this).css('background-color', '#ffffff');
    });
    $(".TR2").hover(function() {
        $(this).css('background-color', '#d2e0f8');
        }, function() {
        $(this).css('background-color', '#e5f2ff');
    });
    $("a.tip").easyTooltip({xOffset:10,yOffset:-5 });
    $("#mainUploadedF").tableDnD({onDragClass: "myDragClass"});
      
    //var h=$(".menu").height();
    //var h1=$(".Content").height();
    //alert("h="+h+" h1="+h1);
    //if (h>h1) $(".Content").height(h+"px");
 });