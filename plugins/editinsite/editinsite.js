(function($){
    
    $.editinsite = function(){
    
        $('.editinsite').each(function(){
            
            var $span = $(this);
            var $area = $span.parent();
            var ext = $span.attr('data-ext');
            
            if($area.css('position') != 'relative' || $area.css('position') != 'absolute'){
                
                if($area.text() == ''){
                    $area.height($('body').css('line-height'));
                }
                
                $area.css('position', 'relative');
                
                $span.css({
                    'display': 'block',
                    'cursor': 'pointer',
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
                    $(this).css('opacity', 1);
                    $(this).html('EDITAR');  
                });
                
                $span.mouseleave(function(){
                    $(this).css('opacity', 0.1);
                    $(this).html('');                
                });

                if(ext == 'txt'){
                    var iw = 500;
                    var ih = 250;
                }

                if(ext == 'html'){
                    var iw = 750;
                    var ih = 350;
                }
                    
                $span.prettyPhoto({
                    default_width: iw,
                    default_height: ih,
                    social_tools: false,
                    allow_resize: false,
                    show_title: false,
                    modal: true,
                    theme: 'light_rounded',
                    markup: '<div class="pp_pic_holder" id="editinsite"> \
						<div class="ppt">&nbsp;</div> \
						<div class="pp_top"> \
							<div class="pp_left"></div> \
							<div class="pp_middle"></div> \
							<div class="pp_right"></div> \
						</div> \
						<div class="pp_content_container"> \
							<div class="pp_left"> \
							<div class="pp_right"> \
								<div class="pp_content"> \
									<div class="pp_loaderIcon"></div> \
									<div class="pp_fade"> \
										<a href="#" class="pp_expand" title="Expand the image">Expand</a> \
										<div class="pp_hoverContainer"> \
											<a class="pp_next" href="#">next</a> \
											<a class="pp_previous" href="#">previous</a> \
										</div> \
										<div id="pp_full_res"></div> \
										<div class="pp_details"> \
											<div class="pp_nav"> \
												<a href="#" class="pp_arrow_previous">Previous</a> \
												<p class="currentTextHolder">0/0</p> \
												<a href="#" class="pp_arrow_next">Next</a> \
											</div> \
											<p class="pp_description"></p> \
											{pp_social} \
											<a class="pp_save" href="#" style="width: auto; background: none; display: inline; text-indent: 0; color: #999; font-size: 14px;">Salvar</a> \
											<a class="pp_close" href="#" style="width: auto; background: none; display: inline; text-indent: 0; color: #999; font-size: 14px;">Fechar</a> \
										</div> \
									</div> \
								</div> \
							</div> \
							</div> \
						</div> \
						<div class="pp_bottom"> \
							<div class="pp_left"></div> \
							<div class="pp_middle"></div> \
							<div class="pp_right"></div> \
						</div> \
					</div> \
					<div class="pp_overlay"></div>',
                    callback: function(){
                        window.location.reload();
                    },
                    changepicturecallback: function(){
                        if(ext == 'html'){
                            $('#editinsite form textarea').wysiwyg();
                        }
                    }
                });
                
                

            }
            
        });

    };
    
    
})(jQuery);

$(function(){
    
    $('#editinsite .pp_save').live('click', function(){    
        
        var form = $('#editinsite form');
        var btn_save = $(this);
        var btn_close = btn_save.parent().children('.pp_close');
        var bg = $('<div>Aguarde, salvando...</div>');
        
        // hide btns
        btn_save.fadeOut('slow');
        btn_close.fadeOut('slow');
        
        //
        bg.css({
            fontWeight: 'bold',
            position: 'absolute',
            left: 0,
            top: 0,
            font: '13px Tahoma',
            background: '#FFFFFF',
            textAlign: 'center',
            height: $('#editinsite').height() -80,
            width: $('#editinsite').width(),
            opacity: 0.7,
            color: '#FF0000'
        }).prependTo(form);
        
        
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            success: function(retorno){
                
                bg.html(retorno);
                setTimeout(function(){
                    bg.fadeOut('slow', function(){
        
                        //
                        btn_save.fadeIn('slow');
                        btn_close.fadeIn('slow');
                        
                        bg.remove();
                    });
                }, 1000);
                
                
            }
        });
        
        return false;
    });
    
    $.editinsite();
});