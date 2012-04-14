function preload(image)
{var d=document; if(!d.wb_pre) d.wb_pre=new Array();
var l=d.wb_pre.length; d.wb_pre[l]=new Image; d.wb_pre[l].src=image;
}

function over_on(n,ovr)
{var d=document,x; x=d[n];if (!(x) && d.all) x=d.all[n];
if (x){        document.wb_image=x; document.wb_normal=x.src; x.src=ovr; }}

function over_off()
{var x=document.wb_image; if (document.wb_normal) x.src=document.wb_normal;}
