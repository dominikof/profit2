
$(document).ready(function(){
    $('.fancybox').fancybox({
        padding:"7"
    });
    $('.fancybox-gallery').fancybox({
        padding:"7",
        closeBtn		: false,
        helpers		: {
            title	: {
                type : 'inside'
            },
            buttons	: {},
            thumbs	: {
                width	: 50,
                height	: 50
            }
        }
    });
});