(function($){

	
	$(window).load(function(){



		//if( $('.acf-field-flexible-content').length ){

			var button = '<li class="acf-ft-save-wrap">';
				button += '<input type="text" class="acf-ft-template-name" value="" placeholder="'+acfft.tpl_name+'">';
					button += '<a href="#save_template" class="acf-ft-save acf-button button button-secondary">'+acfft.tpl_save+'</a>';
					button += '<div class="acf-ft-save-error"></div>';
				button += '</li>';

			$('.acf-field-flexible-content .values').next().prepend( button );



			$('.acf-ft-save').live('click', function(e){
				e.preventDefault();
				
				var flex_cont = $(this).parents('.acf-flexible-content');

				var template = flex_cont.find('.values').html();
				template = '<div class="values">'+template+'</div>';

				//flex_cont.find('.values').remove();

				acf_ft_save( this, template );
			});


			function acf_ft_save( self, template ){

				$('.acf-ft-save-error').text( '' ).hide();

				var template_name = $('.acf-ft-template-name').val();

				//console.log( $('.acf-ft-template-name').val() );

				if( template_name != '' ){
					var json = {
						action: 'ajax_template_save',
						name: template_name,
						template: escape(template)
					};
					$.ajax({
						url: acfft.ajaxurl,
		                type: 'POST',
		                data: json,

						success: function( data ) {
							if( data != "ok" ){
								
								$('.acf-ft-save-error').text( acfft[data] ).show();

							}else{
								var nr = $('.acfft_flexible_templates .acfft-option').length + 1;
								var btn = '<div class="acfft-option" data-value="'+template_name+'"><span class="acfft-select" data-value="'+template_name+'">'+nr+' - '+template_name+'</span><span class="acfft-remove" data-value="'+template_name+'">&nbsp;</span></div>';

								$('.acfft_flexible_templates .acfft-dropdown').append( btn );

								$('.acf-ft-save-error').text( acfft.saved ).addClass('saved').show();

								setTimeout(function(){
									$('.acf-ft-save-error').text( '' ).removeClass('saved').hide();
								}, 3000);
							}
						}
		            });
				}else{
					$('.acf-ft-save-error').text( acfft.no_name ).show();
				}
				/*
				setTimeout(function(){
					$( template ).insertBefore( $(self).parents('.acf-flexible-content').find('.acf-actions') );
				}, 1000);

				setTimeout(function(){
					$('#mceu_146').remove();

					$('.acf-flexible-content .wp-editor-area').each(function(){
						var id = '#'+ $(this).attr('id');
					
						console.log(id);
					
						tinymce.remove();
						tinymce.init({selector: id, id: id, elements: id});

						tinymce.init({selector: id});
						
					});

						


				}, 2000);
				*/




			}

			$('.acfft-remove').live('click', function(){

				var self = this;
				var template_name = $(this).data('value');

				if( template_name !== '' ){
					var json = {
						action: 'ajax_template_remove',
						name: template_name
					};
					$.ajax({
						url: acfft.ajaxurl,
		                type: 'POST',
		                data: json,

						success: function( data ) {
							if( data == "not ok" ){
								
								
							}else{
								
								$('.acfft-option[data-value="'+template_name+'"]').slideUp(400, function(){ $(this).remove(); });
							}
						}
		            });
				}
			});


			$('.acfft-select').live('click', function(){
				var self = this;
				var template_name = $(this).data('value');

				var flex_cont = $(this).parents('.acf-field-flexible-content');
				flex_cont.find('.values').remove();

				console.log('value', template_name);

				if( template_name !== '' ){
					var json = {
						action: 'ajax_template_load',
						name: template_name
					};
					$.ajax({
						url: acfft.ajaxurl,
		                type: 'POST',
		                data: json,

						success: function( data ) {
							if( data == "not ok" ){
								
								
							}else{
								data = unescape(data);
								console.log( 'data', unescape(data) );
								
								$( data ).insertBefore( $(self).parents('.acf-field-flexible-content').find('.acf-actions') );

								var post_status = $('#hidden_post_status').val();

								switch( post_status ){
									case 'publish':
										//$('#post_status').val('draft');
										$('#publish').click();
									break;

									default:
										$('#save-post').click();
									break;
								}	

								

							}
						}
		            });
				}
			});


			$('.acf-input-wrap input').on('change', function(){
				var value = $(this).val();
				$(this).attr('value', value);
			});


			setInterval(function(){

				
				$('.acf-flexible-content .wp-editor-area').each(function(){
					var id = $(this).attr('id');
					
					// Adding events to content and excerpt
					if ( typeof tinyMCE !== 'undefined' && tinyMCE.get( id ) !== null ) {
						//if(!tinymce_loaded){
							tinyMCE.get( id ).on( 'keyup', tinymceUpdateContent( id ) );
							//tinymce_loaded = true;
						//}

					}

					//if(!raw_editor_loaded){
						$('#'+id).bind( 'keyup', tinymceUpdateContent( id ) );
						//raw_editor_loaded = true;
					//}
				});

			}, 800);

			function tinymceUpdateContent( id ){
				var editor_content = '';

				if ( tinyMCE.get( id ) && typeof tinyMCE !== 'undefined' && tinyMCE.get( id ) !== null ) {
					editor_content = tinyMCE.get( id ).getContent();
					$('#'+id).val( editor_content ).text( editor_content );
					
				}else{
					editor_content = $('#'+id).val();
					$('#'+id).text( editor_content );
				}

				//console.log( 'val', id, $('#'+id).val() );
				//console.log( 'txt', id, $('#'+id).text() );
			}
			
		//}

	});


})(jQuery);
