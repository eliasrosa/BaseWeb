$(function() {

    var log = $('#log');
    var keysafe = $('#imagens').attr('data-keysafe')
    
    
    //
    $('html, body').scrollTop(0);

    //
    $('#upload .toolbar .limpar-avisos').click(function(){
        $('#upload .aviso.ok').fadeOut(function(){
            $(this).remove();
        });
    });



    //
    $('#upload .toolbar .limpar-erros').click(function(){
        $('#upload .aviso.erro').fadeOut(function(){
            $(this).remove();
        });
    });



    //
    $('#upload .toolbar .limpar-todos').click(function(){
        $('#upload .aviso').fadeOut(function(){
            $(this).remove();
        });
    });
    
    
    
    
    
    // sortable 
    $('#imagens form').sortable({
        itens: '.files',
        placeholder: 'ui-state-highlight',
        forcePlaceholderSize: true,
        update: function(){
            var html = addAviso('Álbum', "Atualizado ordem da imagens", 'ok');
            $.getJSON('ordem', $('#imagens form').serialize(), function(json){
                $('p', html).html(json.msg);
            });
        }
    });



    //
    var scrollTo = function($i){
        var container = $('html, body');
        var scroll = $i.offset().top;
        container.scrollTop(scroll);
    };


    //
    var createImagem = function(i, scroll){
        var html  = $('<div class="file"></div>');
        html.attr('style', 'background: url(\''+i.url+'\') no-repeat center top;');
        html.attr('data-id', i.id);
        html.append('<input type="hidden" value="'+i.id+'" name="imagens[]" />');
        html.append('<div class="bg"></div>');
        html.append('<img class="editar" src="../../media/image_edit.png" width="18" href="imagem?id='+i.id+'&ajax=true" title="Editar" />');        
        html.append('<img class="remover" src="../../media/image_remove.png" width="18" title="Remover" />');        
        
        html.hide();
                
        $('#imagens form').append(html);
        
        if($('#imagens form .file').length == 10){
            $("#upload").scrollFixed();
        }
        
        $(html).fadeIn('slow');
        
        if(scroll == true){
            scrollTo(html);
        }
        
        $('.editar', html).prettyPhoto({
            default_width: 750,
            default_height: 395,
            social_tools: false,
            allow_resize: false,
            show_title: false,
            modal: true,
            markup: '<div class="pp_pic_holder" id="editbox"> \
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
            changepicturecallback: function(){

            }
        });
        
        
        $('.remover', html).click(function(){
            
            if(confirm('Tem certeza que deseja remover essa immagem?')){
                $.getJSON('remover', 'id='+i.id, function(json) {
                    html.fadeOut(function(){
                        $(this).remove();
                    });
                    addAviso('Imagem #'+json.id, json.msg, 'ok');
                });
            }else{
                return false;
            }
        });
        
        
        return html;
        
    }



    //
    var addAviso = function(titulo, msg, tipo){
        var html  = $('<div class="aviso '+ tipo +'"></div>');
        html.append('<h3>'+titulo+'</h3>');
        html.append('<p>'+msg+'</p>');
        log.prepend(html);
        
        return html;
    }



    //
    $.getJSON('imagens', 'keysafe='+keysafe, function(imagens) {
        
        addAviso('Álbum', 'Carregando <span></span> de '+ imagens.length +' imagens', 'ok album');
        
        var time = 0;
        $.each(imagens, function(k, i){
            
            setTimeout(function(){
                createImagem(i, false);
                $('#log .aviso.album span').html(k+1);
                
                if(k+1 == imagens.length){
                    addAviso('Álbum', 'Todas as imagens foram carregadas com sucesso', 'ok');
                    
                }
                
            }, time);
            
            time += 50;
            
        });
        
    });
        
        
        
    //
    $("#upload input").html5_upload({
        url: 'upload',
        sendBoundary: window.FormData || $.browser.mozilla,
        extraFields : {
            'keysafe': keysafe
        },
        onStart: function(event, total) {
            return true;
        },
        onProgress: function(event, progress, name, number, total) {
        //console.log(progress, number);
        },
        setName: function(text) {
            $("#upload .name").text(text);
        },
        setStatus: function(text) {
            $("#upload .status").text(text);
        },
        setProgress: function(val) {
            $("#upload .bar").show();
            $("#upload .bar div").css('width', Math.ceil(val*100)+"%");
        },
        onFinishOne: function(event, response, name) {
            var json = $.parseJSON(response);
            var tipo = 'ok';
            
            if(json.retorno){
                $.getJSON('imagens', 'id_imagem='+json.id, function(response) {
                    createImagem(response, true);
                });
            }else{
                tipo = 'erro';
            }
            
            addAviso('Arquivo: ' + name, json.msg, tipo);
                
        },
        onError: function(event, name, error) {
        // console.log('error while uploading file ' + name);
        }
    });
    
    
    
    $('#editbox form').live('submit', function(){
        $.getJSON('salvar', $(this).serialize(), function(json) {
            addAviso('Imagem #'+json.id, 'Os dados foram salvos com sucesso!', 'ok');
            $.prettyPhoto.close();
        });
        return false;
    });

    $('#editbox form .fechar').live('click', function(){
        $.prettyPhoto.close();
        return false;
    });

    $('#editbox form div.alt a').live('click', function(){
        var v = $('#editbox form div.title input').val();
        $('#editbox form div.alt input').val(v);
       
        return false;
    });
    
});
