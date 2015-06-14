/**
 *	@plugin 	Multi Select Auto Complete
 *	@data 		March 5 2012
 *	@author	Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/ 
(function( $, undefined ){

	$.fn.msAutoComplete = function(options){
		return this.each(function(){
			// Settings and local cache
			var self = this, 
				$main = $( self ), 
				settings = $.extend({
					source: [],
					minLength: 1,
					element : '.msauto-complete',
					display: '.display',
					form: '.form',
					loadtxt: 'Loading...',
					name: 'mstext'
				}, options || {}, $.metadata ? $main.metadata() : {} ),

				// Cache All Selectors
				$display = $main.find( settings.display ),
				$form = $main.find( settings.form ),
				$element = $main.find( settings.element );

			// Make sure the plugin only get initialized once
			if ( $.data( self, 'msauto-complete' ) === true ) {
				return;
			}
			$.data( self, 'msauto-complete', true );
			
			try {
				$element.autocomplete({ 
					source : settings.source, 
					minLength: settings.minLength,
					
					//prevent value inserted on focus
					focus: function() { 
						return false; 
					},
					
					select: function(event, ui){
						var inputelement = $('<input type="hidden" name="'+ settings.postVar +'" value="'+ ui.item.value +'"/>').appendTo($form);
						
						$('<input type="button" class="button msac-select">').attr('value', ui.item.value).bind('click', function(){
							inputelement.remove();
							$(this).remove();
							return false;
						}).appendTo($display);
						
						this.value = '';
						return false;
					}
				});
			} catch(e) {
				if(console.log || false){
					console.log(e);
					return;
				}
			}
		});
	};

})( jQuery );
