$(function(){
	$('textarea.autoresize').autoResize({
		onResize: function(){},
		animateCallback: function(){},
		animate: true,
		animateDuration: 150,
		extraSpace: 20,
		limit: 999999,
	}).trigger('change');	
});
