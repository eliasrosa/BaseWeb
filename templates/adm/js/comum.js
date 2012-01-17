$(function(){   
    $('.bwGrid table tbody tr').mouseenter(function(){
        $(this).addClass('hover');
    }).mouseleave(function(){
         $(this).removeClass('hover');
    });
});
