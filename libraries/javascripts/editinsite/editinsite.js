(function($){
    $.fn.editinsite = function(){
    
        $(this).each(function(){
            var $span = $(this);
            var $area = $span.parent();
            var file = $span.attr('editinsite-file');
            var tipo = $span.attr('editinsite-tipo');
            var url = $span.attr('editinsite-url');
            
            if($area.css('position') != 'relative' || $area.css('position') != 'absolute'){
                
                if($area.text() == ''){
                    $area.prepend('{NULL}');
                }
                
                $area.css('position', 'relative');
                $span.css({
                    'display': 'block',
                    'position' : 'absolute',
                    'text-align' : 'center',
                    'top' : 0,
                    'left' : 0,
                    'width' : $area.css('width'),
                    'height' : $area.css('height'),
                    'line-height' : $area.css('height'),
                    'background' : '#00FFFF',
                    'opacity' : .1,
                    'font-weight' : 'bold',
                    'border' : '1px solid #F00',
                    'color' : '#666666'
                });
                
                $span.mouseenter(function(){
                    $(this).css({ 'opacity' : 1 });
                    $(this).html('EDITAR');
                });
                
                $span.mouseleave(function(){
                    $(this).css({ 'opacity' : .1 });
                    $(this).html('');                
                });

                $span.click(function(){
                    
                    var w = 600;
                    if(tipo == 'html')
                        w = 750;
                        
                    $.ajaxBox(url, {
                        closeBg : false,
                        width: w,
                        data: { 'file': file, 'tipo': tipo },
                        callback: function(){
                            
                            $('form', '#ajaxBox').validaForm({
                                upload: true,
                                success: function(retorno){
                                    //var json = eval('('+ retorno +')');
                                    window.location.reload();
                                }
                            });
                        }
                    });
                });

            }
            
        });

    };
})(jQuery);

$(function(){
    $('span.editinsite').editinsite();
});