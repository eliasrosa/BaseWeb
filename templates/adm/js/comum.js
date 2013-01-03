$(function(){
    
    
    
    /* cssfix
     **************************************/
    if($("#conteudo #submenu").length > 0) {
        $("#conteudo #submenu").clone().appendTo("#menu");
        $("#conteudo #submenu").remove();
    }
    
    
    
    /* cssfix - bwGrid
     **************************************/
    $('.bwGrid table tbody tr').mouseenter(function(){
        $(this).addClass('hover');
    }).mouseleave(function(){
        $(this).removeClass('hover');
    });
    
    
    
    /* menu
     **************************************/
    var w = 0;
    
    $("#menu .center ul.com li").each(function(){
        w += $(this).outerWidth();
    });
    
    $("#menu .center ul.com").css('width', w);
    
    var fixMenu = function(){
        
        $('#menu .prev').css({
            'left': $('#menu .center').offset().left - 24,
            'top': 10
        });

        $('#menu .next').css({
            'left': $('#menu .center').offset().left + 980 + 12,
            'top': 10
        });
        
        var x = $('#menu .center').scrollLeft();
        
        if(typeof(Storage)!=="undefined"){
            sessionStorage.bwadm_menu_x = x;
        }
        
        if(x > 0){
            $('#menu .prev').show();
        }else{
            $('#menu .prev').hide();
        }
        
        var w = $('#menu .center ul.com').outerWidth();
        
        if(w > 980){
            $('#menu .next').show();
        }
        
        if(x + 980 == w){
            $('#menu .next').hide();
        }
        
    }
    
    if(typeof(Storage)!== 'undefined'){
        if(sessionStorage.bwadm_menu_x != 'undefined'){
            $('#menu .center').scrollLeft(sessionStorage.bwadm_menu_x);
            fixMenu();
        }
    }
    
    fixMenu();
    
    $(window).resize(function(){
        fixMenu();
    });
    
    $('#menu .next').click(function(){
        $('#menu .center').animate({
            'scrollLeft': '+=250'
        }, '500', function(){
            fixMenu();
        });
        
        return false;
    });
    
    $('#menu .prev').click(function(){
        $('#menu .center').animate({
            'scrollLeft': '-=250'
        }, '500', function(){
            fixMenu();
        });
        
        return false;
    });
    
});
