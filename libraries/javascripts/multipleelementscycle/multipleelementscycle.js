/**
 * Multiple Elements Cycle Plugin.
 *
 * Provides a simple preview scrolling panel. Shows the given range of li items from the middle and
 * allows scrolling left and right within the list. Does not handle automatic scrolling / time based
 * or wrapping items around. 
 *
 * Note $.multipleElementsCycle needs to be called on a div containing a ul rather then the actual ul

 * @author Will Rossiter <will.rossiter@gmail.com>
 * @version 0.5
 */
(function($) {
	$.fn.multipleElementsCycle = function(options){
		
		var defaults = {
			elementContainer: '#cycleElements',	// Selector for element (ul) container (selector)
			prevElement: '#cycleElementsLeft',	// Selector to scroll previous (selector)
			nextElement: '#cycleElementsRight', // Selector to scroll next (selector)
			speed: 500,							// Speed to scroll elements (int)
			containerSize: false,				// Override default size (int with size)
			showCount: 4,						// Items to show from the list (int)
			overrideStart: false,				// Override the start with a defined value (int)
			jumpTo: false,						// Selectors to use as jump list
			vertical: false						// Whether Scroll is for vertical
		};
		
		var options = $.extend(defaults, options);
				
		return this.each(function() {

			var totalElems = $(this).find("li");
			var maxIndex = totalElems.length - 1;
			
			// WORK OUT START INDEX
			var lowerIndex = (options.overrideStart === false) ? Math.floor((maxIndex - options.showCount + 1) / 2) : options.overrideStart;
			var elementSize = (options.vertical === false) ? $(this).find("li").outerWidth(true) : $(this).find("li").outerHeight(true);

			var margin = ((lowerIndex) * elementSize) * -1;
			var upperIndex = lowerIndex + options.showCount;
			var parent = $(this);
			
			// HIDE ARROWS IF NEEDED
			if(upperIndex >= totalElems.length) $(options.nextElement).hide();
			if(lowerIndex <= 0) $(options.prevElement).hide(); 
			
			if(options.vertical === false) {
				$(this).find(options.elementContainer).css({
					width: (options.containerSize) ? options.containerSize : elementSize * options.showCount,
					overflow: 'hidden'
				});
				$(this).find("ul").css({
					width: (maxIndex + 1) * elementSize,
					padding: '0'
				});
				
				$("ul",parent).animate({marginLeft: margin}, options.speed);
			}
			else {
				$(this).find(options.elementContainer).css({
					height: (options.containerSize) ? options.containerSize : elementSize * options.showCount,
					overflow: 'hidden'
				});
				$(this).find("ul").css({
					height: (maxIndex + 1) * elementSize,
					padding: '0'
				});
				
				$("ul",parent).animate({marginTop: margin}, options.speed);
			}
	
			var cycle = {
				next: function() {
					if(upperIndex <= maxIndex) {
						$(options.prevElement).show();
						margin = margin - elementSize;
						upperIndex = upperIndex + 1;
						lowerIndex = lowerIndex + 1;
						
						if(options.vertical === false)
							$("ul",parent).animate({marginLeft: margin},options.speed);
						else
							$("ul",parent).animate({marginTop: margin},options.speed);
							
						if(upperIndex > maxIndex) $(options.nextElement).hide();
					}
				},
				prev: function() {
					if(lowerIndex >= 0) {
						$(options.nextElement).show();	
						upperIndex = upperIndex - 1;
						lowerIndex = lowerIndex - 1;
						margin = margin + elementSize;
						
						if(options.vertical === false)
							$("ul",parent).animate({marginLeft: margin}, options.speed);
						else
							$("ul",parent).animate({marginTop: margin},options.speed);
							
						if((lowerIndex-1) < 0) $(options.prevElement).hide();
					}
				},
				toPoint: function(pos) {
					var oldUpper = upperIndex;
					if(pos == 0) {
						// jump to end
						upperIndex = maxIndex + 1;
						lowerIndex = upperIndex - options.showCount;
					}
					else if(pos < 0) {
						// offset from end
						upperIndex = maxIndex + parseInt(pos);
						lowerIndex = lowerIndex + parseInt(pos);
					}
					else {
						// offset from start
						lowerIndex = pos - 1;
						upperIndex = lowerIndex + options.showCount;
					}
					// if the upper index is 
					margin = margin + (elementSize * (oldUpper-upperIndex));
					
					if(options.vertical === false) 
						$("ul",parent).animate({marginLeft: margin},options.speed);
					else 
						$("ul",parent).animate({marginTop: margin},options.speed);
						
					if(upperIndex >= maxIndex) $(options.nextElement).hide();
					else $(options.nextElement).show();
					
					if(lowerIndex == 0) $(options.prevElement).hide();
					else $(options.prevElement).show();
				}
			}
				
			// CLICK
			$(options.nextElement).live('click', function(){ cycle.next(); return false; });
			$(options.prevElement).live('click', function(){ cycle.prev(); return false; });

			// JUMP
			if(options.jumpTo) {
				$(options.jumpTo).live('click', function() { cycle.toPoint($(this).attr('rel')); return false; });
			}
		});	
	};
})(jQuery);