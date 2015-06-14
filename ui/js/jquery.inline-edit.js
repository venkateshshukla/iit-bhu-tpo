/*
 * Inline Text Editing 1.3
 * April 26, 2010
 * Corey Hart @ http://www.codenothing.com
 */ 
(function( $, undefined ){

	$.fn.inlineEdit = function( options ) {
		return this.each(function(){
			// Settings and local cache
			var self = this, $main = $( self ), original,
				settings = $.extend({
					href: 'ajax.php',
					requestType: 'POST',
					html: true,
					load: undefined,
					edit : '.edit',
					display: '.display',
					form: '.form',
					text: '.text',
					save: '.save',
					cancel: '.cancel',
					revert: '.revert',
					loadtxt: 'Loading...',
					hover: undefined,
					postVar: 'text',
					postData: {},
					postFormat: undefined
				}, options || {}, $.metadata ? $main.metadata() : {} ),

				// Cache All Selectors
				$display = $main.find( settings.display ),
				$edit = $main.find( settings.edit ),
				$form = $main.find( settings.form ),
				$text = $form.find( settings.text ),
				$save = $form.find( settings.save ),
				$revert = $form.find( settings.revert ),
				$cancel = $form.find( settings.cancel );

			// Make sure the plugin only get initialized once
			if ( $.data( self, 'inline-edit' ) === true ) {
				return;
			}
			$.data( self, 'inline-edit', true );

			// Prevent sending form submission
			$form.bind( 'submit.inline-edit', function(){
				$save.trigger( 'click.inline-edit' );
				return false;
			});
	
			// Display Actions
			$display.bind( 'click.inline-edit', function(){
				$display.hide();
				$form.show();

				if ( settings.html ) {
					if ( original === undefined ) {
						original = $display.html();
					}

					$text.val( original ).focus();
				}
				else if ( original === undefined ) {
					original = $text.val();
				}

				return false;
			})
			/*.bind( 'mouseenter.inline-edit', function(){
				$display.addClass( settings.hover );
			})
			.bind( 'mouseleave.inline-edit', function(){
				$display.removeClass( settings.hover );
			})*/;
			
			//Add edit handler
			$edit.bind( 'click.inline-edit', function(){
				$display.hide();
				$form.show();

				if ( settings.html ) {
					if ( original === undefined ) {
						original = $display.html();
					}

					$text.val( original ).focus();
				}
				else if ( original === undefined ) {
					original = $text.val();
				}

				return false;
			});

			// Add revert handler
			$revert.bind( 'click.inline-edit', function(){
				$text.val( original || '' ).focus();
				return false;
			});

			// Cancel Actions
			$cancel.bind( 'click.inline-edit', function(){
				$form.hide();
				$display.show();

				// Remove hover action if stalled
				if ( $display.hasClass( settings.hover ) ) {
					$display.removeClass( settings.hover );
				}

				return false;
			});

			// Save Actions
			$save.bind( 'click.inline-edit', function( event ) {
				var newtext = $text.val();
				settings.postData = $form.serialize();
				$form.hide();
				$display.html( settings.loadtxt ).show();

				if ( $display.hasClass( settings.hover ) ) {
					$display.removeClass( settings.hover );
				}

				$.ajax({
					url: settings.href,
					type: settings.requestType,
					data: settings.postFormat ? 
						settings.postFormat.call( $main, event, { settings: settings, postData: settings.postData } ) :
						settings.postData,
					dataType : 'json',
					success: function( response ){
						if(response['valid'] || false){
							if ( settings.load ) {
								settings.load.call( $display, event, { response: response, settings: settings } );
							}
							$display.html( newtext );
							original = newtext;
							$edit.hide();
						}
						else {
							$display.hide();
							$form.show();
						}
					},
					error: function(event, data){
						$display.html(original);
					}
				});

				return false;
			});
		});
	};

})( jQuery );
