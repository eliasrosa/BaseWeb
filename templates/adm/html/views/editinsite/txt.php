<? defined('BW') or die("Acesso negado!"); ?>
<textarea name="conteudo" class="text"><?= $conteudo; ?></textarea>

<script type="text/javascript">
    $(function(){
        $('textarea.text', '#ajaxBox').keyup(function(e){
            var msg = $(this).val($(this).val().replace(/\n/g, ''));
        });
    });
</script>
