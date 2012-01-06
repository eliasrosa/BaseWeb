(function($) {
    $.ajaxBox = function(box_url, params){
        var options = {
            width: 600,
            closeBg : true,
            callback: function(){},
            data: {},
            bgcolor: '#FFF',
            border: '5px solid #EEE'
        };

        var op = $.extend(options, params);

        $('body').css({position: ''})
            .append('<div id="ajaxBoxBg"></div>')
            .append('<div id="ajaxBox"></div>');

        /* Selectors */
        var $bg   = $('#ajaxBoxBg');
        var $box  = $('#ajaxBox');

        $bg.css({
            'height': $(document).height(),
            'width': '100%',
            'cursor': 'pointer',
            'position': 'absolute',
            'background-color': '#000',
            'top' : 0,
            'left': 0,
            'z-index': 555 
        }).fadeTo('fast', 0.5);

        $box.css({
            'width': op.width,
            'display': 'block',
            'position': 'absolute',
            'background-color': op.bgcolor,
            'border': op.border,
            'padding': 20,
            'z-index': 999
        }).load(box_url, op.data, function() {
            $.ajaxBox.center();
            $box.fadeTo('fast', 1);
            op.callback();
        });

        if(op.closeBg){
            $bg.click(function(){
                $.ajaxBox.Fechar();
            });
        }
    }

    $.ajaxBox.Fechar = function() {
        $('#ajaxBoxBg').fadeOut('fast', function() { $(this).remove(); });
        $('#ajaxBox').fadeOut('fast', function() { $(this).remove(); });
    };

    $.ajaxBox.center = function(){
        $box = $('#ajaxBox');
        $box.css({
            'top' : ($(document).scrollTop() + (($(window).height() - $box.height()) / 2)) - 5,
            'left': (($(document).width() - $box.width() ) / 2) - 5
        });
    };
})(jQuery);
