/**
 * animeOnScrollDown
 * 
 * @author Elias da Rosa
 * @url http://www.eliasdarosa.com.br/blog
 * @email elias@eliasdarosa.com.br
 * 
 */
(function($){
    $.fn.animeOnScrollDown = function(op){
   
        return this.each(function(){
   
            var obj = $(this);
    
            op = $.extend({
                x: {
                    position: null,
                    distance: 0,
                    aceleration: 0
                },
                y: {
                    position: null,
                    distance: 0,
                    aceleration: 0
                },
                opacity: {
                    active: null,
                    aceleration: 0
                }
        
            }, op);
   
            // porcentagem
            var calculePorcentage = function(aceleration){

                var e = $(document)[0].documentElement;
                var scrollTop = $(window).scrollTop();
                var i = (aceleration + (scrollTop / (e.scrollHeight - e.offsetHeight)));
            
                i = (i > 1) ? 1 : i;
                i = (i < 0) ? 0 : i;          
            
                return i;
            };

            var calculePosition = function(element) {
                return Math.round((element.distance * calculePorcentage(element.aceleration)) + (element.position));
            }; 
   
            $(window).on('load scroll resize', function() {

                // setPostion
                if(obj.css('position') == 'static'){
                    obj.css('position', 'relative');
                }
            
                // anime X
                if(op.x.position != null){
                    obj.css('left', calculePosition(op.x));      
                }

                // anime Y
                if(op.y.position != null){
                    obj.css('top', calculePosition(op.y));      
                }

                // anime Y
                if(op.opacity.active){
                    obj.css('opacity', calculePorcentage(op.opacity.aceleration));      
                }
        
            });
        });
    };
})(jQuery);