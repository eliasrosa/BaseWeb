$(function(){
    jQuery.fn.autohide = function(defaultValue){
        var $t = $(this);
        
        if($t.val() == ''){
            $t.val(defaultValue);
            $t.attr('default-value', defaultValue);
        }
        
        $t.focus(function(){
            if($t.val() == defaultValue)
                $t.val('');
        })
        
        $t.blur(function(){
            if($t.val() == '')
                $t.val(defaultValue);
        });
    };
});
