$(function(){
	$('#usuarios-cadastro-completo form.tipo').validaForm();

    $form = $('#usuarios-cadastro-completo form.tipo');

    $('label input:radio', $form).change(function(){
        $form.submit();
    });
});



