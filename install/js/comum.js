$(function(){
	
	$('form.validaForm').validaForm({
		upload: true,
        success: function(a){           
            if(a == '')
                window.location = 'install.php';
            else
                alert(a);
        },
        callstart: function(){        
            $('.sub').hide();   
            $('.instalando-msg').show();        
        },		
        callback: function(){        
            $('.sub').show();   
            $('.instalando-msg').hide();        
        }		
	});
});
