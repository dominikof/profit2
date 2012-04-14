$(document).ready(function(){

    /*Галерея  зображень, прокрутка*/ 
    $("#carousel").jcarousel({
        scroll: 5,
        auto: 0,
        wrap: "last"
    });
    $("#carousel").removeClass("vhidden");

 });

// Ф-ыя вивиоду зображення в детальному перегляді галереї. 
function showImage (path, path_org, alt, title) {
      $("#imageLarge").html( '<a href="'+path_org+'" class="highslide" onclick="return hs.expand(this);"><img align="middle" src="'+path+'" alt="'+alt+'" title="'+title+'"/></a>' );
}